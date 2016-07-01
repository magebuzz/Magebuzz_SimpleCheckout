<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Simplecheckout_Block_Sales_Order_Info extends Mage_Sales_Block_Order_Info
{
  protected function _construct()
  {
    parent::_construct();
    $this->setTemplate('simplecheckout/sales/order/info.phtml');
  }

  public function deliveryHtml(){  
    $orderId = $this->getOrder()->getId();
    $block = Mage::getBlockSingleton('simplecheckout/sales_order_delivery')->setTemplate('simplecheckout/sales/order/delivery_method.phtml') ;
    $block->setOrderId($orderId);

    return $block->toHtml();    
  }

}
