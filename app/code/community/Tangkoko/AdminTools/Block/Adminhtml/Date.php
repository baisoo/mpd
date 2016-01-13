<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * @category    Tangkoko
 * @package     Tangkoko_AdminTools
 * @author      Louis Cailleux
 * @copyright   Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Tangkoko_AdminTools_Block_Adminhtml_Date extends Mage_Core_Block_Template
{
	const FORMAT = 'Y-m-d H:i:s';
	
	public function showServerTime()
	{
		$date = Mage::getModel('core/date')->gmtDate(self::FORMAT);
		$html = $this->__('Server Time:') . '<span class="separator">|</span>' . $date . '<br/>';
		return $html;
	}
	
	public function showLocaleTime() 
	{
		$html = $this->__('Locale Time:') . '<span class="separator">|</span>' . Mage::getModel('core/date')->date(self::FORMAT). '<br/>';
		return $html;
	}
}