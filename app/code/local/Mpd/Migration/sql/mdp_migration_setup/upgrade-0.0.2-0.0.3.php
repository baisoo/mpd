<?php
/**
 * Create new class tax id and manage customer groups and visibility_group client attribute
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

// Create new tax class : General and Retail Customer without VAT ID
$taxClass = array("General","Retail Customer without VAT ID");
foreach($taxClass as $tax)
{
    $taxClass = Mage::getModel('tax/class');
    $taxClass->load($tax,'class_name');

    // If does not exist
    if (!$taxClass->getId()) {
        $taxClass->setClassName($tax);
        $taxClass->setClassType('CUSTOMER');
        $taxClass->save();
        Mage::log('Save Tax class : '.$tax, null, null, true);
    }
}

// Create customer groups
$codes = array('PRO_WAITING','PRO_PRICE_10','PRO_PRICE_10_NOVATID','PRO_PRICE_20','PRO_PRICE_25','PRO_PRICE_35','PRO_ACCESS_GENERAL','PRO_ACCESS_SITEL','PRO_ACCESS_<>');
$retailTaxClass = Mage::getModel('tax/class')->load('Retail Customer','class_name');
$retailTaxClassNoVAT = Mage::getModel('tax/class')->load('Retail Customer without VAT ID','class_name');

foreach ($codes as $code)
{
    // Create model and attempt to load
    $customerGroup = Mage::getModel('customer/group');
    $customerGroup->load($code,'customer_group_code');

    // If does not exist
    if (!$customerGroup->getId()) {
        $customerGroup->setCode($code);
        if($code == "PRO_PRICE_10_NOVATID"){
            $customerGroup->setTaxClassId($retailTaxClassNoVAT->getId());
        } else {
            $customerGroup->setTaxClassId($retailTaxClass->getId());
        }
        $customerGroup->save();
        Mage::log('Save Customer group : '.$code, null, null, true);
    }

}

/**
// Create visibility_group attribute for customers
$entity = $this->getEntityTypeId('customer');
$this->addAttribute($entity, 'visibility_group', array(
    'label'             => 'Visibility Group',
    'visible'           => true,
    'required'          => true,
    'type'              => 'int',
    'input'             => 'select',
    'source'            => 'customer/customer_attribute_source_group',
    'default_value' => '',
    'is_user_defined'   => 0,
    'adminhtml_only'    => 1
));

$forms = array(
    'adminhtml_customer'
);
$attribute = Mage::getSingleton('eav/config')->getAttribute($this->getEntityTypeId('customer'), 'visibility_group');
$attribute->setData('used_in_forms', $forms);
$attribute->save();
**/

Mage::log('end migration', null, null, true);
$this->endSetup();