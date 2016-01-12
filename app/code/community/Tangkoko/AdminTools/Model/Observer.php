<?php
/**
 * @category    Tangkoko
 * @package     Tangkoko_AdminTools
 * @copyright   Copyright (c) 2015 Tangkoko (http://www.tangkoko.com)
 * @license     All rights reserved
 */
class Tangkoko_AdminTools_Model_Observer
{
    /**
     * Set config in php.ini accoprding to store configuration
     * @param Varien_Event_Observer $observer
     */
    public function setPhpIniConfig(Varien_Event_Observer $observer)
    {
        if (Mage::getStoreConfig('tangkoko_admintools/phpini/phpini_active')) {
            $memoryLimit = Mage::getStoreConfig('tangkoko_admintools/phpini/memory_limit');
            ini_set("memory_limit", $memoryLimit . "M");

            $maxExecutionTime = Mage::getStoreConfig('tangkoko_admintools/phpini/max_execution_time');
            ini_set("max_execution_time", $maxExecutionTime);
        }
    }
}