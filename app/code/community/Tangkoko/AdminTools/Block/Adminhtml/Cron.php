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

class Tangkoko_AdminTools_Block_Adminhtml_Cron extends Mage_Core_Block_Template
{
	/**
	 * Get All Cron Jobs
	 *
	 * @return array
	 */
	public function getCron()
	{
		$events = Mage::getConfig()->init()->getNode("crontab/jobs");
		$events = $events->children();
		
		$listOfCron = array();
		
		foreach ($events as $event) {
			$listOfCron[]['name'] = $event->getName();
		}
		
		return $listOfCron;
	}

}
