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

class Magpleasure_Massshipping_Block_Adminhtml_Import_Abstract extends Mage_Adminhtml_Block_Template
{
    /**
     * Helper
     *
     * @return Magpleasure_Massshipping_Helper_Data
     */
    public function _helper()
    {
        return Mage::helper('massshipping');
    }


    /**
     * Button HTML
     *
     * @param array $data
     * @return string
     */
    protected function _getButtonHtml(array $data)
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button');
        if ($button){
            $button->addData($data);
            return $button->toHtml();
        }
        return "";
    }

    /**
     * Quote
     *
     * @return Magpleasure_Massshipping_Model_Quote
     */
    public function getQuote()
    {
        if (!$this->_quote){
            /** @var $quote Magpleasure_Massshipping_Model_Quote */
            $quote = Mage::getModel('massshipping/quote');
            $quote->loadByUserId($this->_helper()->getUser()->getId());
            $this->_quote = $quote;
        }
        return $this->_quote;
    }

    public function getJsName()
    {
        return $this->_helper()->getJsObjectName();
    }

    /**
     * Escape quotes inside html attributes
     * Use $addSlashes = false for escaping js that inside html attribute (onClick, onSubmit etc)
     *
     * @param string $data
     * @param bool $addSlashes
     * @return string
     */
    public function quoteEscape($data, $addSlashes = false)
    {
        if ($addSlashes === true) {
            $data = addslashes($data);
        }
        return htmlspecialchars($data, ENT_QUOTES, null, false);
    }

    protected function _getLinkHtml(array $data)
    {
        $aData = new Varien_Object($data);

        $html = $aData->getBeforeHtml().'<a '
            . ($aData->getId()?' id="'.$aData->getId() . '"':'')
            . ' title="'
            . $this->quoteEscape($aData->getTitle() ? $aData->getTitle() : $aData->getLabel())
            . '"'
            . ' href="'.$aData->getHref() . '"'
            . ' class="' . $aData->getClass() . ($aData->getDisabled() ? ' disabled' : '') . '"'
            . ' onclick="'.$aData->getOnclick().'"'
            . ' style="'.$aData->getStyle() .'"'
            . ($aData->getDisabled() ? ' disabled="disabled"' : '')
            . '><span>' .$aData->getLabel().'</span></a>'.$aData->getAfterHtml();

        return $html;
    }

    public function getSpecialMessage()
    {
        return false;
    }

}