<?php
/**
 * Configure different store view authorization and url
 *
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

// configure store URL depending on environments
$mpdtUrl = "http://www.mespiecesdetachees.com/fr/";
$mpdtProUrl = "http://www.mespiecesdetachees.com/fr_pro/";
if (isset($_SERVER['MAGE_ENV'])) {
    if ($_SERVER['MAGE_ENV'] == "development") {
        $mpdtUrl = "http://dev.mpdt.com/fr/";
        $mpdtProUrl = "http://dev.mpdt.com/fr_pro/";
    } elseif ($_SERVER['MAGE_ENV'] == "test") {
        $mpdtUrl = "http://mpdt.test.tangkoko.net/fr/";
        $mpdtProUrl = "http://mpdt.test.tangkoko.net/fr_pro/";
    }
}
$store = Mage::getModel('core/store')->load('fr');
$storeId = $store->getId();

$mpdtProStore = Mage::getModel('core/store')->load('fr_pro');
$mpdtProStoreId = $mpdtProStore->getId();


$this->setConfigData('web/url/use_store', '1');


// Configuration Tangkoko Authorization module
$this->setConfigData('store_authorization/general/active', '1', 'stores', $mpdtProStoreId);
$this->setConfigData('store_authorization/general/error_message', 'Votre compte est en attente de validation ou n\'est pas un compte professionnel. Vous n\'avez donc pas accès à la partie professionnelle du site. Merci de naviguer sur la version <a href="'.$mpdtUrl.'">particuliers</a> du site.', 'stores', $mpdtProStoreId);


Mage::log('end migration', null, null, true);
$this->endSetup();