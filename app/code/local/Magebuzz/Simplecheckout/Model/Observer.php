<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Simplecheckout_Model_Observer {
  public function saveOrderComment($observer) {
    $_order = $observer->getEvent()->getOrder();     
    $customerComment = Mage::getSingleton('checkout/session')->getData('customer_order_comment');

    if ($customerComment != "") {
      try {
        $_order->setCustomerOrderComment($customerComment)->setCustomerNote($customerComment)->save();
      } catch (Exception $e) {
        //Silence is gold
      }
    }
  }

  public function saveOrderAfter($observer){
    $_order = $observer->getEvent()->getOrder();    
    if (Mage::helper('simplecheckout')->isEnableDelivery() && Mage::getSingleton('checkout/session')->getData('delivery_data')) { 
      $deliveryData =  Mage::getSingleton('checkout/session')->getData('delivery_data');
      if(!empty($deliveryData)){
        $orderExist = Mage::getModel('simplecheckout/delivery')->getCollection()
        ->addFieldToFilter('sales_order_id',$_order->getId());
        if(!$orderExist->getSize()){
          $deliveryModel = Mage::getModel('simplecheckout/delivery');
          $deliveryModel->setSalesOrderId($_order->getId());
          $deliveryModel->setDeliveryComment($deliveryData['comments']);
          $deliveryModel->setDeliveryDate($deliveryData['date']);
          if(isset($deliveryData['timerange'])){
            $deliveryModel->setDeliveryTime($deliveryData['timerange']);           
          }
          $deliveryModel->save(); 
        }   
        Mage::getSingleton('core/session')->setData('delivery_data',null);  
      }
    }
  }

  public function loadOrderAfter($observer){           
    $_order =  $observer->getEvent()->getOrder();    
    $orderId = $_order->getId()    ;
    $deliveryBlock = null;
    if($orderId){
      $delivery = Mage::getModel('simplecheckout/delivery')->load($orderId,'sales_order_id');       
      if($delivery->getId()){        
        $deliveryBlock = $this->deliveryHtml($delivery);
      }else{
        $deliveryBlock ="";    
      }       
      $_order->setDeliveryHtml($deliveryBlock) ;     
    }    
    return $_order;
  }

  public function deliveryHtml($delivery){    
    $block = Mage::getBlockSingleton('core/template')->setTemplate('simplecheckout/sales/order/delivery_method.phtml');
    $block->setDelivery($delivery);      
    return $block->toHtml();    
  }

}