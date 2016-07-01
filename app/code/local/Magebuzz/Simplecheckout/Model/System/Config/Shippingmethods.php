<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Simplecheckout_Model_System_Config_Shippingmethods {
  public function toOptionArray() {
    $options = array();
    $options = $this->getActiveShippingMethods();
    return $options;
  }

  public function getActiveShippingMethods() {
    $activeCarriers = Mage::getSingleton('shipping/config')->getActiveCarriers();
    foreach ($activeCarriers as $carrierCode => $carrierModel) {
      $options = array();
      if ($carrierMethods = $carrierModel->getAllowedMethods()) {
        foreach ($carrierMethods as $methodCode => $method) {
          $code = $carrierCode . '_' . $methodCode;
          $options[] = array('value' => $code, 'label' => $method);
        }
        $carrierTitle = Mage::getStoreConfig('carriers/' . $carrierCode . '/title');
      }
      if ($options) {
        $methods[] = array('value' => $options, 'label' => $carrierTitle);
      }
    }
    return $methods;
  }

}