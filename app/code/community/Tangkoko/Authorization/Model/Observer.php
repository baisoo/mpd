<?php
/**
 * Authorization Observer
 *
 * @category    Tangkoko
 * @package     Tangkoko_Authorization
 * @author      Tangkoko <support@tangkoko.com>
 * @copyright   Copyright (c) 2015 Tangkoko (http://www.tangkoko.com)
 * @license     All rights reserved
 */

class Tangkoko_Authorization_Model_Observer
{
    /**
     * Called before action dispatched
     *
     * @param Varien_Event_Observer $observer
     * @return Tangkoko_Authorization_Model_Observer
     */
    public function onFrontendActionDispatch($observer)
    {
        if (!Mage::helper('tangkoko_authorization/store')->isActive()) {
            return $this;
        }

        /* @var $action Mage_Core_Controller_Varien_Action */
        $action = $observer->getEvent()->getControllerAction();

        $this->_authorizeFrontendAccess($action);

        return $this;
    }

    /**
     * Authorize store access
     *
     * @param Mage_Core_Controller_Varien_Action $action
     */
    protected function _authorizeFrontendAccess($action)
    {
        /* @var $session Mage_Customer_Model_Session */
        $session = Mage::getSingleton('customer/session');
        /* @var $helper Tangkoko_Authorization_Helper_Store */
        $helper = Mage::helper('tangkoko_authorization/store');

        // Check if customer is authorized
        if (!$helper->isAuthorized($session->getCustomer())) {

            $request = $action->getRequest();
            $path = $action->getFullActionName('/');

            $allowedActions = $helper->getPublicActions();
            if ($allowedActions != "" && preg_match($allowedActions, $path)) {
                // Do not redirect on allowed actions
                return;
            } elseif (preg_match('#^cms/#', $path)) {
                // Do not redirect on allowed CMS page
                $identifier = $request->getParam('page_id', $request->getParam('id', Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE)));
                $page = Mage::getModel('cms/page')->load($identifier);
                if (in_array($page->getIdentifier(), $helper->getPublicCmsPages())) {
                    return;
                }
            }

            // Path is not an allowed page or action
            $url = null;
            $parameters = array();
            $message = null;
            $redirectMode = null;

            // Define redirect mode
            if ($session->isLoggedIn()) {
                // Customer is loged in
                $redirectMode = $helper->getUnauthorizedCustomerRedirect();
            } else {
                // Customer is not logged in
                $redirectMode = $helper->getAnonymousCustomerRedirect();
            }

            // Define redirection parameters depending on redirect mode
            if ($redirectMode == Tangkoko_Authorization_Helper_Store::PAGE_REDIRECT) {
                // Page redirection
                $url = $helper->getErrorPage();
            } elseif ($redirectMode == Tangkoko_Authorization_Helper_Store::CUSTOM_REDIRECT) {
                // Url redirection
                $url = $helper->getCustomRedirectUrl();
            } elseif ($redirectMode == Tangkoko_Authorization_Helper_Store::LOGIN_REDIRECT) {
                // Visitor with redirect mode equal to login
                $url = Mage_Customer_Helper_Data::ROUTE_ACCOUNT_LOGIN;
                $parameters = Mage::helper('customer')->getLoginUrlParams();
            }

            // Get redirect message
            $message = $helper->getErrorMessage();
            if (!empty($message)) {
                $errorMessage = new Mage_Core_Model_Message_Error($message);
                $session->addUniqueMessages($errorMessage);
            }


            return Mage::app()->getResponse()->setRedirect(Mage::getUrl($url, $parameters));

        }
    }
    
    /**
     * Define origin page as referer in account login page
     *
     * @param Mage_Core_Controller_Varien_Action $action
     */
    public function setRedirectRefererOnLogin($observer)
    {
        $url = Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer()  : Mage::getUrl();
        Mage::getSingleton('customer/session')->setBeforeAuthUrl($url);
        
        return $this;
    }
}
