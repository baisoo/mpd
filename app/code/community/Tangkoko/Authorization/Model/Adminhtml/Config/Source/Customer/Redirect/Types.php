<?php
/**
 * Configuration Source Customer Groups
 *
 * @category    Tangkoko
 * @package     Tangkoko_Authorization
 * @author      Tangkoko <support@tangkoko.com>
 * @copyright   Copyright (c) 2015 Tangkoko (http://www.tangkoko.com)
 * @license     All rights reserved
 */

class Tangkoko_Authorization_Model_Adminhtml_Config_Source_Customer_Redirect_Types extends Varien_Object
{

    public function toOptionArray()
    {
        $result = array(
            array(
                'value' => Tangkoko_Authorization_Helper_Store::CUSTOM_REDIRECT,
                'label' => Mage::helper('tangkoko_authorization')->__('URL')
            ),
            array(
                'value' => Tangkoko_Authorization_Helper_Store::PAGE_REDIRECT,
                'label' => Mage::helper('tangkoko_authorization')->__('Page'),
            ),
        );

        if (strpos($this->getPath(), 'anonymous') !== false) {
            $result[] = array(
                'value' => Tangkoko_Authorization_Helper_Store::LOGIN_REDIRECT,
                'label' => Mage::helper('tangkoko_authorization')->__('Login'),
            );
        }

        return $result;
    }
}