<?php
namespace Ozi\Securecommanbank\Model;

class Source{
	public function toOptionArray(){
    return [
      ['value' => 'ssl', 'label' => __('Auth-Purchase with 3DS Authentication')],
      ['value' => 'threeDSecure', 'label' => __('3DS Authentication Only')],
    ];
  }
}
?>