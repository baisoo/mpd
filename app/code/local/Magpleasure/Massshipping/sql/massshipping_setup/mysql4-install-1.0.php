<?php
/**
 * MagPleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE-CE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE-CE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * MagPleasure does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Magpleasure does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   MagPleasure
 * @package    Magpleasure_Massshipping
 * @version    1.0.3
 * @copyright  Copyright (c) 2012-2013 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('mp_ms_cells')};
DROP TABLE IF EXISTS {$this->getTable('mp_ms_rows')};
DROP TABLE IF EXISTS {$this->getTable('mp_ms_columns')};
DROP TABLE IF EXISTS {$this->getTable('mp_ms_quotes')};

CREATE TABLE IF NOT EXISTS `{$this->getTable('mp_ms_quotes')}`(
  `quote_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(10) UNSIGNED NOT NULL,
  `status` SMALLINT(1) UNSIGNED NOT NULL,
  `carrier_match` TEXT,
  `created_at` TIMESTAMP NOT NULL,
  `updated_at` TIMESTAMP NOT NULL,
  PRIMARY KEY (`quote_id`)
) ENGINE=INNODB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('mp_ms_rows')}`(
  `row_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quote_id` INT(10) UNSIGNED NOT NULL,
  `status` SMALLINT(1) UNSIGNED NOT NULL,
  `message` tinytext,
  PRIMARY KEY (`row_id`),
  CONSTRAINT `FK_MP_MASSSHIPPING_ROW_QUOTE` FOREIGN KEY (`quote_id`) REFERENCES `{$this->getTable('mp_ms_quotes')}`(`quote_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=INNODB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('mp_ms_columns')}`(
  `column_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quote_id` int(10) unsigned NOT NULL,
  `data_key` varchar(255) DEFAULT NULL,
  `match_key` varchar(255) DEFAULT NULL,
  `is_resolved` smallint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`column_id`),
  CONSTRAINT `FK_MP_MASSSHIPPING_COLUMN_QUOTE` FOREIGN KEY (`quote_id`) REFERENCES `{$this->getTable('mp_ms_quotes')}`(`quote_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=INNODB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('mp_ms_cells')}`(
  `cell_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `column_id` INT(10) UNSIGNED NOT NULL,
  `row_id` INT(10) UNSIGNED NOT NULL,
  `value` TINYTEXT,
  PRIMARY KEY (`cell_id`),
  CONSTRAINT `FK_MP_MASSSHIPPING_CELL_COLUMN` FOREIGN KEY (`column_id`) REFERENCES `{$this->getTable('mp_ms_columns')}`(`column_id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `FK_MP_MASSSHIPPING_CELL_ROW` FOREIGN KEY (`row_id`) REFERENCES `{$this->getTable('mp_ms_rows')}`(`row_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=INNODB CHARSET=utf8;

");

$installer->endSetup();