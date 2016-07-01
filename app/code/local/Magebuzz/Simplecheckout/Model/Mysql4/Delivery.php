<?php
/*
* @copyright   Copyright (c) 2014 www.magebuzz.com
*/
class Magebuzz_Simplecheckout_Model_Mysql4_Delivery extends Mage_Core_Model_Mysql4_Abstract
{
  public function _construct()
  {    
    $this->_init('simplecheckout/delivery', 'delivery_id');
  }
}