<?php

namespace Ozi\Securecommanbank\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @param \Magento\Payment\Helper\Data $paymentHelper
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->paymentHelper = $paymentHelper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        /**
         * @var $payment Payupl
         */
        $config = [];
        $payment = $this->paymentHelper->getMethodInstance(Standard::CODE);
        if ($payment->isAvailable()) {
            $redirectUrl = $payment->getCheckoutRedirectUrl();
            $config = [
                'payment' => [
                    'securecommanbank' => [
                        'redirectUrl' => $redirectUrl
                    ]
                ]
            ];
        }
        return $config;
    }
}
