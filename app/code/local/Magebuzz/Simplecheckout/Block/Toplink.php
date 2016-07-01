<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Simplecheckout_Block_Toplink extends Mage_Checkout_Block_Links {
  public function addCheckoutLink() {
    if (Mage::helper('simplecheckout')->canSimpleCheckout()) {
      $parentBlock = $this->getParentBlock();
      if ($parentBlock) {
        $text = $this->__('Checkout');
        $parentBlock->addLink($text, 'simplecheckout', $text, TRUE, array(), 60, null, 'class="top-link-checkout"');
      }
      return $this;
    } else {
      return parent::addCheckoutLink();
    }
  }
}