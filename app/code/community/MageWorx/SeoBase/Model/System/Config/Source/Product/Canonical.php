<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Model_System_Config_Source_Product_Canonical
{

    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = array(
                array('label' => Mage::helper('seosuite')->__('URLs by Path Length'), 'value' => array(
                        array('value' => '1', 'label' => Mage::helper('seosuite')->__('Use Longest')),
                        array('value' => '2', 'label' => Mage::helper('seosuite')->__('Use Shortest')),
                    )
                ),
                array('label' => Mage::helper('seosuite')->__('URLs by Categories Counter'), 'value' => array(
                        array('value' => '4', 'label' => Mage::helper('seosuite')->__('Use Longest')),
                        array('value' => '5', 'label' => Mage::helper('seosuite')->__('Use Shortest')),
                    )
                ),
                array('value' => '3', 'label' => Mage::helper('seosuite')->__('Use Root'))
            );
        }
        // echo "<pre>"; print_r($this->_options); exit;
        return $this->_options;
    }

}