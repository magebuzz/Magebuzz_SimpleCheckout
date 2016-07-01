<?php
/*
* @copyright   Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Simplecheckout_Model_Delivery extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('simplecheckout/delivery');
    }
    
    
}