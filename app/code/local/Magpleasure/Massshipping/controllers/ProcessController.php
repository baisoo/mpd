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

class Magpleasure_Massshipping_ProcessController extends Magpleasure_Massshipping_Controller_Action
{

    protected function _getAjaxData()
    {
        $quote = $this->_getQuote();
        $result = array();
        $result['complete'] = $quote->getProgressComplete();
        $result['left'] = $quote->getProgressLeft();
        $result['row_id'] = $quote->getProgressNextId();
        $result['total'] = $quote->getProgressTotal();

        # Complete quote
        if (!$quote->getProgressLeft()){
            $quote->complete();
        }

        return $result;
    }

    protected function _processDataForLog($quote)
    {
        Mage::dispatchEvent('magpleasure_massshipping_process_quote', array('quote'=>$quote));
    }

    public function registerAction()
    {
        $result = array();
        try {

            # Process Post
            $post = $this->getRequest()->getPost();
            if (isset($post['form_key'])){
                unset($post['form_key']);
            }

            $quote = $this->_getQuote()
                ->setCarrierMatch(serialize($post))
                ->setStatus(Magpleasure_Massshipping_Model_Quote::STATUS_PROCESS)
                ->save();

            $this->_processDataForLog($quote);

            $result = $this->_getAjaxData();

        } catch (Exception $e){
            $this->_getSession()->addError($e->getMessage());
            $result['error'] = true;
        }

        if ($message = $this->_getMessageBlockHtml()){
            $result['message'] = $message;
        }

        $this->_ajaxResponse($result);
    }

    public function shipAction()
    {
        $result = array();

        try {

            # Process Row
            if ($rowId = $this->getRequest()->getParam('id')){

                /** @var $row Magpleasure_Massshipping_Model_Row */
                $row = Mage::getModel('massshipping/row')->load($rowId);
                $row->process();

            } else {
                Mage::throwException($this->__("Sorry. Can't continue."));
            }

            $result = $this->_getAjaxData();

        } catch (Exception $e){
            $this->_getSession()->addError($e->getMessage());
            $result['error'] = true;
        }

        if ($message = $this->_getMessageBlockHtml()){
            $result['message'] = $message;
        }
        $this->_ajaxResponse($result);
    }

}