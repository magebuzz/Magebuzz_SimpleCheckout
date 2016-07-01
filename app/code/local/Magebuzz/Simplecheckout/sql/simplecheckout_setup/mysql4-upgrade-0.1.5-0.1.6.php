<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('simplecheckout_delivery')};
CREATE TABLE {$this->getTable('simplecheckout_delivery')} (
  `delivery_id` int(11) unsigned NOT NULL auto_increment,
  `sales_order_id` int(11) unsigned NOT NULL,
  `delivery_comment` varchar(255) default '',
  `delivery_date` varchar(30) default '',
  `delivery_time` varchar(30) default '',
  `status` smallint(6) default '0',
  PRIMARY KEY (`delivery_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 