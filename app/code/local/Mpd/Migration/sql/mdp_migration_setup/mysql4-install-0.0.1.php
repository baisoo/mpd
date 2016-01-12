<?php
/**
 * Create new store view for pro customer
 * @category    Mpd
 * @package     Mpd_Migration
 * @author      Tangkoko <support@tangkoko.com>
 * @copyright   Copyright (c) 2016 Tangkoko (http://www.tangkoko.com)
 * @license     All rights reserved
 */

/* @var $this Mage_Catalog_Model_Resource_Setup */
/* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

Mage::log('start migration', null, null, true);
$this->startSetup();

// Create pro store view
/** @var $store Mage_Core_Model_Store */
$store = Mage::getModel('core/store');
Mage::log('create new store view', null, null, true);
$store->setCode('fr_pro')
    ->setWebsiteId(1)
    ->setGroupId(1)
    ->setName('Professionnels')
    ->setIsActive(1)
    ->save();

// Update first store view name and code
$store = Mage::getModel('core/store')->load(1);
Mage::log('update default store view information', null, null, true);
$store->setCode('fr')
    ->setName('Particuliers')
    ->save();


Mage::log('end migration', null, null, true);
$this->endSetup();