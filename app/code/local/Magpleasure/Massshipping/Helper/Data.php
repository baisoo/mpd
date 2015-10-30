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

class Magpleasure_Massshipping_Helper_Data extends Mage_Core_Helper_Abstract
{
    const JS_OBJECT_NAME = 'MassShip';

    public function getJsObjectName()
    {
        return self::JS_OBJECT_NAME;
    }

    /**
     * Common Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    public function getCommon()
    {
        return Mage::helper('magpleasure');
    }

    /**
     * User
     *
     * @return bool|Magpleasure_Massshipping_Model_Quote
     */
    public function getUser()
    {
        /** @var Mage_Admin_Model_Session $session  */
        $session = Mage::getSingleton('admin/session');
        if ($session->isLoggedIn()) {
            return $session->getUser();
        }
        return false;
    }

    /**
     * Retrives global timezone
     * @return string
     */
    public function getTimezone()
    {
        return Mage::app()->getStore()->getConfig('general/locale/timezone');
    }

    public function getTimezoneOffset()
    {
        $date = new Zend_Date();
        $date->setTimezone($this->getTimezone());
        return $date->getGmtOffset();
    }


}