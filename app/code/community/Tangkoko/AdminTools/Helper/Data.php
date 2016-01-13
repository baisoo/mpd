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

class Tangkoko_AdminTools_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isDateActive()
	{
		return (bool)Mage::getStoreConfig('tangkoko_admintools/date/date_active');
	}
}
