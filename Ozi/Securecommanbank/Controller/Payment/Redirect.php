<?php

namespace Ozi\Securecommanbank\Controller\Payment;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\LayoutFactory;
use Ozi\Securecommanbank\Model\PaymentconnectionFactory;

class Redirect extends AbstractPayment
{
    /**
     * @var PaymentconnectionFactory
     */
    protected $_modelPaymentconnectionFactory;

    /**
     * @var LayoutFactory
     */
    protected $_viewLayoutFactory;

    public function __construct(
        Context $context, 
        PaymentconnectionFactory $modelPaymentconnectionFactory, 
        LayoutFactory $viewLayoutFactory
    )
    {
        $this->_modelPaymentconnectionFactory = $modelPaymentconnectionFactory;
        $this->_viewLayoutFactory = $viewLayoutFactory;

        parent::__construct($context);
    }

	public function execute() { 
        $this->getResponse()->setBody(
            $this->_viewLayoutFactory
                ->create()
                ->createBlock('Magento\Framework\View\Element\Template')
                ->setTemplate('Ozi_Securecommanbank::securecommanbank/redirect.phtml')
                ->toHtml()
        );
	}
}
