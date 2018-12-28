<?php

namespace Ozi\Securecommanbank\Block\Info;

use Magento\Framework\DataObject;
use Magento\Payment\Block\Info;

class Vpcsave extends Info
{
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $info = $this->getInfo();
        if($info->getVpcCard()){
            $transport = new DataObject([__('Credit Card Type') => $info->getVpcCard()]);
        }
        $transport = parent::_prepareSpecificInformation($transport);
        /*if (!$this->getIsSecureMode()) {
            $transport->addData(array(
                __('Expiration Date') => $this->_formatCardDate(
                    $info->getCcExpYear(), $this->getCcExpMonth()
                ),
                __('Credit Card Number') => $info->getCcNumber(),
            ));
        }                                 */
        return $transport;
    }
    
}
