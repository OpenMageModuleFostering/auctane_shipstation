<?php
 
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
 
$quoteTable = $installer->getTable('sales/quote');

$installer->getConnection()
    ->addColumn($quoteTable, 'auctaneapi_discounts', 'text default NULL');

$orderTable = $installer->getTable('sales/order');

$installer->getConnection()
    ->addColumn($orderTable, 'auctaneapi_discounts', 'text default NULL');

$installer->endSetup();
