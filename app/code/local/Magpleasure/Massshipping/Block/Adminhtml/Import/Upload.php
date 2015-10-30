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

class Magpleasure_Massshipping_Block_Adminhtml_Import_Upload extends Magpleasure_Massshipping_Block_Adminhtml_Import_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("massshipping/import/upload.phtml");
    }



    public function getChangeMehtodButtonHtml()
    {
        return $this->_getButtonHtml(array(
            'label' => $this->__("Change Method of Import"),
            'title' => $this->__("Change Method of Import"),
            'onclick' => "{$this->_helper()->getJsObjectName()}.wantPage('home', this);",
            'class' => 'ms-button grey medium upload',
            'id'    => 'button-upload',
            'type' => 'button',
        ));
    }

    public function getBrowseButtonHtml()
    {
        return $this->_getButtonHtml(array(
            'label' => $this->__("Browse"),
            'title' => $this->__("Browse"),
            'onclick' => "",
            'class' => 'ms-button blue medium upload',
            'id'    => 'button-browse',
            'type' => 'button',
        ));
    }

    public function getImportButtonHtml()
    {
        return $this->_getButtonHtml(array(
            'label' => $this->__("Import List"),
            'title' => $this->__("Import List"),
            'onclick' => "{$this->_helper()->getJsObjectName()}.uploadFile('grid', this);",
            'class' => 'ms-button blue medium upload',
            'id'    => 'button-import',
            'type' => 'button',
        ));
    }



}