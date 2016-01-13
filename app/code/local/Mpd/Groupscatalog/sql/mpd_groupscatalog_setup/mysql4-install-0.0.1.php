<?php
/**
 * Create new customer attribute : mpd_group_visibility
 *
 * @category    Mpd
 * @package     Mpd_Groupscatalog
 * @author      Tangkoko <support@tangkoko.com>
 * @copyright   Copyright (c) 2016 Tangkoko (http://www.tangkoko.com)
 * @license     All rights reserved
 */

/* @var $this Mage_Catalog_Model_Resource_Setup */
/* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */


$this->startSetup();


Mage::log('start', null, null, true);
$attribute = Mage::getSingleton('eav/config')->getAttribute($this->getEntityTypeId('customer'), 'visibility_group');
if($attribute->getId())
{
    Mage::log('Delete old visibility_group', null, null, true);
    $attribute->delete();
}


// Create visibility_group attribute for customers
$entity = $this->getEntityTypeId('customer');
$this->addAttribute($entity, 'mpd_group_visibility', array(
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
$attribute = Mage::getSingleton('eav/config')->getAttribute($this->getEntityTypeId('customer'), 'mpd_group_visibility');
$attribute->setData('used_in_forms', $forms);
$attribute->save();

Mage::log('end', null, null, true);
$this->endSetup();