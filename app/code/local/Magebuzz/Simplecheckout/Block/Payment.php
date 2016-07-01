<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Simplecheckout_Block_Payment extends Mage_Checkout_Block_Onepage_Payment {
  public function getQuote() {
    return Mage::getSingleton('checkout/session')->getQuote();
  }

  public function getSelectedMethodCode() {
    if ($method = $this->getQuote()->getPayment()->getMethod()) {
      return $method;
    }
    return $this->getPaymentdefault();
  }

  public function getPaymentdefault() {
    $paymentdefault = Mage::getStoreConfig('simplecheckout/default_fields/payment_method_id');
    return $paymentdefault;
  }
  /* public function getPaymentmethod(){
    $method = $this->getSelectedMethodCode();
    return $method;
  } */
}