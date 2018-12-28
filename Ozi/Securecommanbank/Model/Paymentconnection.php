<?php
namespace Ozi\Securecommanbank\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Locale\Resolver;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Paymentconnection {
    /**
     * @var ScopeConfigInterface
     */
    protected $_configScopeConfigInterface;

    /**
     * @var Encryptor
     */
    protected $_encryptionEncryptor;

    /**
     * @var StoreManagerInterface
     */
    protected $_modelStoreManagerInterface;

    /**
     * @var Resolver
     */
    protected $_localeResolver;
    
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;
    
    protected $order;
	
	protected $_checkoutSession;
	
	protected $_orderFactory;
    
    /**
     * @var LoggerInterface
     */
    protected $_logLoggerInterface;

    public function __construct(
        ScopeConfigInterface $configScopeConfigInterface, 
        Encryptor $encryptionEncryptor, 
        StoreManagerInterface $modelStoreManagerInterface, 
        Resolver $localeResolver,
        \Magento\Framework\UrlInterface $urlBuilder,
        LoggerInterface $logLoggerInterface,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Sales\Model\OrderFactory $OrderFactory
    )
    {
        $this->_configScopeConfigInterface = $configScopeConfigInterface;
        $this->_encryptionEncryptor = $encryptionEncryptor;
        $this->_modelStoreManagerInterface = $modelStoreManagerInterface;
        $this->_localeResolver = $localeResolver;
        $this->urlBuilder = $urlBuilder;
        $this->_logLoggerInterface = $logLoggerInterface;
		$this->_checkoutSession = $checkoutSession;
		$this->_orderFactory = $OrderFactory;
    }
    /*
    * get all param values for VPC
    * @return array
    */
    public function getPostData(){
        $postdata = Array(
            'Title' => 'MIGS 2.5 Party Transaction',
            'vpc_AccessCode' => (string)$this->getVpcAccessCode(),
            'vpc_Amount' => (string)$this->_getAmount(),
            'vpc_Command' => (string)$this->getVpcCommand(),
            'vpc_Gateway' => (string)$this->getVpcGateway(),
            'vpc_MerchTxnRef' => (string)$this->getVpcMerchTxnRef(),
            'vpc_Merchant' => (string)$this->getVpcMerchant(),
            'vpc_OrderInfo' => (string)$this->getVpcOrderInfo(),
            'vpc_ReturnURL' => (string)$this->getVpcReturnURL(),
            'vpc_Version' => (string)$this->getVpcVersion()
        );
        ksort($postdata);
        
        $this->_logLoggerInterface->debug(print_r(array('RequestData'=>$postdata), true));
        return $postdata;
    }
    /**
     * Get Virtual Payment Client Version
     *
     * @return string
     */
    public function getVpcVersion(){
        return $this->_configScopeConfigInterface->getValue('payment/securecommanbank/vpc_Version', ScopeInterface::SCOPE_STORE);
    }
    /**
     * Get Virtual Payment Client Command
     * @return string
     */
    public function getVpcCommand(){
        return $this->_configScopeConfigInterface->getValue('payment/securecommanbank/vpc_Command', ScopeInterface::SCOPE_STORE);
    }
    /**
     * Get Virtual Payment Client Merchant Id
     * @return string
     */
    public function getVpcMerchant(){
        
        if ($this->_configScopeConfigInterface->getValue('payment/securecommanbank/vpc_mode_test', ScopeInterface::SCOPE_STORE)) {
            return 'TEST' . $this->_configScopeConfigInterface->getValue('payment/securecommanbank/vpc_Merchant', ScopeInterface::SCOPE_STORE);
        } else {
            return $this->_configScopeConfigInterface->getValue('payment/securecommanbank/vpc_Merchant', ScopeInterface::SCOPE_STORE);
        }
    }
    /**
     * Get Virtual Payment Client Access Code
     * @return string
     */
    public function getVpcAccessCode(){
        if ($this->_configScopeConfigInterface->getValue('payment/securecommanbank/vpc_mode_test', ScopeInterface::SCOPE_STORE)) {
            return $this->_configScopeConfigInterface->getValue('payment/securecommanbank/vpc_AccessCode_Test', ScopeInterface::SCOPE_STORE);
        } else {
            return $this->_configScopeConfigInterface->getValue('payment/securecommanbank/vpc_AccessCode_Live', ScopeInterface::SCOPE_STORE);
        }
    }
    /**
     * Get Virtual Payment Client Transaction reference
     * @return string
     */
    public function getVpcMerchTxnRef(){
        //return 'MerchTxnRef_'.$this->getOrder()->getRealOrderId();
        return $this->getOrder()->getRealOrderId();
    }
    /**
     * Get Virtual Payment Client Order Info
     * @return string
     */
    public function getVpcOrderInfo(){
        return $this->getOrder()->getRealOrderId();
    }
    /**
     * Get Virtual Payment Client Gateway Type
     * @return string
     */
    public function getVpcGateway(){
        return $this->_configScopeConfigInterface->getValue('payment/securecommanbank/vpc_Gateway', ScopeInterface::SCOPE_STORE);
    }
    /**
     * Get Virtual Payment Client Return URL
     * @return string
     */
    public function getVpcReturnURL(){
        return $this->urlBuilder->getUrl('securecommanbank/payment/response', ['_secure' => true]);
    }
    
    /**
     * order object
     *
     * @return \Magento\Sales\Model\Order
     */
    private function getOrder()
    {
        if(!$this->order){
			
			$_order = ObjectManager::getInstance()->get('Magento\Sales\Model\Order');
			$orderId = ObjectManager::getInstance()->get('Magento\Checkout\Model\Session')->getLastRealOrderId();
            $this->order = $_order->loadByIncrementId($orderId);
			
			/*$incrementId = $this->_checkoutSession->getLastRealOrder()->getIncrementId();
			$this->_logLoggerInterface->debug(print_r(array('incrementId'=>$incrementId), true));
			$this->_orderFactory->create()->load($incrementId);
			exit;*/
        }
        
        return $this->order;
    }
	
    /**
     * Grand total getter
     *
     * @return string
     */
    private function _getAmount()
    {
        $_amount = (double)$this->getOrder()->getBaseGrandTotal();           
        return $_amount*100;
    }
}

?>