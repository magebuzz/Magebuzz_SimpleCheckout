<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Simplecheckout_Block_Link extends Mage_Checkout_Block_Onepage_Link {
  public function getCheckoutUrl() {
    if (Mage::helper('simplecheckout')->canSimpleCheckout()) {
      return $this->getUrl('simplecheckout', array('_secure' => TRUE));
    } else {
      return parent::getCheckoutUrl();
    }
  }
}