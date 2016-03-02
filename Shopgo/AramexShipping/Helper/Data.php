<?php

namespace ShopGo\AramexShipping\Helper;

use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Module\Dir;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const XML_PATH_DEBUG       = 'carriers/aramex/debug';
    const XML_PATH_USERNAME    = 'carriers/aramex/username';
    const XML_PATH_PASSWORD    = 'carriers/aramex/password';
    const XML_PATH_ACCOUNTENT  = 'carriers/aramex/accountentity';
    const XML_PATH_ACCOUNTPIN  = 'carriers/aramex/accountpin';
    const XML_PATH_ACCOUNTNUM  = 'carriers/aramex/accountnumber';
    const XML_PATH_ACCOUNTCC   = 'carriers/aramex/accountcountrycode';
    const XML_PATH_PRODUCTYPE  = 'carriers/aramex/producttype';

    protected $_rateServiceWsdl;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Reader $configReader
        )
    {    
        parent::__construct($context, $scopeConfig);

        $wsdlPath = $configReader->getModuleDir(Dir::MODULE_ETC_DIR, 'ShopGo_AramexShipping') . '/wsdl/';
        $this->_rateServiceWsdl = $wsdlPath . 'aramex-rates-calculator-wsdl.wsdl';
    }

    /**
     * Create soap client
     *
     * @return \SoapClient
     */
    protected function _createSoapClient($wsdl, $trace = true)
    {
        $client = new \SoapClient($wsdl, ['trace' => $trace]);
        return $client;
    }

    /**
     * Create rate soap client
     *
     * @return \RateSoapClient
     */
    public function createRateSoapClient()
    {
        return $this->_createSoapClient($this->_rateServiceWsdl);
    }

    /**
     * Convert between 2 currency
     * @param  string $from     aramex currency code.
     * @param  string $to       store currency code.
     * @param  int    $amount  shipping rate that aramex return it.
     * @return int
     */
    public function convertCurrency($from, $to, $amount)
    {
        $url  = "https://www.google.com/finance/converter?a=$amount&from=$from&to=$to";
        $data = file_get_contents($url);
        preg_match("/<span class=bld>(.*)<\/span>/",$data, $converted);
        $converted = preg_replace("/[^0-9.]/", "", $converted[1]);
        return round($converted);
    }

    /**
     * Check if aramex debuging mode is enabled
     *
     * @return Boolean
     */
    public function getDebugStatus()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DEBUG,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
   }

    /**
     * Verfication Aramex Account
     *
     * @return string
     */
    public function checkAccount()
    {
        $params = array(
            'ClientInfo'  => array(
                'AccountCountryCode'    => $this->scopeConfig->getValue(self::XML_PATH_ACCOUNTCC,\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                'AccountEntity'         => $this->scopeConfig->getValue(self::XML_PATH_ACCOUNTENT,\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                'AccountNumber'         => $this->scopeConfig->getValue(self::XML_PATH_ACCOUNTNUM,\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                'AccountPin'            => $this->scopeConfig->getValue(self::XML_PATH_ACCOUNTPIN,\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                'UserName'              => $this->scopeConfig->getValue(self::XML_PATH_USERNAME,\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                'Password'              => $this->scopeConfig->getValue(self::XML_PATH_PASSWORD,\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                'Version'               => 'v1.0'
            ),

            'Transaction' => array(
                'Reference1'            => '001'
            ),

            'OriginAddress' => array(
                'Line1'                 => 'Originstreet',
                'City'                  => 'Dubai',
                'CountryCode'           => 'AE',
            ),

            'DestinationAddress' => array(
                'Line1'                 => 'DestinationStree',
                'City'                  => "Germany",
                'CountryCode'           => "DE",
                'PostCode'              => "12249",
            ),
            'ShipmentDetails' => array(
                'PaymentType'            => 'P',
                'ProductGroup'           => "EXP",
                'ProductType'            => $this->scopeConfig->getValue(self::XML_PATH_PRODUCTYPE,\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                'ActualWeight'           => array('Value' => 1, 'Unit' => 'KG'),
                'ChargeableWeight'       => array('Value' => 1, 'Unit' => 'KG'),
                'NumberOfPieces'         => 1
            )
        );

        $client  = $this->createRateSoapClient();
        $results = $client->CalculateRate($params);

        if($results->HasErrors)
            return "Invalid Account Parameter";
        else
            return "Success, Your Account is Valid";
    }

}