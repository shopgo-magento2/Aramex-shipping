<?php

namespace ShopGo\AramexShipping\Model\Carrier;

use Magento\Framework\Module\Dir;
use Magento\Quote\Model\Quote\Address\RateResult\Error;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrierOnline;
use Magento\Shipping\Model\Rate\Result;
use Magento\Framework\Xml\Security;


class Standardshipping extends AbstractCarrierOnline implements \Magento\Shipping\Model\Carrier\CarrierInterface
{

    const CODE = 'aramex';

    /**
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * Rate request data
     *
     * @var RateRequest|null
     */
    protected $_request = null;

    /**
     * Rate result data
     *
     * @var Result|null
     */
    protected $_result = null;

    /**
     * @var string
     */
    protected $_rateServiceWsdl;

    protected $directoryHelper;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \ShopGo\AramexShipping\Helper\Logger\Logger $logger
     * @param Security $xmlSecurity
     * @param \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory
     * @param \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory
     * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Directory\Helper\Data $directoryData
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Framework\Module\Dir\Reader $configReader
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \ShopGo\AramexShipping\Helper\Logger\Logger $logger,
        Security $xmlSecurity,
        \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Module\Dir\Reader $configReader,
        \ShopGo\AramexShipping\Helper\Data $helper,
        array $data = []
    ) {
        $this->directoryHelper           = $directoryData;
        $this->_helper                   = $helper;
        $this->_logger                   = $logger;

        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $xmlSecurity,
            $xmlElFactory,
            $rateFactory,
            $rateMethodFactory,
            $trackFactory,
            $trackErrorFactory,
            $trackStatusFactory,
            $regionFactory,
            $countryFactory,
            $currencyFactory,
            $directoryData,
            $stockRegistry,
            $data
        );
    }

    /**
     * @param RateRequest $request
     * @return Result|bool|null
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->canCollectRates()){
            return $this->getErrorMessage();
        }

        $requestAramex = clone $request;
        $this->setRequest($requestAramex);

        $params         = $this->buildAramexReq();
        $aramexResponse = $this->sendAramexReq($params);

        $rate           = $this->_rateFactory->create();
        $resultQuote    = $this->_result;

        if($resultQuote == false){
            $aramexErrorMessage = $this->_helper->getServiceErrorMessages($aramexResponse->Notifications->Notification);
            return $this->failAramex($rate, $aramexErrorMessage);
        }else{
            return $this->addAramexRate($rate, $resultQuote);
        }
    }

    /**
     * Prepare and set request to this instance
     *
     * @param RateRequest $request
     * @return $this
     */
    public function setRequest(\Magento\Framework\DataObject $request)
    {

        $this->_request = $request;
        $this->setStore($request->getStoreId());

        $reqObject = new \Magento\Framework\DataObject();
        
        $reqObject->setUserName($this->getConfigData('username'));
        $reqObject->setPassword($this->getConfigData('password'));
        $reqObject->setAccountNumber($this->getConfigData('accountnumber'));
        $reqObject->setAccountEntity($this->getConfigData('accountentity'));
        $reqObject->setAccountPin($this->getConfigData('accountpin'));
        $reqObject->setAccountCountryCode($this->getConfigData('accountcountrycode'));

        $origCountry = $this->_scopeConfig->getValue(
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_COUNTRY_ID,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $request->getStoreId()
                     );

        $reqObject->setOrigCountry($this->_countryFactory->create()->load($origCountry)->getData('iso2_code'));

        $reqObject->setOrigCity(
                $this->_scopeConfig->getValue(
                    \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_CITY,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $request->getStoreId()
                )
            );

        $destCountry = $request->getDestCountryId();

        $reqObject->setDestCountry($this->_countryFactory->create()->load($destCountry)->getData('iso2_code'));

        $reqObject->setDestCity($request->getDestCity());
        $reqObject->setDestPostal($request->getDestPostcode());

        if ($reqObject->getDestCountry() == $reqObject->getOrigCountry()){
            $reqObject->setProductGroup('DOM');
            $reqObject->setProductType($this->getConfigData('domesticproducttype'));
        }
        else{
            $reqObject->setProductGroup('EXP');
            $reqObject->setProductType($this->getConfigData('producttype'));
        }

        $reqObject->setUnitOfMeasure($this->getConfigData('unit_of_measure'));
        
        $this->setRawRequest($reqObject);

        return $this;
    }

    /**
     * Prepare and set Aramex request array
     *
     * @return array
     */
    public function buildAramexReq()
    {

        $reqObject = $this->_rawRequest;

        $orderInfo = $this->getOrderInfo();

        $qty    = $orderInfo['qty'];
        $weight = $orderInfo['weight'];

        $params = [
            'ClientInfo'  => [
                'AccountCountryCode' => $reqObject->getAccountCountryCode(),
                'AccountEntity'      => $reqObject->getAccountEntity(),
                'AccountNumber'      => $reqObject->getAccountNumber(),
                'AccountPin'         => $reqObject->getAccountPin(),
                'UserName'           => $reqObject->getUserName(),
                'Password'           => $reqObject->getPassword(),
                'Version'            => 'v1.0'
            ],
                                    
            'Transaction' => [
                'Reference1' => '001'
            ],
                                    
            'OriginAddress' => [
                'Line1'        => 'Originstreet',
                'City'         => $reqObject->getOrigCity(),
                'CountryCode'  => $reqObject->getOrigCountry(),
            ],
                                    
            'DestinationAddress' => [
                'Line1'        => 'DestinationStreet',
                'City'         => $reqObject->getDestCity(),
                'CountryCode'  => $reqObject->getDestCountry(),
                'PostCode'     => $reqObject->getDestPostal(),
            ],
            'ShipmentDetails' => [
                'PaymentType'       => 'P',
                'ProductGroup'      => $reqObject->getProductGroup(),
                'ProductType'       => $reqObject->getProductType(),
                'ActualWeight'      => array('Value' => $weight, 'Unit' => $reqObject->getUnitOfMeasure()),
                'ChargeableWeight'  => array('Value' => $weight, 'Unit' => $reqObject->getUnitOfMeasure()),
                'NumberOfPieces'    => $qty
            ]
        ];
            return $params;
    }

    /**
     * Send Aramex Rate Calculation request
     * @param Array $param Aramex Request Array
     * @return array
     */
    public function sendAramexReq($params)
    {

        if ($this->_helper->getDebugStatus()){
            $this->_logger->info(print_r($params,true));
        }

        $client  = $this->_helper->createRateSoapClient();
        $results = $client->CalculateRate($params);

        if ($this->_helper->getDebugStatus()){
            $this->_logger->info(print_r($results,true));
        }

        if($results->HasErrors){
            $this->_result = false;
        }
        else{
            $this->_result = $results->TotalAmount;
        }
        return $results;
    }

    /**
     * Get cart items details "weight and number of pieces"
     *
     * @return $array
     */
    public function getOrderInfo()
    {
        $request = $this->_request;

        $qty     = $request->getPackageQty();
        $weight  = $request->getPackageWeight();

        return array('qty' => $qty, 'weight' => $weight);
    }

   /**
     * Create failed carrier
     *
     * @param  ResultFactory $result
     * @param  string $aramexErrorMessage
     * @return Result|bool|null
     */
    public function failAramex($result, $aramexErrorMessage)
    {
        $error = $this->_rateErrorFactory->create();

        $error->setCarrier($this->_code);
        $error->setCarrierTitle($this->getConfigData('title'));

        if (($this->getConfigData('showaramexerror')) && ($this->_helper->getDebugStatus())){
            $error->setErrorMessage($aramexErrorMessage);
        }
        else{
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
        }
        $result->append($error);

        return $result;
    }

    /**
     * Create Aramex carrier
     *
     * @param ResultFactory $result
     * @param Result        $resultQuote
     * @return Result|bool|null
     */
    public function addAramexRate($result, $resultQuote)
    {
         $rate  = $this->_rateMethodFactory->create();
         $price = $resultQuote->Value;

         $rate->setCarrier($this->_code);
         $rate->setMethod($this->_code);

         $rate->setMethodTitle($this->getConfigData('method_title'));
         $rate->setCarrierTitle($this->getConfigData('title'));

         $aramexCurrency = $resultQuote->CurrencyCode;
         $storeCurrency  = $this->directoryHelper->getBaseCurrencyCode();

         $currency = $this->_currencyFactory->create();
         $rates    = $currency->getCurrencyRates($storeCurrency, [$aramexCurrency]);

         $price    = round($price/$rates[$aramexCurrency]);
        
         $rate->setCost($price);
         $rate->setPrice($price);
         $result->append($rate);

         return $result;
     }

    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('title')];
    }

    protected function _doShipmentRequest(\Magento\Framework\DataObject $request)
    {
        
    }

}
