<?php

/*
* Copyright (c) 2014 www.magebuzz.com
*/

class Magebuzz_Simplecheckout_Model_Ajaxfields {
  public function toOptionArray() {
    $fields = array('Country', 'Postcode', 'State/region', 'City');
    $options = array();
    foreach ($fields as $field) {
      $options[] = array('label' => $field, 'value' => strtolower($field));
    }
    return $options;
  }
}