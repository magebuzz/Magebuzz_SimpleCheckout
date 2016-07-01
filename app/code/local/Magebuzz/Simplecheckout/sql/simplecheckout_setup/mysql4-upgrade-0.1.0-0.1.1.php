<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/

$installer = $this;
$installer->startSetup();

//create order comment 
$entity_type = Mage::getSingleton("eav/entity_type")->loadByCode("order");
$entity_type_id = $entity_type->getId();
$collection = Mage::getModel("eav/entity_attribute")->getCollection()->addFieldToFilter("entity_type_id", $entity_type_id)->addFieldToFilter("attribute_code", "customer_order_comment");

if (!count($collection)) {
  $attribute = Mage::getModel("eav/entity_attribute");
  $data = array();
  $data['id'] = null;
  $data['entity_type_id'] = $entity_type_id;
  $data['attribute_code'] = "customer_order_comment";
  $data['backend_type'] = "text";
  $data['frontend_input'] = "textarea";
  $data['frontend_label'] = 'Customer Order Comment';
  $data['is_global'] = "1";
  $data['is_visible'] = "1";
  $data['is_required'] = "0";
  $data['is_user_defined'] = "0";
  $attribute->setData($data);
  $attribute->save();
}

$installer->endSetup(); 