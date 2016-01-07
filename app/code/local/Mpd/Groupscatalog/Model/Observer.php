<?php
/**
 * @category    Mpd
 * @package     Mpd_Groupscatalog
 * @author      Tangkoko <support@tangkoko.com>
 * @copyright   Copyright (c) 2016 Tangkoko (http://www.tangkoko.com)
 * @license     All rights reserved
 */

class Mpd_Groupscatalog_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Allow to add customer visibility_group attribute in session
     *
     * @param $observer
     */
    public function addVisibilityGroupInSession($observer)
    {
        Mage::log(__METHOD__);
        $customerSession = $observer->getEvent()->getCustomerSession();
        $customer  = Mage::getModel('customer/customer')->load($customerSession->getId());
        $attr = $customer->getData('visibility_group');
        $customerSession->setVisibilityGroup($attr);
        Mage::log("trt");
    }
}