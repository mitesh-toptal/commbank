<?php
namespace Ozi\Securecommanbank\Model\Source\Payment;

class Vpc{
	public function toOptionArray(){
    return [
      ['value' => 'Mastercard', 'label' => __('Mastercard')],
      ['value' => 'Visa', 'label' => __('Visa')],
      ['value' => 'Amex', 'label' => __('American Express')],
      ['value' => 'AmexPurchaseCard', 'label' => __('Amex Corporate Purchase Card')],
    ];
  }
}
?>