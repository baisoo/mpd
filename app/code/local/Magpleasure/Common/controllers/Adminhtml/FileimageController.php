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

require_once Mage::getBaseDir('lib').DS."FileUpload".DS."MpUploadHandler.php";
class Magpleasure_Common_Adminhtml_FileimageController extends Magpleasure_Common_Controller_Adminhtml_Action
{
    const TEMP_DIR = 'tmp';

    protected function _getUploadUrl($folder, $field = null)
    {
        $url = Mage::getBaseUrl('media') . self::TEMP_DIR . "/" . $folder . "/";
        if ($field){
            $url .= $field . "/";
        }
        return $url;
    }

    protected function _getUploadDir($folder, $field = null)
    {
        $url = Mage::getBaseDir('media') . DS. self::TEMP_DIR . DS . $folder;
        if ($field){
            $url .= DS . $field;
        }
        return $url;
    }

    public function uploadAction()
    {
        if ($hash = $this->getRequest()->getParam('h')) {
            $initData = $this->_commonHelper()->getHash()->getData($hash);
        } else {
            $initData = array();
        }

        if (isset($initData['allowed']) && $initData['allowed'] && is_array($initData['allowed'])){
            $allowedTypes = implode("|", $initData['allowed']);
        } else {
            $allowedTypes = "png|gif|jpg|jpeg";
        }

        $field = isset($initData['html_id']) ? $initData['html_id'] : "default";
        $folder = isset($initData['dir']) ? $initData['dir'] : "default";

        $uploadHandler = new MpUploadHandler(array(
            'script_url' => $this->getRequest()->getRequestUri(),
            'upload_dir' => $this->_getUploadDir($folder, $field),
            'upload_url' => $this->_getUploadUrl($folder, $field),
            'param_name' => $field,
        ));

    }

}