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
 * @package    Magpleasure_Common
 * @version    0.6.0
 * @copyright  Copyright (c) 2012-2013 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

class Magpleasure_Common_Block_System_Entity_Form_Element_File_Image_Render extends Mage_Adminhtml_Block_Template
{
    /**
     * Path to element template
     */
    const TEMPLATE_PATH = 'magpleasure/system/config/form/element/file/image.phtml';

    protected $_collectData = array(
        'max_size',
        'allowed',
        'dir',
        'html_id',
    );

    protected function  _construct()
    {
        parent::_construct();
        $this->setTemplate(self::TEMPLATE_PATH);
    }

    /**
     * Common Helper
     *
     * @return Magpleasure_Common_Helper_Data
     */
    protected function _commonHelper()
    {
        return Mage::helper('magpleasure');
    }

    public function getName()
    {
        return $this->getData('name') ? $this->getData('name') : $this->getData('html_id');
    }

    public function isAjax()
    {
        return $this->_commonHelper()->getRequest()->isAjax();
    }

    public function getUploadUrl()
    {
        $data = array();
        foreach ($this->_collectData as $key){
            if ($this->hasData($key)){
                $data[$key] = $this->getData($key);
            }
        }
        $hash = $this->_commonHelper()->getHash()->getHash($data);
        return $this->getUrl('magpleasure_admin/adminhtml_fileimage/upload', array('h' => $hash));
    }

    public function hasImage()
    {
        return true;
    }

    public function getThumbnailWidth()
    {

    }

    public function getThumbnailHeight()
    {

    }

    public function getThumbnailUrl()
    {

    }

}
