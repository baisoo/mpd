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

class Magpleasure_Massshipping_Block_Adminhtml_Import extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate("massshipping/import.phtml");

    }

    /**
     * Helper
     *
     * @return Magpleasure_Massshipping_Helper_Data
     */
    public function _helper()
    {
        return Mage::helper('massshipping');
    }

    public function getResultMessages()
    {
        return "";
    }

    public function getContentHtml()
    {
        $home = $this->getLayout()->createBlock("massshipping/adminhtml_import_home");
        if ($home){
            return $home->toHtml();
        }
        return "";
    }

    public function getInterfaceUrl()
    {
        return $this->getUrl("massshipping/import/interface", array('name'=>'{{name}}'));
    }

    public function getUploadUrl()
    {
        return $this->getUrl("massshipping/import/upload");
    }

    public function getXmlUploadUrl()
    {
        return $this->getUrl("massshipping/import/xmlUpload");
    }

    public function getPostUrl()
    {
        return $this->getUrl("massshipping/import/post");
    }

    public function getPostExcelUrl()
    {
        return $this->getUrl("massshipping/import/postExcel", array('name' => '{{name}}'));
    }

    public function getRegisterUrl()
    {
        return $this->getUrl("massshipping/process/register");
    }

    public function getProcessUrl()
    {
        return $this->getUrl("massshipping/process/ship", array('id'=>'{{row_id}}'));
    }

    public function getJsName()
    {
        return $this->_helper()->getJsObjectName();
    }

}