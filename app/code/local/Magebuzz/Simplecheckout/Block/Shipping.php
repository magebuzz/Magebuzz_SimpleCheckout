<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Simplecheckout_Block_Shipping extends Mage_Checkout_Block_Onepage_Shipping {
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

  public function getCountryHtmlSelect($type) {
    $shipping_data = $this->getShippingCheckoutData();
    if (isset($shipping_data['country_id'])) {
      $countryId = $shipping_data['country_id'];
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

  public function getShippingCheckoutData() {
    $data = Mage::getSingleton('checkout/session')->getCheckoutData();
    $shipping_data = isset($data['shipping']) ? $data['shipping'] : FALSE;
    if ($shipping_data && isset($shipping_data['shipping_to_different_address'])) {
      $shipping_data['shipping_to_different_address'] = $data['shipping_to_different_address'];
    }
    return $shipping_data;
  }
}