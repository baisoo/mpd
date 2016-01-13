<?php
/**
 * @category    Mpd
 * @package     Mpd_Groupscatalog
 * @author      Tangkoko <support@tangkoko.com>
 * @copyright   Copyright (c) 2016 Tangkoko (http://www.tangkoko.com)
 * @license     All rights reserved
 */

class Mpd_Groupscatalog_Helper_Data extends Netzarbeiter_GroupsCatalog2_Helper_Data
{
    /**
     * Return the customer id of the current customer
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        return Mage::getSingleton('customer/session')->getData('mpd_group_visibility');
    }
}