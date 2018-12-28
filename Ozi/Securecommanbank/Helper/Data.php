<?php
namespace Ozi\Securecommanbank\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $_configScopeConfigInterface;

    public function __construct(Context $context)
    {
        $this->_configScopeConfigInterface = $context->getScopeConfig();

        parent::__construct($context);
    }
    /*
    * parse response code into message
    * @return string
    */
    function getResultDescription($responseCode) {

        switch ($responseCode) {
            case "0" : $result = "Transaction Successful"; break;
            case "?" : $result = "Transaction status is unknown"; break;
            case "E" : $result = "Referred"; break;
            case "1" : $result = "Transaction Declined"; break;
            case "2" : $result = "Bank Declined Transaction"; break;
            case "3" : $result = "No Reply from Bank"; break;
            case "4" : $result = "Expired Card"; break;
            case "5" : $result = "Insufficient funds"; break;
            case "6" : $result = "Error Communicating with Bank"; break;
            case "7" : $result = "Payment Server detected an error"; break;
            case "8" : $result = "Transaction Type Not Supported"; break;
            case "9" : $result = "Bank declined transaction (Do not contact Bank)"; break;
            case "A" : $result = "Transaction Aborted"; break;
            case "C" : $result = "Transaction Cancelled"; break;
            case "D" : $result = "Deferred transaction has been received and is awaiting processing"; break;
            case "F" : $result = "3D Secure Authentication failed"; break;
            case "I" : $result = "Card Security Code verification failed"; break;
            case "L" : $result = "Shopping Transaction Locked (Please try the transaction again later)"; break;
            case "N" : $result = "Cardholder is not enrolled in Authentication scheme"; break;
            case "P" : $result = "Transaction has been received by the Payment Adaptor and is being processed"; break;
            case "R" : $result = "Transaction was not processed - Reached limit of retry attempts allowed"; break;
            case "S" : $result = "Duplicate SessionID (Amex Only)"; break;
            case "T" : $result = "Address Verification Failed"; break;
            case "U" : $result = "Card Security Code Failed"; break;
            case "V" : $result = "Address Verification and Card Security Code Failed"; break;
            default  : $result = "Unable to be determined"; 
        }
        
        return $result;
    }
    
    public function getRedirectUrl(){
        if($this->_configScopeConfigInterface->getValue('payment/securecommanbank/virtualPaymentClientURL', ScopeInterface::SCOPE_STORE)){
            return $this->_configScopeConfigInterface->getValue('payment/securecommanbank/virtualPaymentClientURL', ScopeInterface::SCOPE_STORE);
        }
        
        return 'https://migs.mastercard.com.au/vpcpay';
    }
    /*
    * This is secret for encoding the MD5 hash
    * This secret will vary from merchant to merchant
    * To not create a secure hash, let SECURE_SECRET be an empty string - "" 
    */
    public function getSecureHash(){        
        if ($this->_configScopeConfigInterface->getValue('payment/securecommanbank/vpc_mode_test', ScopeInterface::SCOPE_STORE)) {
            return $this->_configScopeConfigInterface->getValue('payment/securecommanbank/vpc_SecureHash_Test', ScopeInterface::SCOPE_STORE);
        } else {
            return $this->_configScopeConfigInterface->getValue('payment/securecommanbank/vpc_SecureHash_Live', ScopeInterface::SCOPE_STORE);
        }
        
        //return $this->_configScopeConfigInterface->getValue('payment/securecommanbank/vpc_SecureHash', ScopeInterface::SCOPE_STORE);
    }
    
}