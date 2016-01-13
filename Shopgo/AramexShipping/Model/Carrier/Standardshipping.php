<?php

namespace Shopgo\AramexShipping\Model\Carrier;

use Magento\Framework\Module\Dir;
use Magento\Quote\Model\Quote\Address\RateResult\Error;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrierOnline;
use Magento\Shipping\Model\Rate\Result;
use Magento\Framework\Xml\Security;


class Standardshipping extends AbstractCarrierOnline implements \Magento\Shipping\Model\Carrier\CarrierInterface
{
    const CODE = 'aramex';

    protected $_code = self::CODE;

    protected $_request = null;

    protected $_result = null;

    protected $_storeManager;

    protected $_productCollectionFactory;

    protected $_rateServiceWsdl;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Shopgo\AramexShipping\Helper\Logger\Logger $logger
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
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Module\Dir\Reader $configReader
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Shopgo\AramexShipping\Helper\Logger\Logger $logger,
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
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Dir\Reader $configReader,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        $this->_logger = $logger;
        $this->_storeManager = $storeManager;
        $this->_productCollectionFactory = $productCollectionFactory;
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
        $wsdlPath = $configReader->getModuleDir(Dir::MODULE_ETC_DIR, 'Shopgo_AramexShipping') . '/wsdl/';
        $this->_rateServiceWsdl = $wsdlPath . 'aramex-rates-calculator-wsdl.wsdl';
    }

    protected function _createSoapClient($wsdl, $trace = false)
    {
        $client = new \SoapClient($wsdl, ['trace' => $trace]);
        return $client;
    }

    protected function _createRateSoapClient()
    {
        return $this->_createSoapClient($this->_rateServiceWsdl);
    }

    public function collectRates(RateRequest $request)
    {
        if (!$this->canCollectRates()) {
            return $this->getErrorMessage();
        }

        $requestAramex = clone $request;
        $this->setRequest($requestAramex);

    }

    public function setRequest(\Magento\Framework\DataObject $request)
    {

        $this->_request = $request;
        $this->setStore($request->getStoreId());

        $r = new \Magento\Framework\DataObject();
        
        $r->setUserName($this->getConfigData('username'));
        $r->setPassword($this->getConfigData('password'));
        $r->setAccountNumber($this->getConfigData('accountnumber'));
        $r->setAccountEntity($this->getConfigData('accountentity'));
        $r->setAccountPin($this->getConfigData('accountpin'));
        $r->setAccountCountryCode($this->getConfigData('accountcountrycode'));

        $origCountry = $this->_scopeConfig->getValue(
                        \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_COUNTRY_ID,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $request->getStoreId()
                     );

        $r->setOrigCountry($this->_countryFactory->create()->load($origCountry)->getData('iso2_code'));

        $r->setOrigCity(
                $this->_scopeConfig->getValue(
                    \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_CITY,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $request->getStoreId()
                )
            );

        $destCountry = $request->getDestCountryId();

        $r->setDestCountry($this->_countryFactory->create()->load($destCountry)->getData('iso2_code'));
        $r->setDestCity("Amman");
        //$r->setDestCity($request->getDestCity());

        $r->setProductGroup('EXP');
        $r->setProductType($this->getConfigData('producttype'));

        
        $this->setRawRequest($r);

        return $this;
    }

    public function getRequestParam()
    {
        
    }

    public function getResult()
    {
        
    }

    public function getAllowedMethods()
    {

    }

    protected function _doShipmentRequest(\Magento\Framework\DataObject $request)
    {
        
    }

}
