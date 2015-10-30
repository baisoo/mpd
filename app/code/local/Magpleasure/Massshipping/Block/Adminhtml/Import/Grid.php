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

class Magpleasure_Massshipping_Block_Adminhtml_Import_Grid extends Magpleasure_Massshipping_Block_Adminhtml_Import_Abstract
{
    protected $_quote;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("massshipping/import/grid.phtml");
    }

    public function getNextButtonHtml()
    {
        return $this->_getButtonHtml(array(
            'label' => $this->__("Next"),
            'title' => $this->__("Next"),
            'onclick' => "{$this->_helper()->getJsObjectName()}.matchColumns('finish', this);",
            'class' => 'ms-button blue medium next',
            'id'    => 'button-next',
        ));
    }

    public function getDeleteButtonHtml()
    {
        return $this->_getButtonHtml(array(
            'label' => $this->__("Delete Unnamed Columns"),
            'title' => $this->__("Delete Unnamed Columns"),
            'onclick' => "columns.deleteUnresolved();",
            'class' => 'ms-button grey medium delete-unnamed',
            'id'    => 'button-delete-unnamed',
        ));
    }

    public function getCancelAndReloadLinkHtml()
    {
        return $this->_getLinkHtml(array(
            'label' => $this->__("Cancel and Re-Upload List"),
            'title' => $this->__("Cancel and Re-Upload List"),
            'onclick' => "{$this->_helper()->getJsObjectName()}.wantPage('home', this);",
            'class' => 'ms-link cancel-and-reload',
            'id'    => 'link-cancel-and-reload',
            'href' => '#',
        ));
    }

    public function getSpecialMessage()
    {
        return $this->_helper()->__("Let's match the columns in your uploaded list to your Shipping Data.");
    }

    public function getColumns()
    {
        return $this->getQuote()->getColumns();
    }

    public function getRows()
    {
        return $this->getQuote()->getRows(5);
    }

    public function getOkButtonHtml(Magpleasure_Massshipping_Model_Column $column)
    {
        $columnId = $column->getId();
        return $this->_getButtonHtml(array(
            'label' => $this->__("OK"),
            'title' => $this->__("OK"),
            'onclick' => "columns.resolveColumn({$columnId});",
            'class' => 'ms-button blue medium delete-unnamed',
            'id'    => 'button_ok_column_'.$columnId,
            'type' => 'button',
        ));
    }

    public function getDeleteColumnButtonHtml(Magpleasure_Massshipping_Model_Column $column)
    {
        $columnId = $column->getId();
        return $this->_getButtonHtml(array(
            'label' => $this->__("Delete"),
            'title' => $this->__("Delete"),
            'onclick' => "columns.deleteColumn({$columnId});",
            'class' => 'ms-button grey medium delete-column',
            'id'    => 'button_delete_column_'.$columnId,
            'type' => 'button',
        ));
    }

    public function getMatchJson()
    {
        /** @var $column Magpleasure_Massshipping_Model_Column */
        $column = Mage::getModel('massshipping/column');
        return  Zend_Json::encode($column->getMatchTypes());
    }

    public function getUndefinedLabel()
    {
        /** @var $column Magpleasure_Massshipping_Model_Column */
        $column = Mage::getModel('massshipping/column');
        return $column->getDefinedName();
    }



}