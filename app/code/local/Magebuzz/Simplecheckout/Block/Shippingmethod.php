<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Simplecheckout_Block_Shippingmethod extends Mage_Checkout_Block_Onepage_Shipping_Method {
  protected $_address;

  public function getAddressShippingMethod() {
    if ($method = $this->getAddress()->getShippingMethod()) {
      return $method;
    }
    return $this->getShippingdefault();
  }

  public function getAddress() {
    if (empty($this->_address)) {
      $this->_address = $this->getQuote()->getShippingAddress();
    }
    return $this->_address;
  }

  public function getShippingdefault() {
    $shippingdefault = Mage::getStoreConfig('simplecheckout/default_fields/shipping_method_id');
    return $shippingdefault;
  }
}