<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * @category    Tangkoko
 * @package     Tangkoko_AdminTools
 * @author      Olivier Michaud
 * @copyright   Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Tangkoko_AdminTools_Block_Adminhtml_Login extends Mage_Adminhtml_Block_Template
{
    protected function _construct()
    {
        parent::_construct();

        $activeConfigValue = Mage::getStoreConfig('tangkoko_admintools/login/login_active');
        $ipConfigValue = Mage::getStoreConfig('tangkoko_admintools/login/login_ip');
        $remoteAddr = Mage::helper('core/http')->getRemoteAddr();
        $ipArray = array();

        if(!empty($ipConfigValue)) {
            $ipArray = explode(',', $ipConfigValue);
            $ipArray = array_map('trim', $ipArray);
        }

        if ($activeConfigValue == 1 && in_array($remoteAddr, $ipArray)) {
            $this->setTemplate("tangkoko/admintools/login.phtml");
        }
        else {
            $this->setTemplate("login.phtml");
        }
    }
}