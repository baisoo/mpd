<?php
/**
 * Create new attributes and attrubute sets for migration
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

$attributes = array(
    'mpd_salable' => array(
        'label'                     => 'Disponible à la vente',
        'input'                     => 'select',
        'type'                      => 'int',
        'required'                  => true,
        'filterable'                => false,
        'filterable_in_search'      => false,
        'is_searchable'             => false,
        'visible_on_front'          => false,
        'used_in_product_listing'   => false,
        'backend'                   => '',
        'option'                    => '',
        'source'                    => 'eav/entity_attribute_source_boolean',
        'global'                    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
    ),
    'mpd_deee' => array(
        'label'                     => 'Eco-taxe',
        'input'                     => 'price',
        'type'                      => 'decimal',
        'required'                  => false,
        'filterable'                => false,
        'filterable_in_search'      => false,
        'is_searchable'             => false,
        'visible_on_front'          => true,
        'used_in_product_listing'   => true,
        'backend'                   => 'catalog/product_attribute_backend_price',
        'option'                    => '',
        'source'                    => '',
        'global'                    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    ),
    'mpd_order_item_file_comment' => array(
        'label'                     => 'Preuve d\'achat obligatoire',
        'input'                     => 'select',
        'type'                      => 'int',
        'required'                  => true,
        'filterable'                => false,
        'filterable_in_search'      => false,
        'is_searchable'             => false,
        'visible_on_front'          => false,
        'used_in_product_listing'   => false,
        'backend'                   => '',
        'option'                    => '',
        'source'                    => 'eav/entity_attribute_source_boolean',
        'global'                    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
    ),
    'mpd_manufacturer' => array(
        'label'                     => 'Constructeur',
        'type'                      => 'varchar',
        'input'                     => 'select',
        'required'                  => true,
        'filterable'                => true,
        'filterable_in_search'      => true,
        'is_searchable'             => true,
        'visible_on_front'          => true,
        'used_in_product_listing'   => true,
        'source'                    => '',
        'backend'                   => 'eav/entity_attribute_backend_array',
        'source'                    => 'eav/entity_attribute_source_table',
        'option'                    => array('value' => array(
                'apple' => array(0 => 'Apple'),
                'blackberry' => array(0 => 'BlackBerry'),
                'kazam' => array(0 => 'Kazam'),
                'lg' => array(0 => 'LG'),
                'motorola' => array(0 => 'Motorola'),
                'nokia' => array(0 => 'Nokia'),
                'philips' => array(0 => 'Philips'),
                'samsung' => array(0 => 'Samsung'),
                'sony_ericsson' => array(0 => 'Sony Ericsson'),
                'yezz' => array(0 => 'Yezz')
            )),
        'global'                    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
        ),
    'mpd_type' => array(
        'label'                     => 'Type',
        'type'                      => 'varchar',
        'input'                     => 'select',
        'required'                  => false,
        'filterable'                => false,
        'filterable_in_search'      => false,
        'is_searchable'             => false,
        'visible_on_front'          => true,
        'used_in_product_listing'   => true,
        'backend'                   => 'eav/entity_attribute_backend_array',
        'source'                    => '',
        'option'                    => array('value' => array(
                'reparable' => array(0 => 'Réparable'),
                'echangeable' => array(0 => 'Echangeable'),
                'reparable_centre_agree' => array(0 => 'Réparable dans centre agréé')
            )),
        'global'                    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
    )
);

foreach ($attributes as $code => $attribute) {
    Mage::log('add attribute '.$code, null, null, true);
    $this->addAttribute(Mage_Catalog_Model_Product::ENTITY, $code, array(
        'group'                     => '',
        'type'                      => $attribute['type'],
        'source'                    => $attribute['source'],
        'label'                     => $attribute['label'],
        'input'                     => $attribute['input'],
        'backend'                   => $attribute['backend'],
        'global'                    => $attribute['global'],
        'visible'                   => true,
        'visible_in_advanced_search'=> false,
        'comparable'                => false,
        'filterable'                => $attribute['filterable'],
        'filterable_in_search'      => $attribute['filterable_in_search'],
        'required'                  => $attribute['required'],
        'user_defined'              => true,
        'default'                   => '',
        'visible_on_front'          => $attribute['visible_on_front'],
        'used_in_product_listing'   => $attribute['used_in_product_listing'],
        'option'                    => $attribute['option'],
    ));
}

$entityType = Mage_Catalog_Model_Product::ENTITY;
$entityTypeId = $this->getEntityTypeId($entityType);

$entityType = Mage::getModel('eav/entity_type')->loadByCode(Mage_Catalog_Model_Product::ENTITY);
$entityTypeId = $entityType->getId();

$defaultAttributeSetId = $entityType->getDefaultAttributeSetId();

$attributeSets = array('Produit fini', 'Pièce - accessoire');

foreach ($attributeSets as $attributeSetName) {
    Mage::log('create set '.$attributeSetName, null, null, true);
    //Create attribute set
    $attributeSet = Mage::getModel('eav/entity_attribute_set');
    $attributeSet->setEntityTypeId($entityTypeId);
    $attributeSet->setAttributeSetName($attributeSetName);
    $attributeSet->save();
    $attributeSet->initFromSkeleton($defaultAttributeSetId);
    $attributeSet->save();

    //Create attribute group
    $attributeGroup = Mage::getModel('eav/entity_attribute_group');
    $attributeGroup->setAttributeGroupName('Attributs Mpd');
    $attributeGroup->setAttributeSetId($attributeSet->getId());
    $attributeGroup->save();

    // Add attributes to Mpd in attribute set
    foreach ($attributes as $attributeCode => $attributeValues) {
        if(!(($attributeCode == 'mpd_type' || $attributeCode == 'mpd_salable') && $attributeSetName == 'Pièce - accessoire')){
            $attributeId = $this->getAttributeId(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
            $this->addAttributeToSet(Mage_Catalog_Model_Product::ENTITY, $attributeSet->getId(), $attributeGroup->getId(), $attributeId);
        }
    }
}

Mage::log('end migration', null, null, true);
$this->endSetup();