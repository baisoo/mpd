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

class Magpleasure_Massshipping_ImportController extends Magpleasure_Massshipping_Controller_Action
{
    const LAST_STEP_NAME = 'finish';
    const COLUMN_PREFIX = 'column_';

    /**
     * Array with upload error descriptions
     * @var string
     */
    protected $_uploadErrors = array(
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3',
        7 => 'Failed to write file to disk. Introduced in PHP 5.1.0',
        8 => 'A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help. Introduced in PHP 5.2.0',
    );

    /**
     * Retrives Upload Error Description
     * @param int|string $errorCode
     * @return string
     */
    public function getUploadErrorDesc($errorCode)
    {
        if (isset($this->_uploadErrors[$errorCode])){
            return $this->_helper()->__($this->_uploadErrors[$errorCode]);
        }
        return $this->_helper()->__('Unknown upload error');
    }

    public function indexAction()
    {
        $this->_title($this->_helper()->__("Mass Shipping"));

        $this->_processReport();
        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _validateFormKey()
    {
        return true;
    }

    protected function _processReport()
    {
        $quote = $this->_getQuote();
        if ($quote->isUnfinished()){
            $quote->complete();
        }

        if ($lastQuote = $this->_getQuote()->getLastQuote()){

            $date = new Zend_Date($lastQuote->getUpdatedAt(), Zend_Date::ISO_8601, Mage::app()->getLocale()->getLocaleCode());
            $date->subSecond($this->_helper()->getTimezoneOffset());

            $date = $date->toString(Zend_Date::DATETIME_MEDIUM);

            if ($lastQuote->isUnfinished()){

                $link = '<a class="report" href="#" onclick="MassShip.wantPage(\'finish\'); return false;" target="_blank">'.$this->_helper()->__("Complete").'</a>';

                $this->_getSession()->addNotice($this->_helper()->__("Your Last Session was started at %s. But it wasn't completed for a some reason. Do you want to compete the process? - %s", $date, $link));
            } else {
                $this->_getSession()->addNotice($this->_helper()->__("Your Last Session was successful completed at %s", $date));
            }


            $success = $lastQuote->getSuccessfullRows()->getSize();
            $errors = $lastQuote->getFailedRows()->getSize();
            $left = $lastQuote->getProgressLeft();

            if ($success){

                $url = $this->getUrl('*/*/successCsv');
                $link = '<a class="report" href="'.$url.'" target="_blank">'.$this->_helper()->__("Report").'</a>';

                $this->_getSession()->addNotice($this->_helper()->__("%s row(s) was successfully processed - %s", $success, $link));
            }

            if ($errors){

                $url = $this->getUrl('*/*/errorsCsv');
                $link = '<a class="report" href="'.$url.'" target="_blank">'.$this->_helper()->__("Report").'</a>';

                $this->_getSession()->addNotice($this->_helper()->__("%s row(s) was failed with some errors - %s", $errors, $link));
            }

            if ($left){
                $url = $this->getUrl('*/*/leftCsv');
                $link = '<a class="report" href="'.$url.'" target="_blank">'.$this->_helper()->__("Report").'</a>';

                $this->_getSession()->addNotice($this->_helper()->__("%s row(s) was unprocessed - %s", $left, $link));
            }
        }
        return $this;
    }


    public function interfaceAction()
    {
        $result = array();
        if ($name = $this->getRequest()->getParam('name')){

            /** @var $block Magpleasure_Massshipping_Block_Adminhtml_Import_Abstract */
            $block = $this->getLayout()->createBlock("massshipping/adminhtml_import_{$name}");
            if ($block){
                if ($block->getSpecialMessage()){
                    $this->_getSession()->addSuccess($block->getSpecialMessage());
                }

                # Report
                if ($name == 'home'){
                    $this->_processReport();
                }

                $result['html'] = $block->toHtml();
            } else {
                $this->_getSession()->addError("Ooops. Can not find some part.");
            }
        } else {
            $this->_getSession()->addError("Sorry but something wrong.");
        }

        $result['message'] = $this->_getMessageBlockHtml();


        $this->_ajaxResponse($result);
    }

    protected function _prepareColumns(array $post)
    {
        $columnIds = array();
        foreach ($post as $key=>$value){
            if (strpos($key, self::COLUMN_PREFIX) !== false){
                $columnIds[str_replace(self::COLUMN_PREFIX, "", $key)] = $value;
            }
        }
        if (!count($columnIds)){
            return false;
        }

        $quote = $this->_getQuote();
        $quote->unmatchColumns();

        foreach ($quote->getColumns() as $column){
            if (isset($columnIds[$column->getId()])){
                $column->setMatchKey($columnIds[$column->getId()]);
            }
            $column->setIsResolved(1)->save();
        }
        return true;
    }

    public function postAction(){

        $result = array();
        $name = self::LAST_STEP_NAME;
        $post = $this->getRequest()->getPost();

        if ($this->_prepareColumns($post)){

            /** @var $block Magpleasure_Massshipping_Block_Adminhtml_Import_Abstract */
            $block = $this->getLayout()->createBlock("massshipping/adminhtml_import_{$name}");
            if ($block){

                if ($block->getSpecialMessage()){
                    $this->_getSession()->addSuccess($block->getSpecialMessage());
                }

                $result['html'] = $block->toHtml();
            } else {
                $this->_getSession()->addError("Ooops. Can not find some part.");
            }
        } else {
            $this->_getSession()->addError("Order # is required to be matched.");
        }

        $result['message'] = $this->_getMessageBlockHtml();


        $this->_ajaxResponse($result);
    }

    public function postExcelAction()
    {
        $result = array();
        $quote = $this->_getQuote();

        $name = $this->getRequest()->getParam('name');

        if ($pasteData = $this->getRequest()->getPost('paste_field')){

            try {
                if ($quote->processPostExcel($pasteData) && $this->_getQuote()->getColumns()->getSize()){

                    /** @var $block Magpleasure_Massshipping_Block_Adminhtml_Import_Abstract */
                    $block = $this->getLayout()->createBlock("massshipping/adminhtml_import_{$name}");
                    if ($block){

                        if ($block->getSpecialMessage()){
                            $this->_getSession()->addSuccess($block->getSpecialMessage());
                        }

                        $result['html'] = $block->toHtml();
                    } else {
                        $this->_getSession()->addError("Ooops. Can not find some part.");
                    }


                    $result['success'] = true;
                    $result['data'] = $quote->getRowData();
                    $result['message'] = $this->_getMessageBlockHtml();

                } else {

                    $this->_getSession()->addError($this->_helper()->__("Wrong data was provided."));
                    $result['error'] = true;
                    $result['message'] = $this->_getMessageBlockHtml();

                }

            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $result['error'] = true;
                $result['message'] = $this->_getMessageBlockHtml();

            }
        }

        $this->_ajaxResponse($result);
    }

    public function uploadAction()
    {
        $result = array();
        $quote = $this->_getQuote();

        $file = new Varien_Object(isset($_FILES['data_file']) ? $_FILES['data_file'] : array());

        if ($error = $file->getError()){
            $this->_getSession()->addError($this->_helper()->__($this->getUploadErrorDesc($error)));
            $result['error'] = true;
            $result['message'] = $this->_getMessageBlockHtml();
            $this->_ajaxResponse($result, true);

            return;
        }

        if (file_exists($fileName = $file->getTmpName())){

            try {
                if ($quote->processFile($fileName, $file->getType()) && $this->_getQuote()->getColumns()->getSize()){
                    $result['success'] = true;
//                    $result['data'] = $quote->getRowData();

                } else {

                    $this->_getSession()->addError($this->_helper()->__("Wrong data file was provided."));
                    $result['error'] = true;
                    $result['message'] = $this->_getMessageBlockHtml();

                }

            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $result['error'] = true;
                $result['message'] = $this->_getMessageBlockHtml();

            }
        }

        $this->_ajaxResponse($result, true);
    }

    public function errorsCsvAction()
    {
        $fileName   = 'errors.csv';
        $content    = $this->_getQuote()->getLastQuote()->getErrorsCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function successCsvAction()
    {
        $fileName   = 'success.csv';
        $content    = $this->_getQuote()->getLastQuote()->getSuccessCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function leftCsvAction()
    {
        $fileName   = 'left.csv';
        $content    = $this->_getQuote()->getLastQuote()->getLeftCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

}