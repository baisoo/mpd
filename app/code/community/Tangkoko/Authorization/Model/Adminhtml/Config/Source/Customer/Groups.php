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

class Tangkoko_Authorization_Model_Adminhtml_Config_Source_Customer_Groups
{

    public function toOptionArray()
    {
        $groups = Mage::getResourceModel('customer/group_collection')
            ->loadData()
            ->toOptionArray();
        return $groups;
    }
}