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

class Magpleasure_Massshipping_Model_Order extends Mage_Sales_Model_Order
{
    protected $_skuMap = array();

    /**
     * Helper
     *
     * @return Magpleasure_Massshipping_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('massshipping');
    }

    public function getUnshippedQty($itemSku)
    {
        if ($item = $this->getItemBySku($itemSku)){
            if ($item->canShip() && $item->getQtyToShip()){
                return $item->getQtyToShip();
            }
        }
        return false;
    }

    /**
     * Find Order Item
     *
     * @param $itemSku
     * @return Mage_Sales_Model_Order_Item
     */
    public function getItemBySku($itemSku)
    {
        $itemSku = trim($itemSku);
        if (!isset($this->_skuMap[$itemSku])){
            foreach ($this->getAllItems() as $item){
                /** @var $item Mage_Sales_Model_Order_Item */
                if ($item->getSku() == $itemSku){
                    if ($item->canShip()){
                        $this->_skuMap[$itemSku] = $item;
                        return $item;
                    }
                }
            }
            Mage::throwException($this->_helper()->__("Process error: Could not find item with sku %s", $itemSku));
        }
        return $this->_skuMap[$itemSku];
    }

    public function collectItemData()
    {
        $items = array();
        foreach ($this->getAllItems() as $item){
            /** @var $item Mage_Sales_Model_Order_Item */
            if ($item->canShip() && $item->getQtyToShip()){
                $qty = $item->getQtyToShip();
                $sku = $item->getSku();

                if ($qty && $sku){
                    $items[$item->getId()] = $qty;
                }
            }
        }
        return $items;
    }
}