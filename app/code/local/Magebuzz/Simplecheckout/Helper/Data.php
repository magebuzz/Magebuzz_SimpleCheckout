<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Simplecheckout_Helper_Data extends Mage_Core_Helper_Abstract {
  public function isDifferentShippingAddress() {
    $storeId = Mage::app()->getStore()->getId();
    return (bool)Mage::getStoreConfig('simplecheckout/general/show_shipping_address', $storeId);
  }
  
  public function appChooseStyle(){
    $storeId = Mage::app()->getStore()->getId();
    $nameCss = Mage::getStoreConfig('simplecheckout/general/choose_style', $storeId);
    return 'magebuzz/simplecheckout/css/simplecheckout-'.$nameCss.'.css';
    //Zend_Debug::dump($cl);die('2');
  }

  public function getDefaultData() {
    $data = array();
    $storeId = Mage::app()->getStore()->getId();
    $data['country_id'] = Mage::getStoreConfig('simplecheckout/default_fields/country_id', $storeId);
    $data['postcode'] = Mage::getStoreConfig('simplecheckout/default_fields/postcode', $storeId);
    $data['region_id'] = Mage::getStoreConfig('simplecheckout/default_fields/region_id', $storeId);
    $data['city'] = Mage::getStoreConfig('simplecheckout/default_fields/city', $storeId);
    return $data;
  }

  public function enableSaveBilling() {
    $storeId = Mage::app()->getStore()->getId();
    return (bool)Mage::getStoreConfig('simplecheckout/ajax_update/enable_ajax', $storeId);
  }

  public function isUpdatePayment() {
    $storeId = Mage::app()->getStore()->getId();
    return (bool)Mage::getStoreConfig('simplecheckout/ajax_update/update_payment', $storeId);
  }

  public function getAjaxFields() {
    $storeId = Mage::app()->getStore()->getId();
    $options = Mage::getStoreConfig('simplecheckout/ajax_update/ajax_fields', $storeId);
    return $options;
  }

  public function canSimpleCheckout() {
    $storeId = Mage::app()->getStore()->getId();
    return (bool)Mage::getStoreConfig('simplecheckout/general/enable_simplecheckout', $storeId);
  }

  public function enableOrderComment() {
    $storeId = Mage::app()->getStore()->getId();
    return (bool)Mage::getStoreConfig('simplecheckout/general/enable_order_comment', $storeId);
  }

  public function isShowTermsConditions() {
    $storeId = Mage::app()->getStore()->getId();
    return (bool)Mage::getStoreConfig('simplecheckout/term_condition/enable_term_condition', $storeId);
  }

  public function getTermsConditions() {
    $storeId = Mage::app()->getStore()->getId();
    return Mage::getStoreConfig('simplecheckout/term_condition/condition_content', $storeId);
  }

  public function isAllowedGuestCheckout() {
    $quote = Mage::getSingleton('checkout/session')->getQuote();
    $store = $quote->getStoreId();
    //$storeId = Mage::app()->getStore()->getId();
    $guestCheckout = Mage::getStoreConfig('simplecheckout/general/allow_guest_checkout', $store);
    if ($guestCheckout == TRUE) {
      $result = new Varien_Object();
      $result->setIsAllowed($guestCheckout);
      Mage::dispatchEvent('checkout_allow_guest', array('quote' => $quote, 'store' => $store, 'result' => $result));

      $guestCheckout = $result->getIsAllowed();
    }
    return $guestCheckout;
  }

  // update Delivery by Xboy

  public function getCurrentStoreId(){
    return Mage::app()->getStore()->getId();  
  }                          
                                                                 
  public function isEnableDelivery(){
    return (int) Mage::getStoreConfig('simplecheckout/deliverydate/enable_delivery',$this->getCurrentStoreId()) ; 
  }
  public function getNumberPeriod(){
    return (int) Mage::getStoreConfig('simplecheckout/deliverydate/number_period',$this->getCurrentStoreId()) ; 
  }
  
  public function getDeliveryFormatDate(){
    return Mage::getStoreConfig('simplecheckout/deliverydate/formatdate',$this->getCurrentStoreId()) ; 
  }
  
  public function getDeliveryWeekendDay(){
    return Mage::getStoreConfig('simplecheckout/deliverydate/weekend',$this->getCurrentStoreId()) ; 
  }
  
  public function getDisableday(){
    return Mage::getStoreConfig('simplecheckout/deliverydate/disableday',$this->getCurrentStoreId()) ;     
  }
  
  public function getTimeRagge(){
    
    return Mage::getStoreConfig('simplecheckout/deliverydate/addtimerange',$this->getCurrentStoreId()) ; 
  }
          
  public function getDeliveryNote(){
    return Mage::getStoreConfig('simplecheckout/deliverydate/delinote',$this->getCurrentStoreId()) ;  
  }
  
  public function getDefaultMessage(){
    return Mage::getStoreConfig('simplecheckout/deliverydate/delicomment',$this->getCurrentStoreId()) ;    
  }

  // end update Delivery

	//coupon code
	public function isEnabledCoupon()
	{
		return Mage::getStoreConfig('simplecheckout/coupon/enable_coupon', $this->getCurrentStoreId());
	}
	
	public function getDefaultShippingMethod() {		
		return Mage::getStoreConfig('simplecheckout/default_fields/shipping_method_id', $this->getCurrentStoreId()); 		
	}
	
	public function getDefaultPaymentMethod() {
		return Mage::getStoreConfig('simplecheckout/default_fields/payment_method_id', $this->getCurrentStoreId());
	}
}