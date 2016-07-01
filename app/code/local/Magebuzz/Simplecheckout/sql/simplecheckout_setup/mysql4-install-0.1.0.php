<?php
/*
* Copyright (c) 2014 www.magebuzz.com
*/
$installer = $this;
$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('sales/order')} ADD `customer_order_comment` text null;

");

$installer->endSetup(); 