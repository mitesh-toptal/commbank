<?php

namespace Ozi\Securecommanbank\Controller\Payment;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Magento\Framework\Action;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Psr\Log\LoggerInterface;
use Ozi\Securecommanbank\Helper\Data as HelperData;

class Response extends AbstractPayment
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var OrderFactory
     */
    protected $_modelOrderFactory;

    /**
     * @var LoggerInterface
     */
    protected $_logLoggerInterface;

    public function __construct(
        Context $context, 
        HelperData $helperData, 
        OrderFactory $modelOrderFactory, 
        LoggerInterface $logLoggerInterface
    )
    {
        $this->_helperData = $helperData;
        $this->_modelOrderFactory = $modelOrderFactory;
        $this->_logLoggerInterface = $logLoggerInterface;

        parent::__construct($context);
    }
    /*
    * 
    */
	public function execute() {
		if($this->getRequest()->isGet()) {
			
            $response = $this->getRequest()->getParams();
            $this->_logLoggerInterface->debug(print_r(array('ResponseData'=>$response), true));
            /********************************************************************************
            * 
            * -------------------------------------------------------------------------------
            * CHECK RETURNED HASH with sent secureHASH for INTEGRITY....
            * REMAINING
            * Validate HASH
            * vpc_ReturnAuthResponseData  
            * 
            * 
            * 
            *********************************************************************************/
            $validated = false;
            if($response['vpc_TxnResponseCode']==="0"){
                //Success
                $response['vpc_TransactionNo'];
                $validated=true;
            }else {//if($response['vpc_TxnResponseCode']==="7"){
                $response['vpc_Message'];//Error message 
            }
            $ResponseMessage=$this->_helperData->getResultDescription($response['vpc_TxnResponseCode']);

			$orderId = $response['vpc_OrderInfo']; // Generally sent by gateway

			if($validated) {
				// Payment was successful, so update the order's state, send order email and move to the success page
				$order = $this->_modelOrderFactory->create();
				$order->loadByIncrementId($orderId);
				$order->setState(Order::STATE_PROCESSING, true, 'Gateway has authorized the payment.');
				
				//$order->sendNewOrderEmail();
				//$order->setEmailSent(true);
				
				$order->save();
			
				ObjectManager::getInstance()->get('Magento\Checkout\Model\Session')->unsQuoteId();
				
				//Action::_redirect('checkout/onepage/success', ['_secure'=>true]);
                $this->_redirect('checkout/onepage/success', ['_secure'=>true]);
			}
			else {
				// There is a problem in the response we got
				$this->cancelOrder();
                ObjectManager::getInstance()->get('Magento\Checkout\Model\Session')->setErrorMessage($ResponseMessage);
                $this->_logLoggerInterface->debug('MIGS Payment gateway error: '.$ResponseMessage.' ['.$response['vpc_Message'].' ]');
				//Action::_redirect('checkout/onepage/failure', ['_secure'=>true]);
                $this->_redirect('checkout/onepage/failure', ['_secure'=>true]);
			}
		}
		else{
			//Action::_redirect('');
            $this->_redirect('');
        }
	}
    /*
    * 
    */
    public function cancelOrder() {
        if (ObjectManager::getInstance()->get('Magento\Checkout\Model\Session')->getLastRealOrderId()) {
            $order = $this->_modelOrderFactory->create()->loadByIncrementId(ObjectManager::getInstance()->get('Magento\Checkout\Model\Session')->getLastRealOrderId());
            if($order->getId()) {
                // Flag the order as 'cancelled' and save it
                $order->cancel()->setState(Order::STATE_CANCELED, true, 'Gateway has declined the payment.')->save();
            }
        }
    }
}
