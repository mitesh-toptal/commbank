<?php

namespace Ozi\Securecommanbank\Controller\Payment;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;

class Cancel extends AbstractPayment
{
    /**
     * @var OrderFactory
     */
    protected $_modelOrderFactory;

    public function __construct(Context $context, 
        OrderFactory $modelOrderFactory)
    {
        $this->_modelOrderFactory = $modelOrderFactory;

        parent::__construct($context);
    }

	public function execute() {
        if (ObjectManager::getInstance()->get('Magento\Checkout\Model\Session')->getLastRealOrderId()) {
            $order = $this->_modelOrderFactory->create()->loadByIncrementId(ObjectManager::getInstance()->get('Magento\Checkout\Model\Session')->getLastRealOrderId());
            if($order->getId()) {
				// Flag the order as 'cancelled' and save it
				$order->cancel()->setState(Order::STATE_CANCELED, true, 'Gateway has declined the payment.')->save();
			}
        }
	}
}
