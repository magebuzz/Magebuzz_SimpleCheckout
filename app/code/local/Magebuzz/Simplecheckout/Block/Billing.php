<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Simplecheckout_Block_Billing extends Mage_Checkout_Block_Onepage_Abstract {
  protected $_address;

  public function _prepareLayout() {
    return parent::_prepareLayout();
  }

  public function getAddress() {
    if (is_null($this->_address)) {
      if ($this->isCustomerLoggedIn()) {
        $this->_address = $this->getQuote()->getBillingAddress();
        if (!$this->_address->getFirstname()) {
          $this->_address->setFirstname($this->getQuote()->getCustomer()->getFirstname());
        }
        if (!$this->_address->getLastname()) {
          $this->_address->setLastname($this->getQuote()->getCustomer()->getLastname());
        }
      } else {
        $this->_address = Mage::getModel('sales/quote_address');
      }
    }

    return $this->_address;
  }

  /*
  * get data in session if have
  */
  public function getBillingCheckoutData() {
    $data = Mage::getSingleton('checkout/session')->getCheckoutData();
    return isset($data['billing']) ? $data['billing'] : FALSE;
  }

  public function canShip() {
    return !$this->getQuote()->isVirtual();
  }

  public function isUseBillingAddressForShipping() {
    if (($this->getQuote()->getIsVirtual()) || !$this->getQuote()->getShippingAddress()->getSameAsBilling()) {
      return FALSE;
    }
    return TRUE;
  }

  public function getCountryHtmlSelect($type) {
    $billing_data = $this->getBillingCheckoutData();
    if (isset($billing_data['country_id'])) {
      $countryId = $billing_data['country_id'];
    } else {
      $countryId = $this->getAddress()->getCountryId();
    }
    if (is_null($countryId)) {
      $countryId = Mage::helper('core')->getDefaultCountry();
    }
    $select = $this->getLayout()->createBlock('core/html_select')->setName($type . '[country_id]')->setId($type . ':country_id')->setTitle(Mage::helper('checkout')->__('Country'))->setClass('validate-select')->setValue($countryId)->setOptions($this->getCountryOptions());
    if ($type === 'shipping') {
      $select->setExtraParams('onchange="if(window.shipping)shipping.setSameAsBilling(false);"');
    }

    return $select->getHtml();
  }

  public function getAddressesHtmlSelect($type) {
    if ($this->isCustomerLoggedIn()) {
      $options = array();
      foreach ($this->getCustomer()->getAddresses() as $address) {
        $options[] = array('value' => $address->getId(), 'label' => $address->format('oneline'));
      }

      $addressId = $this->getAddress()->getCustomerAddressId();
      if (empty($addressId)) {
        if ($type == 'billing') {
          $address = $this->getCustomer()->getPrimaryBillingAddress();
        } else {
          $address = $this->getCustomer()->getPrimaryShippingAddress();
        }
        if ($address) {
          $addressId = $address->getId();
        }
      }

      $select = $this->getLayout()->createBlock('core/html_select')->setName($type . '_address_id')->setId($type . '-address-select')->setClass('address-select') //->setExtraParams('onchange="show_new_address(this.value)"')
        ->setValue($addressId)->setOptions($options);

      $select->addOption('', Mage::helper('checkout')->__('New Address'));

      return $select->getHtml();
    }
    return '';
  }

  public function isAllowedGuestCheckout() {
    return Mage::helper('simplecheckout')->isAllowedGuestCheckout();
  }
}