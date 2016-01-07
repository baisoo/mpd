<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * @category    Tangkoko
 * @package     Tangkoko_AdminTools
 * @author      Olivier Michaud
 * @copyright   Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Tangkoko_AdminTools_Adminhtml_DateController extends Mage_Adminhtml_Controller_action
{
    /**
     * Init Action
     *
     * @return current Object
     */
    protected function _initAction() {
        $this->loadLayout()->_setActiveMenu('admintools/date');
        return $this;
    }

    /**
     * Index Action
     */
    public function indexAction() {
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Check is allow ACL
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('tangkoko_admintools/date');
    }

}