<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Simplecheckout_Model_System_Config_Delivery_Formatdate {

  public function toOptionArray()
  {
    return array(
    array('value'=>'mm/dd/yy', 'label'=>Mage::helper('adminhtml')->__('mm/dd/yyyy')),
    array('value'=>'dd/mm/yy', 'label'=>Mage::helper('adminhtml')->__('dd/mm/yyyy')),
    );
  }
}
