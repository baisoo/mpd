<?php
/**
 * Rewrite Mage_Customer_Model_Session
 *
 * @category    Tangkoko
 * @package     Tangkoko_Authorization
 * @author      Tangkoko <support@tangkoko.com>
 * @copyright   Copyright (c) 2015 Tangkoko (http://www.tangkoko.com)
 * @license     All rights reserved
 */

class Tangkoko_Authorization_Model_Customer_Session extends Mage_Customer_Model_Session
{

    /**
     * Rewrite customer authorization, add group authorization checking
     *
     * @param   string $username
     * @param   string $password
     * @return  bool
     */
    public function login($username, $password)
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('customer/customer')
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
        /* @var $helper Tangkoko_Authorization_Helper_Store */
        $helper = Mage::helper('tangkoko_authorization/store');

        if ($customer->authenticate($username, $password)) {

            // REWRITE
            if(Mage::helper('tangkoko_authorization/store')->isActive() && !$helper->isAuthorized($customer)){
                $message = $helper->getErrorMessage();
                if (!empty($message)) {
                    $errorMessage = new Mage_Core_Model_Message_Error($message);
                    $this->addUniqueMessages($errorMessage);
                }
                return false;
            }
            // END REWRITE

            $this->setCustomerAsLoggedIn($customer);
            return true;
        }
        return false;
    }
}