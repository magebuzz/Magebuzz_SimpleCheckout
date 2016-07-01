<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Simplecheckout_Block_Simplecheckout extends Mage_Checkout_Block_Onepage_Abstract {
  public function _prepareLayout() {
    return parent::_prepareLayout();
  }

  protected function _construct() {
    parent::_construct();
    if (!$this->isCustomerLoggedIn()) {
      $this->_initDefaultShippingAddress();
    } else {
      $this->_setShippingAddress();
    }
		
		//set default shipping method
		try {
			$shipping_method = Mage::helper('simplecheckout')->getDefaultShippingMethod();
			if ($shipping_method = Mage::helper('simplecheckout')->getDefaultShippingMethod()) {
				Mage::getSingleton('checkout/type_onepage')->saveShippingMethod($shipping_method);
			}	
		}
		catch (Exception $e) {
			// Silence is go
		}
		Mage::getSingleton('checkout/type_onepage')->getQuote()->collectTotals()->save();
		
  }

  protected function _initDefaultShippingAddress() {
    $address = $this->getCheckout()->getQuote()->getShippingAddress();
    $default_data = Mage::helper('simplecheckout')->getDefaultData();

    if ($address->getPostcode() == '') $address->setPostcode($default_data['postcode']);
    if ($address->getCountryId() == '') $address->setCountryId($default_data['country_id']);
    if ($address->getRegionId() == '') $address->setRegionId($default_data['region_id']);
    if ($address->getCity() == '') $address->setCity($default_data['city']);
    // $address->setPostcode($default_data['postcode'])
    // ->setCountryId($default_data['country_id'])
    // ->setRegionId($default_data['region_id'])
    // ->setCity($default_data['city']);
    $address->setCollectShippingRates(TRUE)->save();

    //have to set default country for billing address to load some kinds of payment method in the first time.
    $billing_address = $this->getCheckout()->getQuote()->getBillingAddress();
    $billing_address->setCountryId($default_data['country_id']);
  }

  protected function _setShippingAddress() {
    $address = $this->getCheckout()->getQuote()->getShippingAddress();
    $shipping_address = $this->getCustomer()->getPrimaryShippingAddress();
    if ($shipping_address) {
      $address->setPostcode($shipping_address->getPostcode());
      $address->setCountryId($shipping_address->getCountryId());
      $address->setRegionId($shipping_address->getRegionId());
      $address->setCity($shipping_address->getCity());
      $address->setCollectShippingRates(TRUE)->save();
    } else {
      $default_data = Mage::helper('simplecheckout')->getDefaultData();
      if ($address->getPostcode() == '') $address->setPostcode($default_data['postcode']);
      if ($address->getCountryId() == '') $address->setCountryId($default_data['country_id']);
      if ($address->getRegionId() == '') $address->setRegionId($default_data['region_id']);
      if ($address->getCity() == '') $address->setCity($default_data['city']);
    }

    $billing_address = $this->getCheckout()->getQuote()->getBillingAddress();
    $billing_address_data = $this->getCustomer()->getPrimaryBillingAddress();
    if ($billing_address_data) {
      $billing_address->setCountryId($billing_address_data->getCountryId());
    } else {
      $default_data = Mage::helper('simplecheckout')->getDefaultData();
      $billing_address->setCountryId($default_data['country_id']);
    }
  }

  public function getBillingAddress() {
    echo $this->getChildHtml('billing');
  }

  public function isShowShippingMethod() {
    return !$this->getQuote()->isVirtual();
  }

  public function isEnabledField($field) {
    $options = explode(',', Mage::helper('simplecheckout')->getAjaxFields());
    if (in_array($field, $options)) {
      return TRUE;
    }
    return FALSE;
  }

  public function getCouponCode() {
    $quote = $this->getQuote();
    return $quote->getCouponCode();
  }
}