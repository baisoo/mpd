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

class Magpleasure_Massshipping_Model_Cell extends Mage_Core_Model_Abstract
{
    protected $_column;
    protected $_row;


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
        $this->_init('massshipping/cell');
    }

    public function setColumn(Magpleasure_Massshipping_Model_Column $column)
    {
        $this->_column = $column;
        $this->setColumnId($column->getId());
        return $this;
    }


    public function setRow(Magpleasure_Massshipping_Model_Row $row)
    {
        $this->_row = $row;
        $this->setRowId($row->getId());
        return $this;
    }

}