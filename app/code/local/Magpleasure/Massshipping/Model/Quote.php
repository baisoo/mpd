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

class Magpleasure_Massshipping_Model_Quote extends Mage_Core_Model_Abstract
{
    const STATUS_ACTIVE = 1;
    const STATUS_OLD = 2;
    const STATUS_PROCESS = 3;

    protected $_row_data = array();
    protected $_columns;
    protected $_rows;

    protected $_allowedTypes = array(
        'text/csv',
        'application/x-csv',
        'application/csv',
        /*'text/xml'*/
    );

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
        $this->_init('massshipping/quote');
    }

    /**
     * Process data from file
     *
     * @param $fileName
     * @param bool $type
     * @return bool
     */
    public function processFile($fileName, $type = false)
    {
        $type = str_replace("application/vnd.ms-excel", "text/csv", $type);

        if (!in_array($type, $this->_allowedTypes)){
            Mage::throwException($this->_helper()->__("Type '%s' is not allowed.", $type));
        }

        $content = file_get_contents($fileName);

        /** @var $processor Magpleasure_Massshipping_Model_Processor_Abstract */
        $processor = Mage::getSingleton("massshipping/processor_".str_replace("/","_", $type));
        $this->setRowData($processor->process($content));

        $this->_storeData();

        return true;
    }

    public function processPostExcel($content)
    {
        /** @var $processor Magpleasure_Massshipping_Model_Processor_Abstract */
        $processor = Mage::getSingleton("massshipping/processor_text_excel");
        $this->setRowData($processor->process($content));

        $this->_storeData();

        return true;
    }

    public function forgetAllForMe($userId)
    {
        $this->getResource()->removeOldData($userId);
        return $this;
    }

    public function isActive()
    {
        return $this->getStatus() == self::STATUS_ACTIVE;
    }

    public function getUnfinishedQuote()
    {
        die;
    }

    /**
     * Last Quote
     *
     * @return bool|Magpleasure_Massshipping_Model_Quote
     */
    public function getLastQuote()
    {
        if ($this->getId() && $this->isActive()){

            /** @var $quotes Magpleasure_Massshipping_Model_Mysql4_Quote_Collection */
            $quotes = Mage::getModel('massshipping/quote')->getCollection();
            $quotes
                ->addFieldToFilter('status', array(self::STATUS_OLD, self::STATUS_PROCESS))
                ->addFieldToFilter('user_id', $this->getUserId())
                ->setOrder("quote_id", "DESC")
                ->setPageSize(1)
                ;

            foreach ($quotes as $quote){
                return $quote;
            }
        }
        return false;
    }

    /**
     * Columns Collection
     *
     * @return Magpleasure_Massshipping_Model_Mysql4_Column_Collection
     */
    public function getColumns()
    {
        if (!$this->_columns){
            /** @var $columns Magpleasure_Massshipping_Model_Mysql4_Column_Collection */
            $columns = Mage::getModel('massshipping/column')->getCollection();
            $columns->addFieldToFilter('quote_id', $this->getId());
            $this->_columns = $columns;
        }
        return $this->_columns;
    }

    /**
     * Retrieves column instance
     *
     * @param $dataKey
     * @return Magpleasure_Massshipping_Model_Column
     */
    protected function _registerColumn($dataKey)
    {
        if (!($column = $this->getColumnByDataKey($dataKey))){
            /** @var $column Magpleasure_Massshipping_Model_Column */
            $column = Mage::getModel('massshipping/column');
            $column
                ->setQuoteId($this->getId())
                ->setDataKey($dataKey)
                ->save()
                ;

            $this->_columns = null;
        }
        return $column;
    }

    /**
     * Column by match key
     *
     * @param $matchKey
     * @return bool|Magpleasure_Massshipping_Model_Column
     */
    public function getColumnByMatchKey($matchKey)
    {
        foreach ($this->getColumns() as $column){
            if ($column->getMatchKey() == $matchKey){
                return $column;
            }
        }
        return false;
    }

    /**
     * Column by data key
     *
     * @param $dataKey
     * @return bool|Magpleasure_Massshipping_Model_Column
     */
    public function getColumnByDataKey($dataKey)
    {
        foreach ($this->getColumns() as $column){
            if ($column->getDataKey() == $dataKey){
                return $column;
            }
        }
        return false;
    }

    protected function _storeData()
    {
        if ($this->getId()){
            $this->clear();
        }

        if ($this->getUserId()){
            $this->setStatus(self::STATUS_ACTIVE);
            $this->save();
            $this->forgetAllForMe($this->getUserId());
        } else {
            Mage::throwException("User Id is required.");
        }

        if (count($this->getRowData())){


            foreach($this->getRowData() as $dataRow){
                /** @var $row Magpleasure_Massshipping_Model_Row */
                $row = Mage::getModel('massshipping/row');
                $row
                    ->setStatus(Magpleasure_Massshipping_Model_Row::STATUS_READY)
                    ->setQuoteId($this->getId())
                    ->save()
                    ;

                $i = 0;

                foreach ($dataRow as $value){
                    $key = "column_".$i;

                    /** @var $column Magpleasure_Massshipping_Model_Column */
                    $column = $this->_registerColumn($key);

                    /** @var $cell Magpleasure_Massshipping_Model_Cell */
                    $cell = Mage::getModel('massshipping/cell');
                    $cell
                        ->setColumn($column)
                        ->setRow($row)
                        ->setValue($value)
                        ->save()
                        ;

                    $i++;
                }

            }
        } else {
            Mage::throwException($this->_helper()->__("Ooops. There are no data."));
        }

        return $this;
    }

    public function getRowData()
    {
        return $this->_row_data;
    }

    public function setRowData($data)
    {
        $this->_row_data = $data;
        return false;
    }

    /**
     * Rows Collction Instance
     *
     * @return Magpleasure_Massshipping_Model_Mysql4_Quote_Collection|object
     */
    protected function _getRowsInstance()
    {
        /** @var $rows Magpleasure_Massshipping_Model_Mysql4_Quote_Collection */
        $rows = Mage::getModel('massshipping/row')->getCollection();
        $rows
            ->addQuoteFilter($this->getId())
            ->addQuoteData($this)
        ;

        return $rows;
    }


    /**
     * Grouped Rows
     *
     * @param $matchKey
     * @return mixed
     */
    public function getGroupedRows($matchKey)
    {
        $rows = $this->_getRowsInstance();
        $rows->groupByValue($this->getColumnByMatchKey($matchKey)->getId());
        return $rows;
    }

    /**
     * Rows
     *
     * @param bool $limit
     * @return Magpleasure_Massshipping_Model_Mysql4_Quote_Collection
     */
    public function getRows($limit = false)
    {
        if (!$this->_rows){
            $rows = $this->_getRowsInstance();

            if ($limit){
                $rows->setPageSize($limit);
            }

            $this->_rows = $rows;
        }
        return $this->_rows;
    }

    public function clear()
    {
        /** @var $collection Magpleasure_Massshipping_Model_Mysql4_Row_Collection */
        $rows = Mage::getModel('massshipping/row')->getCollection();
        $rows->addFieldToFilter('quote_id', $this->getId());
        foreach ($rows as $row){
            $row->delete();
        }

        /** @var $collection Magpleasure_Massshipping_Model_Mysql4_Column_Collection */
        $columns = Mage::getModel('massshipping/column')->getCollection();
        $columns->addFieldToFilter('quote_id', $this->getId());
        foreach ($columns as $column){
            $column->delete();
        }


        return $this;
    }

    public function loadByUserId($userId)
    {
        $this->getResource()->loadByUserId($this, $userId);
    }

    public function unmatchColumns()
    {
        /** @var $collection Magpleasure_Massshipping_Model_Mysql4_Column_Collection */
        $columns = Mage::getModel('massshipping/column')->getCollection();
        $columns->addFieldToFilter('quote_id', $this->getId());
        foreach ($columns as $column){
            $column
                ->setMatchKey(null)
                ->setIsResolved(0)
                ->save();
        }

        return $this;
    }

    public function getProgressComplete()
    {
        $rows = $this->_getRowsInstance();
        $rows->getSelect()->where('status <> ?', Magpleasure_Massshipping_Model_Row::STATUS_READY);
        return $rows->getSize();
    }

    public function getProgressLeft()
    {
        return $this->getProcessLeftCollection()->getSize();
    }

    public function getProcessLeftCollection()
    {
        $rows = $this->_getRowsInstance();
        $rows->getSelect()->where('status = ?', Magpleasure_Massshipping_Model_Row::STATUS_READY);
        return $rows;
    }

    public function getProgressNextId()
    {
        $rows = $this->getProcessLeftCollection();

        foreach ($rows as $row){
            /** @var $row Magpleasure_Massshipping_Model_Row */
            return $row->getId();
        }
        return false;
    }

    public function getProgressTotal()
    {
        return $this->getRows()->getSize();
    }

    public function complete()
    {
        if ($this->getId()){
            $this->setStatus(self::STATUS_OLD)->save();
        }
        return $this;
    }

    public function getSuccessfullRows()
    {
        $rows = $this->_getRowsInstance();
        $rows->addFieldToFilter('status', Magpleasure_Massshipping_Model_Row::STATUS_FINISH);
        return $rows;
    }

    public function getFailedRows()
    {
        $rows = $this->_getRowsInstance();
        $rows->addFieldToFilter('status', Magpleasure_Massshipping_Model_Row::STATUS_FAILED);
        return $rows;
    }

    protected function _protectCsv($value)
    {
        return str_replace('"', '\"', $value);
    }

    public function getSuccessCsv()
    {
        $csv = "";

        $data = array();
        foreach ($this->getColumns() as $column) {
            /** @var $column Magpleasure_Massshipping_Model_Column */
            if ($column->getMatchKey()){
                $data[] = '"'.$this->_protectCsv($column->getDefinedName()).'"';
            }
        }

        $csv.= implode(',', $data)."\n";


        foreach ($this->getSuccessfullRows() as $row){
            /** @var $row Magpleasure_Massshipping_Model_Row */

            $data = array();

            foreach ($this->getColumns() as $column) {
                /** @var $column Magpleasure_Massshipping_Model_Column */
                if ($column->getMatchKey()){
                    $data[] = '"'.$this->_protectCsv($row->getCellByColumnId($column->getId())->getValue()).'"';
                }
            }

            $csv.= implode(',', $data)."\n";
        }

        return $csv;
    }

    public function getErrorsCsv()
    {
        $csv = "";

        $data = array();
        foreach ($this->getColumns() as $column) {
            /** @var $column Magpleasure_Massshipping_Model_Column */
            if ($column->getMatchKey()){
                $data[] = '"'.$this->_protectCsv($column->getDefinedName()).'"';
            }
        }

        $data[] = '"'.$this->_helper()->__("Error").'"';

        $csv.= implode(',', $data)."\n";


        foreach ($this->getFailedRows() as $row){
            /** @var $row Magpleasure_Massshipping_Model_Row */

            $data = array();

            foreach ($this->getColumns() as $column) {
                /** @var $column Magpleasure_Massshipping_Model_Column */
                if ($column->getMatchKey()){
                    $data[] = '"'.$this->_protectCsv($row->getCellByColumnId($column->getId())->getValue()).'"';
                }
            }
            $data[] = '"'.$row->getMessage().'"';

            $csv.= implode(',', $data)."\n";
        }

        return $csv;
    }

    public function getLeftCsv()
    {
        $csv = "";

        $data = array();
        foreach ($this->getColumns() as $column) {
            /** @var $column Magpleasure_Massshipping_Model_Column */
            if ($column->getMatchKey()){
                $data[] = '"'.$this->_protectCsv($column->getDefinedName()).'"';
            }
        }

        $csv.= implode(',', $data)."\n";


        foreach ($this->getProcessLeftCollection() as $row){
            /** @var $row Magpleasure_Massshipping_Model_Row */

            $data = array();

            foreach ($this->getColumns() as $column) {
                /** @var $column Magpleasure_Massshipping_Model_Column */
                if ($column->getMatchKey()){
                    $data[] = '"'.$this->_protectCsv($row->getCellByColumnId($column->getId())->getValue()).'"';
                }
            }

            $csv.= implode(',', $data)."\n";
        }

        return $csv;
    }

    public function isUnfinished()
    {
        return !!$this->getProgressLeft();
    }


    public function reactivate()
    {
        $this->setStatus(self::STATUS_ACTIVE)->save();
        return $this;
    }
}