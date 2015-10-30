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

class Magpleasure_Massshipping_Controller_Action extends Mage_Adminhtml_Controller_Action
{

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/massshipping');
    }

    /**
     * Retrieves user
     *
     * @return bool|Mage_Admin_Model_User
     */
    public function getUser()
    {
        return $this->_helper()->getUser();
    }

    protected function _getMessageBlockHtml()
    {
        return $this->getLayout()->getMessagesBlock()->addMessages($this->_getSession()->getMessages(true))->toHtml();
    }

    /**
     * Quote
     *
     * @return Magpleasure_Massshipping_Model_Quote
     */
    protected function _getQuote()
    {
        /** @var $quote Magpleasure_Massshipping_Model_Quote */
        $quote = Mage::getModel('massshipping/quote');
        $quote->loadByUserId($this->getUser()->getId());
        if (!$quote->getId()){
            $quote
                ->setUserId($this->getUser()->getId())
                ->setStatus(Magpleasure_Massshipping_Model_Quote::STATUS_ACTIVE)
                ->save()
            ;
        }
        return $quote;
    }

    /**
     * Response for Ajax Request
     *
     * @param array $result
     * @param bool $wrapToBase64
     */
    protected function _ajaxResponse($result = array(), $wrapToBase64 = false)
    {
        $json = Zend_Json::encode($result);
        $this->getResponse()->setBody($wrapToBase64 ? base64_encode($json) : $json);
    }

    /**
     * Helper
     *
     * @return Magpleasure_Massshipping_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('massshipping');
    }
}