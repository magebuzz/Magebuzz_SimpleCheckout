<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Simplecheckout_Block_System_Config_Timerange extends Mage_Adminhtml_Block_System_Config_Form_Field_Regexceptions {
  public function __construct()
  {
    $this->addColumn('starttime', array(
    'label' => Mage::helper('adminhtml')->__('Start Time '),
    'style' => 'width:120px',    
    ));
    $this->addColumn('endtime', array(
    'label' => Mage::helper('adminhtml')->__('End Time'),
    'style' => 'width:120px',    
    ));

	  $this->addColumn('disabled_time_for_day', array(
		  'label' => Mage::helper('adminhtml')->__('Disable Date or Time Slot for this day'),
		  'style' => 'width:150px',
	  ));
	  $this->addColumn('disabled_time_slot', array(
		  'label' => Mage::helper('adminhtml')->__('Disable Time Slot'),
		  'style' => 'width:120px',
	  ));
    
    $this->_addAfter = false;
    $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Time Range');
    Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract::__construct(); 
  }
}