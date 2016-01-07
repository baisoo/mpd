<?php
/**
 * Configuration Source CMS Pages
 * @category    Tangkoko
 * @package     Tangkoko_Authorization
 * @author      Tangkoko <support@tangkoko.com>
 * @copyright   Copyright (c) 2015 Tangkoko (http://www.tangkoko.com)
 * @license     All rights reserved
 */

class Tangkoko_Authorization_Model_Adminhtml_Config_Source_Cms_Pages
{

    public function toOptionArray()
    {
        $groups = Mage::getResourceModel('cms/page_collection')
            ->loadData()
            ->toOptionIdArray();
        return $groups;
    }
}