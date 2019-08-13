<?php
/**
 * ShipStation
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@auctane.com so we can send you a copy immediately.
 *
 * @category   Shipping
 * @package    Auctane_ShipStation
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/* @var $this Mage_Core_Model_Resource_Setup */

$this->startSetup();

$this->run("

DROP TABLE IF EXISTS {$this->getTable('auctaneshipstation_user')};
CREATE TABLE {$this->getTable('auctaneshipstation_user')} (
  `entity_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `store_id` SMALLINT(5) NOT NULL DEFAULT 0 COMMENT 'Store 0 is admin',
  `admin_user_id` MEDIUMINT(9) UNSIGNED,
  `request_url` VARCHAR(255),
  `request_username` VARCHAR(255),
  `auth_token_enc` VARCHAR(255),
  `auth_url` VARCHAR(255),
  PRIMARY KEY (`entity_id`),
  UNIQUE KEY (`admin_user_id`, `store_id`)
--  CONSTRAINT `FK_shipstation_core_store_id` FOREIGN KEY (`store_id`)
--    REFERENCES {$this->getTable('core_store')} (`store_id`)
--    ON DELETE CASCADE ON UPDATE CASCADE,
--  CONSTRAINT `FK_shipstation_admin_user_id` FOREIGN KEY (`admin_user_id`)
--    REFERENCES {$this->getTable('admin_user')} (`user_id`)
--    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$this->endSetup();
