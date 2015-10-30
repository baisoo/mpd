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

class Magpleasure_Massshipping_Model_Mysql4_Row_Collection extends Magpleasure_Common_Model_Resource_Collection_Abstract
{
    protected $_quote = null;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('massshipping/row');
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

    public function addQuoteData($quote)
    {
        $this->_quote = $quote;
        return $this;
    }

    /**
     * Quote
     *
     * @return Magpleasure_Massshipping_Model_Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->_quote){
            foreach ($this as $item){
                $item->setQuote($this->_quote);
            }
        }
    }

    public function groupByValue($columnId)
    {
        $columnTable = Mage::getModel('massshipping/column')->getResource()->getMainTable();
        $cellTable = Mage::getModel('massshipping/cell')->getResource()->getMainTable();

        $this->getSelect()
            ->join(array('col'=>$columnTable), "col.column_id = '{$columnId}' AND col.quote_id = main_table.quote_id", array())
            ->join(array('cell'=>$cellTable), "cell.column_id = col.column_id AND cell.row_id = main_table.row_id", array('value'=>'cell.value'))
            ->group("cell.value")
            ;

        return $this;
    }

    public function addQuoteFilter($quoteId)
    {
        $this->getSelect()->where("main_table.quote_id = ?", $quoteId);
        return $this;
    }

    public function addColumnFilter($dataKey, $cell)
    {
        if (!is_numeric($dataKey)){
            $column = $this->getQuote()->getColumnByMatchKey($dataKey);
            $columnId = $column->getId() ? $column->getId() : false;
        } else {
            $columnId = $dataKey;
        }

        if ($columnId){
            $cellAlias = $dataKey."_alias";
            $cellTable = $this->_commonHelper()->getDatabase()->getTableName("mp_ms_cells");
            $this->getSelect()
                ->joinInner(array($cellAlias => $cellTable), "{$cellAlias}.row_id = main_table.row_id AND {$cellAlias}.column_id = '{$columnId}' AND  {$cellAlias}.value = '{$cell}'", array())
                ;
        }

        return $this;
    }

    public function collectItemData()
    {
        $items = array();
        foreach ($this as $row){
            /** @var $row Magpleasure_Massshipping_Model_Row */
            $items += $row->collectItemData();
        }
        return $items;
    }

}