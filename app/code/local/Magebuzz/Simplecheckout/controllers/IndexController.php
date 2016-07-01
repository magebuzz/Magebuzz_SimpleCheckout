<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Simplecheckout_IndexController extends Mage_Core_Controller_Front_Action {
  public function getOnepage() {
    return Mage::getSingleton('checkout/type_onepage');
  }

  public function getSession() {
    return Mage::getSingleton('checkout/session');
  }

  public function indexAction() {
    $quote = $this->getOnepage()->getQuote();
    if (!$quote->hasItems() || $quote->getHasError()) {
      $this->_redirect('checkout/cart');
      return;
    }
    if (!$quote->validateMinimumAmount()) {
      $error = Mage::getStoreConfig('sales/minimum_order/error_message');
      Mage::getSingleton('checkout/session')->addError($error);
      $this->_redirect('checkout/cart');
      return;
    }

    $this->loadLayout();
    $this->_initLayoutMessages('checkout/session');
    $this->getLayout()->getBlock('head')->setTitle(Mage::helper('simplecheckout')->__('Simple Checkout'));
    $this->renderLayout();
  }

  public function save_addressAction() {
    $billing_data = $this->getRequest()->getPost('billing');
    $shipping_data = $this->getRequest()->getPost('shipping');
    $shipping_method = $this->getRequest()->getPost('shipping_method', FALSE);

    $billing_address_id = $this->getRequest()->getPost('billing_address_id', FALSE);
    $shipping_address_id = $this->getRequest()->getPost('shipping_address_id', FALSE);

    if (isset($billing_data['use_for_shipping']) && $billing_data['use_for_shipping'] == '1') {
      $shipping_data = $billing_data;
      $shipping_address_id = $billing_address_id;
    }

    $billing_street = trim(implode("\n", $billing_data['street']));
    $shipping_street = trim(implode("\n", $shipping_data['street']));

    //update shipping address
    if ($shipping_address_id) {
      $customer_address = Mage::getModel('customer/address')->load($shipping_address_id);
      $this->getOnepage()->getQuote()->getShippingAddress()->setCountryId($customer_address->getCountryId())->setCity($customer_address->getCity())->setPostcode($customer_address->getPostcode())->setRegionId($customer_address->getRegionId())->setRegion($customer_address->getRegion())->setFirstname($customer_address->getFirstname())->setLastname($customer_address->getLastname())->setCompany($customer_address->getCompany())->setFax($customer_address->getFax())->setTelephone($customer_address->getTelephone())->setStreet($customer_address->getStreet())->save()->setCollectShippingRates(TRUE);
    } else {
      $this->getOnepage()->getQuote()->getShippingAddress()->setCountryId($shipping_data['country_id'])->setCity($shipping_data['city'])->setPostcode($shipping_data['postcode'])->setRegionId($shipping_data['region_id'])->setRegion($shipping_data['region'])->setFirstname($shipping_data['firstname'])->setLastname($shipping_data['lastname'])->setCompany($shipping_data['company'])->setFax($shipping_data['fax'])->setTelephone($shipping_data['telephone'])->setStreet($shipping_street)->save()->setCollectShippingRates(TRUE);
    }


    if ($billing_address_id) {
      $billing_address = Mage::getModel('customer/address')->load($billing_address_id);
      $this->getOnepage()->getQuote()->getBillingAddress()->setCountryId($billing_address->getCountryId())->setCity($billing_address->getCity())->setPostcode($billing_address->getPostcode())->setRegionId($billing_address->getRegionId())->setRegion($billing_address->getRegion())->setFirstname($billing_address->getFirstname())->setLastname($billing_address->getLastname())->setCompany($billing_address->getCompany())->setFax($billing_address->getFax())->setTelephone($billing_address->getTelephone())->setStreet($billing_address->getStreet())->save();
    } else {
      $this->getOnepage()->getQuote()->getBillingAddress()->setCountryId($billing_data['country_id'])->setCity($billing_data['city'])->setPostcode($billing_data['postcode'])->setRegionId($billing_data['region_id'])->setRegion($billing_data['region'])->setFirstname($billing_data['firstname'])->setLastname($billing_data['lastname'])->setCompany($billing_data['company'])->setFax($billing_data['fax'])->setTelephone($billing_data['telephone'])->setStreet($billing_street)->save();

      if (isset($billing_data['email'])) {
        $this->getOnepage()->getQuote()->getBillingAddress()->setEmail($billing_data['email'])->save();
      }
    }

    if ($shipping_method && $shipping_method != '') {
      $this->getOnepage()->saveShippingMethod($shipping_method);
    }

    $this->getOnepage()->getQuote()->collectTotals()->save();


    $this->loadLayout(FALSE);
    $this->renderLayout();

  }

  public function change_addressAction() {
    $billing_data = $this->getRequest()->getPost('billing');
    $shipping_data = $this->getRequest()->getPost('shipping');
    $shipping_method = $this->getRequest()->getPost('shipping_method', FALSE);

    if (isset($billing_data['use_for_shipping']) && $billing_data['use_for_shipping'] == '1') {
      $shipping_data = $billing_data;
    }

    $billing_street = trim(implode("\n", $billing_data['street']));
    $shipping_street = trim(implode("\n", $shipping_data['street']));

    $billing_address_id = $this->getRequest()->getPost('billing_address_id', FALSE);
    $shipping_address_id = $this->getRequest()->getPost('shipping_address_id', FALSE);

    $this->getOnepage()->getQuote()->getShippingAddress();
    $this->loadLayout(FALSE);
    $this->renderLayout();
  }

  public function save_shipping_methodAction() {
    $shipping_method = $this->getRequest()->getPost('shipping_method', '');
    $payment_method = $this->getRequest()->getPost('payment_method', '');

    $result = $this->getOnepage()->saveShippingMethod($shipping_method);
    if ($payment_method != '') {
      try {
        $payment = $this->getRequest()->getPost('payment', array());
        $payment['method'] = $payment_method;
        $payment_result = $this->getOnepage()->savePayment($payment);
      } catch (Exception $e) {

      }
    }
    $this->getOnepage()->getQuote()->collectTotals()->save();
    $this->loadLayout(FALSE);
    $this->renderLayout();
  }

  public function isCustomerLoggedIn() {
    return (bool)Mage::getSingleton('customer/session')->isLoggedIn();
  }

  public function save_orderAction() {
    if (!$this->getRequest()->isPost()) {
      // redirect to simple checkout page
      $this->_redirect('simplecheckout');
      return;
    }

    $checkout_data = $this->getRequest()->getPost();

    $billing_data = isset($checkout_data['billing']) ? $checkout_data['billing'] : array();
    $shipping_data = isset($checkout_data['shipping']) ? $checkout_data['shipping'] : array();
    $shipping_method = isset($checkout_data['shipping_method']) ? $checkout_data['shipping_method'] : '';
    $payment_method = isset($checkout_data['payment']) ? $checkout_data['payment'] : '';

    // $billing_data = $this->getRequest()->getPost('billing', array());
    // $shipping_data = $this->getRequest()->getPost('shipping', array());
    // $shipping_method = $this->getRequest()->getPost('shipping_method', '');
    // $payment_method = $this->getRequest()->getPost('payment', '');

    if (Mage::helper('simplecheckout')->enableOrderComment()) {
      $customer_order_comment = $this->getRequest()->getPost('customer_order_comment');
      if (isset($customer_order_comment) && $customer_order_comment != '') {
        $this->getSession()->setData('customer_order_comment', $customer_order_comment);
      }
    }

    // enable Delivery 
    
    if (Mage::helper('simplecheckout')->isEnableDelivery()) {
      $deliveryData = $this->getRequest()->getPost('delivery');
      if (isset($deliveryData) && !empty($deliveryData)) {
        $this->getSession()->setData('delivery_data', $deliveryData);
      }
    }

    //end update

    //save checkout method
    if (!$this->isCustomerLoggedIn()) {
      $checkout_method = 'guest';
      if (isset($billing_data['create_new_account']) && $billing_data['create_new_account'] == '1') {
        $checkout_method = 'register';
      }
      $result = $this->getOnepage()->saveCheckoutMethod($checkout_method);
      if (isset($result['error'])) {
        $this->getSession()->setCheckoutData($checkout_data);
        $this->getSession()->addError(Mage::helper('simplecheckout')->__('There was a problem when saving your order. Please try again.'));
        $this->_redirect('simplecheckout');
        return;
      }
    }

    //re-save billing_data
    $billing_customerAddressId = $this->getRequest()->getPost('billing_address_id', FALSE);
    if (isset($billing_data['email'])) {
      $billing_data['email'] = trim($billing_data['email']);
    }
    $result = $this->getOnepage()->saveBilling($billing_data, $billing_customerAddressId);
    if (isset($result['error'])) {
      $this->getSession()->setCheckoutData($checkout_data);
      $this->getSession()->addError(Mage::helper('simplecheckout')->__('There was a problem when saving your order. Please try again.'));
      $this->_redirect('simplecheckout');
      return;
    }

    //re-save shipping_date
    if (isset($billing_data['use_for_shipping']) && $billing_data['use_for_shipping'] == '1') {
      $shipping_data = $billing_data;
    }

    if (!$this->getOnepage()->getQuote()->isVirtual()) {
      if (!$billing_data['use_for_shipping'] || $billing_data['use_for_shipping'] != '1') {
        $shipping_customerAddressId = $this->getRequest()->getPost('shipping_address_id', FALSE);
        $result = $this->getOnepage()->saveShipping($shipping_data, $shipping_customerAddressId);
        if (isset($result['error'])) {
          $this->getSession()->setCheckoutData($checkout_data);
          $this->getSession()->addError(Mage::helper('simplecheckout')->__('There was a problem when saving your order. Please try again.'));
          $this->_redirect('simplecheckout');
          return;
        }
      }
    }

    //re-save shipping_method
    if (!$this->getOnepage()->getQuote()->isVirtual()) {
      $result = $this->getOnepage()->saveShippingMethod($shipping_method);

      if (isset($result['error'])) {
        $this->getSession()->setCheckoutData($checkout_data);
        $this->getSession()->addError(Mage::helper('simplecheckout')->__('There was a problem when saving your order. Please try again.'));
        $this->_redirect('simplecheckout');
        return;
      }
    }

    //re-save payment method
    try {
      $result = $this->getOnepage()->savePayment($payment_method);
      $paymentRedirect = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();

    } catch (Mage_Payment_Exception $e) {
      if ($e->getFields()) {
        $result['fields'] = $e->getFields();
      }
      $this->getSession()->setCheckoutData($checkout_data);
      $this->getSession()->addError($e->getMessage());
      $this->_redirect('simplecheckout');
      return;
    } catch (Exception $e) {
      $this->getSession()->setCheckoutData($checkout_data);
      $this->getSession()->addError($e->getMessage());
      $this->_redirect('simplecheckout');
      return;
    }

    if ($paymentRedirect && $paymentRedirect != '') {
      Header('Location: ' . $paymentRedirect);
      exit();
    }

    //$redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
    if (!isset($result['error'])) {
      $payment_data = $this->getRequest()->getPost('payment', FALSE);
      if ($payment_data) {
        $this->getOnepage()->getQuote()->getPayment()->importData($payment_data);
      }
      try {
        $order = $this->getOnepage()->saveOrder();
        $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
      } catch (Mage_Payment_Model_Info_Exception $e) {
        Mage::logException($e);
        $this->getSession()->setCheckoutData($checkout_data);
        $this->getSession()->addError($e->getMessage());
        $this->_redirect('simplecheckout');
        return;
      } catch (Mage_Core_Exception $e) {
        Mage::logException($e);
        Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
        $this->getSession()->setCheckoutData($checkout_data);
        $this->getSession()->addError($e->getMessage());
        $this->_redirect('simplecheckout');
        return;
      } catch (Exception $e) {
        Mage::logException($e);
        Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
        $this->getSession()->setCheckoutData($checkout_data);
        $this->getSession()->addError($e->getMessage());
        $this->_redirect('simplecheckout');
        return;
      }
      $this->getOnepage()->getQuote()->save();
      if ($redirectUrl) {
        $redirect = $redirectUrl;
      } else {
        $redirect = Mage::getUrl('checkout/onepage/success');
      }

      $this->getSession()->setCheckoutData(null);

      Header('Location: ' . $redirect);
      exit();
    }

  }

  public function testAction() {
    $data = Mage::helper('checkout')->getQuote()->getAllItems();
    foreach($data as $item){
      Zend_Debug::dump($item->getData());
    }
    //tax_amount
    //shipping_amount
    //Zend_Debug::dump($data);
    die();

    $quote = Mage::getSingleton('checkout/session')->getQuote();
    $customerData = $quote->getCustomer();
    $customerData->setFirstname('Neo')->setLastname('Nguyen');

    Zend_Debug::dump($customerData);
    die();
  }

  public function loginAction() {
    $result = array();
    $error = FALSE;
    $session = Mage::getSingleton('customer/session');

    if ($post = $this->getRequest()->getPost()) {
      if (!empty($post['username']) && !empty($post['password'])) {
        try {
          $session->login($post['username'], $post['password']);
          $result = array('success' => TRUE, 'error' => FALSE);
        } catch (Mage_Core_Exception $e) {
          switch ($e->getCode()) {
            case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
              $value = Mage::helper('customer')->getEmailConfirmationUrl($post['username']);
              $message = Mage::helper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
              break;
            case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
              $message = $e->getMessage();
              break;
            default:
              $message = $e->getMessage();
          }
          $result['error'] = TRUE;
          $result['success'] = FALSE;
          $result['message'] = $message;
        } catch (Exception $e) {
          $result['error'] = TRUE;
          $result['success'] = FALSE;
          $result['message'] = $e->getMessage();
        }
      } else {
        $result['error'] = TRUE;
        $result['success'] = FALSE;
        $result['message'] = 'Login and password are required.';
      }
    }
    //$result['html'] = $this->_getLoginHtml();
    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
  }

  protected function _getLoginHtml() {
    $layout = $this->getLayout();
    $update = $layout->getUpdate();
    $update->load('simplecheckout_index_login');
    $layout->generateXml();
    $layout->generateBlocks();
    $output = $layout->getOutput();
    return $output;
  }

  // Xboy Update Coupon code
  public function save_couponcodeAction() {

    $couponCode = $this->getRequest()->getParam('coupon_code');
    $cancelCode = $this->getRequest()->getParam('cancel_code');
    $result = array();

    try {
      $codeLength = strlen($couponCode);
      $isCodeLengthValid = $codeLength && $codeLength <= Mage_Checkout_Helper_Cart::COUPON_CODE_MAX_LENGTH;
      $this->getOnepage()->getQuote()->getShippingAddress()->setCollectShippingRates(TRUE);
      $this->getOnepage()->getQuote()->setCouponCode($isCodeLengthValid ? $couponCode : '')->collectTotals()->save();

      if ($codeLength) {
        if ($isCodeLengthValid && $couponCode == $this->getOnepage()->getQuote()->getCouponCode()) {
          $result['message'] = $this->__('Coupon code "%s" was applied.', Mage::helper('core')->escapeHtml($couponCode));
          $result['success'] = TRUE;
        } else {
          $result['message'] = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($couponCode));
          $result['error'] = TRUE;
        }
      }
      if ($codeLength == "" && $cancelCode) {
        $result['message'] = $this->__('Coupon code was canceled.');
        $result['success'] = TRUE;
      }

    } catch (Mage_Core_Exception $e) {
      $result['message'] = $e->getMessage();
      $result['error'] = TRUE;
    } catch (Exception $e) {
      $result['message'] = $this->__('Cannot apply the coupon code.');
      $result['error'] = TRUE;
    }

    $this->getOnepage()->getQuote()->collectTotals()->save();
    if ($this->getOnepage()->getQuote()->getCouponCode() != "") {
      $result['cancel'] = TRUE;
    } else {
      $result['cancel'] = FALSE;
    }
    //    update review info
    $layout = $this->getLayout();
    $update = $layout->getUpdate();
    $update->load('simplecheckout_index_save_couponcode');
    $layout->generateXml();
    $layout->generateBlocks();
    $output = $layout->getOutput();
    $result['order_review'] = $output;
    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    return;
  }

  public function use_pointAction() {
    $data = $this->getRequest()->getParams();
    $session = Mage::getSingleton('checkout/session');
    if (isset($data['use_rewardpoint']) && ($data['use_rewardpoint'] == '0')) $session->setData('use_rewardpoint', '0'); else if (isset($data['number_of_point']) && ($data['number_of_point'] != '')) {
        $min_point_can_use = Mage::getStoreConfig('rewardpoint/general/minimum_point_to_redeem') ? Mage::getStoreConfig('rewardpoint/general/minimum_point_to_redeem') : 0;
        if (($data['number_of_point'] >= $min_point_can_use) && (is_numeric($data['number_of_point']))) {
          $session->setData('use_rewardpoint', '1');
          $session->setData('number_of_point', $data['number_of_point']);
        } else {
          $session->setData('use_rewardpoint', '0');
          $session->setData('number_of_point', '0');
        }
      }
      $this->getOnepage()->getQuote()->collectTotals()->save();
    $this->loadLayout(FALSE);
    $this->renderLayout();
  }

  public function qtyupAction(){
    $this->getResponse()->setHeader('Content-type', 'application/json');
    $_response = array();
    $_response['message'] = false;
    $_itemHtml = '';
    $params = $this->getRequest()->getParams();
    $itemId = $params['itemId'];
    $qtyOld = null;
    
    if(isset($itemId)){
      $qtyOld = $this->getItemQty($itemId);
      $data = array();
      $qty = $params['qty'];
      $data[$itemId]['qty'] = $qty;
      
      $cart = Mage::getSingleton('checkout/cart');
      $cart->updateItems($data)
      ->save();

      $qtyNew = $this->getItemQty($itemId);
      if($qtyOld != $qtyNew){
        $_response['message'] = true;
        $_response['qty'] = $qtyNew;
        $_response['subtotal'] = '<span class="price">'.Mage::helper('core')->currency($this->getRowTotal($itemId)).'</span>';
        
        $block = Mage::app()->getLayout()->createBlock('checkout/cart_totals');
        $_colspan = $block->helper('tax')->displayCartBothPrices() ? 5 : 3;
        $data = '';
        $data .= $block->renderTotals(null, $_colspan);
        $data .= $block->renderTotals('footer', $_colspan);
        if ($block->needDisplayBaseGrandtotal()){
          $data .= '<tr>';
          $data .= '<td class="a-right" colspan="'.$_colspan.'">';
          $data .= '<small>'.$block->helper('sales')->__('Your credit card will be charged for').'</small>';
          $data .= '</td>';
          $data .= '<td class="a-right">';
          $data .= '<small>'.$block->displayBaseGrandtotal().'</small>';  
          $data .= '</td>';
          $data .= '</tr>';
        }
        $_response['totals'] = $data;
        
      }
    }
    
    $this->getResponse()->setBody(json_encode($_response));
  }
  
  public function getItemQty($itemId){
    $itemsOfQuote = $this->getSession()->getQuote()->getAllItems();
    $qty = null;
    foreach($itemsOfQuote as $item){
      if($item->getItemId() == $itemId){
        $qty = $item->getQty();
      }
    }
    return $qty;
  }
  
  public function getRowTotal($itemId){
    $itemsOfQuote = $this->getSession()->getQuote()->getAllItems();
    $total = null;
    foreach($itemsOfQuote as $item){
      if($item->getItemId() == $itemId){
        $total = $item->getRowTotal();
      }
    }
    return $total;
  }
}