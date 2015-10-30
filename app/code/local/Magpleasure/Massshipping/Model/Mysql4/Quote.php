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

class Magpleasure_Massshipping_Model_Mysql4_Quote extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Helper
     *
     * @return Magpleasure_Massshipping_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('massshipping');
    }

    public function _construct()
    {    
        $this->_init('massshipping/quote', 'quote_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $now = new Zend_Date();
        $now = $now->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        if (!$object->getId()){
            $object->setCreatedAt($now);
        }
        $object->setUpdatedAt($now);
        return parent::_beforeSave($object);
    }

    public function loadByUserId($object, $userId)
    {
        /** @var $collection Magpleasure_Massshipping_Model_Mysql4_Quote_Collection */
        $collection = Mage::getModel('massshipping/quote')->getCollection();

        $collection
            ->addFieldToFilter('user_id', $userId)
            ->addFieldToFilter('status', array(Magpleasure_Massshipping_Model_Quote::STATUS_ACTIVE, Magpleasure_Massshipping_Model_Quote::STATUS_PROCESS))
        ;

        foreach ($collection as $item){
            $quote = Mage::getModel('massshipping/quote')->load($item->getId());
            $object->addData($quote->getData());
            return $this;
        }

        return $this;
    }

    public function removeOldData($userId)
    {
        # Remove old data
        $rowTable = Mage::getModel('massshipping/row')->getResource()->getMainTable();
        $quoteTable = Mage::getModel('massshipping/quote')->getResource()->getMainTable();

        $selectQuotes = new Zend_Db_select($this->_getReadAdapter());
        $selectQuotes
            ->from($quoteTable, array('quote_id'))
            ->where("user_id = ?", $userId)
            ;

        $write = $this->_getWriteAdapter();
        $write
            ->beginTransaction()
            ->delete($rowTable, sprintf(new Zend_Db_Expr("quote_id IN (%s)", $selectQuotes->__toString()), $selectQuotes->__toString()));

        $write->commit();

        # Close unfinished quotes

        /** @var $quote Magpleasure_Massshipping_Model_Quote */
        $quote = Mage::getModel('massshipping/quote');
        $quote->loadByUserId($userId);

        $lastQuote = $quote->getLastQuote();
        if ($lastQuote && $lastQuote->isUnfinished()){
            $lastQuote->complete();
        }

        return $this;
    }
}