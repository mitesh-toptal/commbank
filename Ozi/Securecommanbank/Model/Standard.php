<?php
namespace Ozi\Securecommanbank\Model;

use Magento\Framework\DataObject;
use Magento\Payment\Model\Method\AbstractMethod;

class Standard extends AbstractMethod {
	const CODE = 'securecommanbank';
    
    protected $_code = self::CODE;
	
	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = true;
	protected $_canUseForMultishipping  = false;
 //   protected $_formBlockType = 'securecommanbank/form_vpcpaymentgateway';
    protected $_infoBlockType = 'Ozi\Securecommanbank\Block\Info\Vpcsave';
    
    /**
     * @var bool
     */
    protected $_isGateway = true;

    /**
     * @var bool
     */
    protected $_isOffline = false;

    /**
     * @var bool
     */
    protected $_canRefund = true;

    /**
     * @var bool
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            null,
            null,
            $data
        );
        $this->urlBuilder = $urlBuilder;
    }
    
    /**
     * @return string
     */
    public function getCheckoutRedirectUrl()
    {
        return $this->urlBuilder->getUrl('securecommanbank/payment/redirect', ['_secure' => true]);
    }
    
    public function assignData(\Magento\Framework\DataObject $data) {
        parent::assignData($data);
        if (!($data instanceof \Magento\Framework\DataObject)) {
            $data = $this->dataObjectFactory->create($data);
        }
        $info = $this->getInfoInstance();
        $info->setVpcCard($data->getVpcCard())
        ->setVpcCardNum($info->encrypt($data->getVpcCardNum()))
        ->setVpcCardExp($data->getVpcCardExp())
        ->setVpcCardSecurityCode($info->encrypt($data->getVpcCardSecurityCode()));
        return $this;
    }

    public function prepareSave()
    {/*
        $info = $this->getInfoInstance();
        $info->setVpcCardNum($info->encrypt($info->getVpcCardNum()));
        return $this;*/
    }
}
?>