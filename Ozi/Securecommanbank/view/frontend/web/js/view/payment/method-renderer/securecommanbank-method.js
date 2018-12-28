/**
 * @copyright Copyright (c) 2015 Orba Sp. z o.o. (http://orba.pl)
 */

define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/payment/additional-validators',
        'mage/url',
    ],
    function (  
        $, 
        Component, 
        customer, 
        placeOrderAction, 
        checkoutData,
        additionalValidators,
        url
        ){
        'use strict';

        return Component.extend({
            defaults: {
                redirectAfterPlaceOrder: false,
                template: 'Ozi_Securecommanbank/payment/securecommanbank'
            },
            getData: function() {
                return {
                    "method": this.item.method
                };
            },
            isActive: function() {
                return true;
            },

            validate: function() {
                var $form = $('#' + this.getCode() + '-form');
                return $form.validation() && $form.validation('isValid');
            },
            /**
             * override Place order.
             */
            placeOrder: function (data, event) {
                var self = this,
                    placeOrder;

                if (event) {
                    event.preventDefault();
                }

                if (this.validate() && additionalValidators.validate()) {
                    this.isPlaceOrderActionAllowed(false);
                    placeOrder = placeOrderAction(this.getData(), this.redirectAfterPlaceOrder, this.messageContainer);

                    $.when(placeOrder).done(function () {
                        $.mage.redirect(window.checkoutConfig.payment.securecommanbank.redirectUrl);
                    }).fail(function(){
                        self.isPlaceOrderActionAllowed(true);
                    });
                    return true;
                }
                return false;
            },
            /*
            getPaytypes: function() {
                return window.checkoutConfig.payment.orbaPayupl.paytypes;
            }
            */
        });
    }
);