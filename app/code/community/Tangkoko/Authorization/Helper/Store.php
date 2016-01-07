<?php
/**
 * @category    Tangkoko
 * @package     Tangkoko_Authorization
 * @author      Tangkoko <support@tangkoko.com>
 * @copyright   Copyright (c) 2015 Tangkoko (http://www.tangkoko.com)
 * @license     All rights reserved
 */

class Tangkoko_Authorization_Helper_Store extends Mage_Core_Helper_Abstract
{
    const LOGIN_REDIRECT = 'login';

    const CUSTOM_REDIRECT = 'custom';

    const PAGE_REDIRECT = 'page';

    const XML_PATH_STORE_AUTHORIZATION_ACTIVE = 'store_authorization/general/active';

    const XML_PATH_STORE_AUTHORIZATION_ALLOWED_CUSTOMER_GROUPS = 'store_authorization/general/allowed_customer_groups';

    const XML_PATH_STORE_AUTHORIZATION_PUBLIC_CMS_PAGES = 'store_authorization/general/public_cms_pages';

    const XML_PATH_STORE_AUTHORIZATION_ANONYMOUS_CUSTOMER_REDIRECT = 'store_authorization/general/anonymous_customer_redirect';

    const XML_PATH_STORE_AUTHORIZATION_UNAUTHORIZED_CUSTOMER_REDIRECT = 'store_authorization/general/unauthorized_customer_redirect';

    const XML_PATH_STORE_AUTHORIZATION_ERROR_MESSAGE = 'store_authorization/general/error_message';

    const XML_PATH_STORE_AUTHORIZATION_ERROR_PAGE = 'store_authorization/general/error_page';

    const XML_PATH_STORE_AUTHORIZATION_PUBLIC_ACTIONS = 'store_authorization/general/public_actions';

    const XML_PATH_STORE_AUTHORIZATION_CUSTOM_REDIRECT_URL = 'store_authorization/general/custom_redirect_url';

    /**
     * Check if authorization is enabled for the store
     *
     * @param Mage_Core_Model_Store $store
     * @return boolean
     */
    public function isActive($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_STORE_AUTHORIZATION_ACTIVE, $store);
    }

    /**
     * Get allowed customer groups for the store
     *
     * @param Mage_Core_Model_Store $store
     * @return array
     */
    public function getAnonymousCustomerRedirect($store = null)
    {
        return (string)Mage::getStoreConfig(self::XML_PATH_STORE_AUTHORIZATION_ANONYMOUS_CUSTOMER_REDIRECT, $store);
    }

    /**
     * Get allowed customer groups for the store
     *
     * @param Mage_Core_Model_Store $store
     * @return array
     */
    public function getUnauthorizedCustomerRedirect($store = null)
    {
        return (string)Mage::getStoreConfig(self::XML_PATH_STORE_AUTHORIZATION_UNAUTHORIZED_CUSTOMER_REDIRECT, $store);
    }

    /**
     * Get custom redirect url for the store
     *
     * @param Mage_Core_Model_Store $store
     * @return array
     */
    public function getCustomRedirectUrl($store = null)
    {
        return (string)Mage::getStoreConfig(self::XML_PATH_STORE_AUTHORIZATION_CUSTOM_REDIRECT_URL, $store);
    }

    /**
     * Get allowed customer groups for the store
     *
     * @param Mage_Core_Model_Store $store
     * @return array
     */
    public function getAllowedCustomerGroups($store = null)
    {
        $config = (string)Mage::getStoreConfig(self::XML_PATH_STORE_AUTHORIZATION_ALLOWED_CUSTOMER_GROUPS, $store);
        return explode(',', $config);
    }

    /**
     * Get public CMS pages for the store
     *
     * @param Mage_Core_Model_Store $store
     * @return array
     */
    public function getPublicCmsPages($store = null)
    {
        $config = (string)Mage::getStoreConfig(self::XML_PATH_STORE_AUTHORIZATION_PUBLIC_CMS_PAGES, $store);
        $result = array_map(array($this, '_getPageIdentifier'), explode(',', $config));
        $result[] = $this->getErrorPage($store);

        return $result;
    }

    /**
     * Get public URLs for the store
     *
     * @param Mage_Core_Model_Store $store
     * @return array
     */
    public function getPublicActions($store = null)
    {
        return (string)Mage::getStoreConfig(self::XML_PATH_STORE_AUTHORIZATION_PUBLIC_ACTIONS, $store);
    }

    /**
     * Get error message for the store
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getErrorMessage($store = null)
    {
        return (string)Mage::getStoreConfig(self::XML_PATH_STORE_AUTHORIZATION_ERROR_MESSAGE, $store);
    }

    /**
     * Get error page id for the store
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getErrorPage($store = null)
    {
        $result = (string)Mage::getStoreConfig(self::XML_PATH_STORE_AUTHORIZATION_ERROR_PAGE, $store);
        return $this->_getPageIdentifier($result);
    }

    /**
     * Check if customer is authorized to access store
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param Mage_Core_Model_Store $store
     * @return boolean
     */
    public function isAuthorized($customer, $store = null)
    {
        $groups = $this->getAllowedCustomerGroups($store);
        if (in_array(0, $groups) && !(bool)$customer->getId()) {
            // not logged in
            return true;
        } else {
            // logged in customers within the right group
            return (bool)$customer->getId() && in_array($customer->getGroupId(), $groups);
        }
    }

    protected function _getPageIdentifier($value)
    {
        if (strpos($value, '|') !== false) {
            $value = (int)substr($value, strpos($value, '|') + 1);
        }

        return $value;
    }
}