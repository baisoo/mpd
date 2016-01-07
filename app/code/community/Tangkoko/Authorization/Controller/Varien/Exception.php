<?php
/**
 * Controller exception that can fork different actions, cause forward or redirect
 *
 * @category    Tangkoko
 * @package     Tangkoko_Authorization
 * @author      Tangkoko <support@tangkoko.com>
 * @copyright   Copyright (c) 2015 Tangkoko (http://www.tangkoko.com)
 * @license     All rights reserved
 */

class Tangkoko_Authorization_Controller_Varien_Exception extends Mage_Core_Controller_Varien_Exception
{
    /**
     * Bugfix
     *
     * @see Mage_Core_Controller_Varien_Exception::prepareRedirect()
     */
    public function prepareRedirect($path, $arguments = array())
    {
        $this->_resultCallback = self::RESULT_REDIRECT;
        $this->_resultCallbackParams = array($path, $arguments);
        return $this;
    }
}