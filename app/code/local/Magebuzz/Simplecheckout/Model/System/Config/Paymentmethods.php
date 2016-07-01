<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Simplecheckout_Model_System_Config_Paymentmethods {

  public function toOptionArray() {
    $payments = Mage::getSingleton('payment/config')->getActiveMethods();
    foreach ($payments as $paymentCode => $paymentModel) {
      $paymentTitle = Mage::getStoreConfig('payment/' . $paymentCode . '/title');
      $methods[$paymentCode] = array('label' => $paymentTitle, 'value' => $paymentCode,);
    }
    return $methods;
  }
}
