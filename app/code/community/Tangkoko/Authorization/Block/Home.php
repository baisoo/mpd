<?php

/**
* Cms page content block
*
* @category    Tangkoko
* @package     Tangkoko_Authorization
* @author      Olivier Michaud <omichaud@tangkoko.com>
* @copyright   Copyright (c) 2015 Tangkoko (http://www.tangkoko.com)
* @license     All rights reserved
*/

class Tangkoko_Authorization_Block_Home extends Mage_Core_Block_Template
{

    /**
     * Display Home content for authorized customer or not
     *
     * @return string
     */
    public function getHomeContent() {
        $session = Mage::getSingleton('customer/session');
        /* @var $helper Tangkoko_Authorization_Helper_Store */
        $helper = Mage::helper('tangkoko_authorization/store');

        //load default home page
        $identifier = Mage::getStoreConfig("web/default/cms_home_page");

        // Check if customer is authorized load B2B page
        if ($helper->isAuthorized($session->getCustomer())) {
            $identifier = Mage::getStoreConfig("store_authorization/general/home_page");
        }

        $page = Mage::getModel('cms/page');
        $page->setStoreId(Mage::app()->getStore()->getId());
        $page->load($identifier, 'identifier');

        $helper = Mage::helper('cms');
        $processor = $helper->getPageTemplateProcessor();
        $html = $processor->filter($page->getContent());

        return $html;
    }
}