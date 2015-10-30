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

class Magpleasure_Massshipping_Block_Adminhtml_Import_Home extends Magpleasure_Massshipping_Block_Adminhtml_Import_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("massshipping/import/home.phtml");
    }

    public function getUploadButtonHtml()
    {
        return $this->_getButtonHtml(array(
            'label' => $this->__("Upload File<br/><span class='subtext'>Import from CSV</span>"),
            'title' => $this->__("Import from CSV"),
            'onclick' => "{$this->_helper()->getJsObjectName()}.wantPage('upload', this);",
            'class' => 'ms-button big upload',
            'id'    => 'button-upload',
        ));
    }


    public function getImportButtonHtml()
    {
        return $this->_getButtonHtml(array(
            'label' => $this->__("Import<br/><span class='subtext'>Copy/Past from Excel</span>"),
            'title' => $this->__("Copy/Past from Excel"),
            'onclick' => "{$this->_helper()->getJsObjectName()}.wantPage('excel', this);",
            'class' => 'ms-button big import',
            'id'    => 'button-import',
        ));
    }

    public function getInformatoion()
    {
        $block = $this->getLayout()->createBlock('core/template');
        if ($block){
            $block->setTemplate("massshipping/import/home/info.phtml");
            return $block->toHtml();
        }
        return false;
    }
}