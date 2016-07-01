<?php

/*
* Copyright (c) 2015 www.magebuzz.com
*/

class Magebuzz_Simplecheckout_Model_System_Config_Choosestyle {

  public function toOptionArray()
  {
    return array(
    array('value'=>'default', 'label'=>Mage::helper('adminhtml')->__('Default')),
    array('value'=>'blue', 'label'=>Mage::helper('adminhtml')->__('Blue')),
    array('value'=>'orange', 'label'=>Mage::helper('adminhtml')->__('Orange')),
    );
  }
}
