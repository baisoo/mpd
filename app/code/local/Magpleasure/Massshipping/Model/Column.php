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

class Magpleasure_Massshipping_Model_Column extends Magpleasure_Common_Model_Abstract
{
    const TYPE_ORDER_ID = 'order_id';
    const TYPE_CARRIER_CODE = 'carrier';
    const TYPE_TITLE = 'title';
    const TYPE_TRACK_NUMBER = 'number';
    const TYPE_SEND_EMAIL = 'send_email';
    const TYPE_ITEM_SKU = 'order_item_sku';
    const TYPE_ITEM_QTY = 'order_item_qty';



    const COMMON_CARRIER_VALUE = 'common';

    /**
     * Helper
     *
     * @return Magpleasure_Massshipping_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('massshipping');
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_init('massshipping/column');
    }

    public function getMatchTypes()
    {
        return array(
            self::TYPE_ORDER_ID => $this->_helper()->__("Order #"),
            self::TYPE_CARRIER_CODE => $this->_helper()->__("Carrier"),
            self::TYPE_TITLE => $this->_helper()->__("Title"),
            self::TYPE_TRACK_NUMBER => $this->_helper()->__("Track Number"),
            self::TYPE_ITEM_SKU => $this->_helper()->__("Item SKU"),
            self::TYPE_ITEM_QTY => $this->_helper()->__("Item Quantity"),
            self::TYPE_SEND_EMAIL => $this->_helper()->__("Send Email"),
        );
    }


    public function getDefinedName()
    {
        foreach($this->getMatchTypes() as $key=>$value){
            if ($this->getMatchKey() == $key){
                return $value;
            }
        }
        return $this->_helper()->__("Unnamed Column");
    }

    public function getIsActive()
    {
        return false;
    }

    public function getIsDeleted()
    {
        return $this->getIsResolved() && !$this->getMatchKey();
    }

    public function collectItemData()
    {

    }


}