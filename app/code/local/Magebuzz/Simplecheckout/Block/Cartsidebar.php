<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Simplecheckout_Block_Cartsidebar extends Mage_Checkout_Block_Cart_Sidebar {
  public function getCheckoutUrl() {
    if (Mage::helper('simplecheckout')->canSimpleCheckout()) {
      return Mage::getUrl('simplecheckout');
    }
    return Mage::helper('checkout/url')->getCheckoutUrl();
  }
}