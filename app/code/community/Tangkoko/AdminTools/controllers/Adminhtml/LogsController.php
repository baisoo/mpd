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

class Tangkoko_AdminTools_Adminhtml_LogsController extends Mage_Adminhtml_Controller_action
{

	/**
	 * Init Action
	 *
	 * @return current Object
	 */
	protected function _initAction()
	{
		$this->loadLayout()
		->_setActiveMenu('admintools/logs');
		return $this;
	}

	/**
	 * Index Action
	 */
	public function indexAction()
	{
		$this->_initAction();
		$this->renderLayout();
	}

	/**
	 * showLogs Action. return HTML Ajax Content
	 */
	public function showLogsAction()
	{
		$srcLog = $this->getRequest()->getParam('log');
		$sizeLog = $this->getRequest()->getParam('size');

		$logPath = Mage::getBaseDir('log').DS.$srcLog;
		
		if(file_exists($logPath)) {
			$logSize = filesize($logPath);
			
			if($sizeLog == "all" && $logSize >= 10485760)
			{
				//using $this->getResponse() instead of "echo" avoid HEADERS ALREADY SENT error 
				$this->getResponse()
				->clearHeaders()
				->setHeader('Content-Type', 'text/html')
				->setBody("<p>".Mage::helper('tangkoko_admintools')->__("Your file is bigger than 10MB. I can't handle it !")."</p><p><img src='http://seedwell.com/blog/wp-content/uploads/2011/11/swblog-viral-postpics-112811e.jpg' alt='...'></img>");
			}
			else
			{
				if($sizeLog != "all") {
					$fp = fopen($logPath, "r");
					fseek($fp, -($sizeLog*1000), SEEK_END);
					$position = ftell($fp);
					$logcontent = fgets($fp, ($sizeLog*1000));
					while (($buffer = fgets($fp, 4096)) !== false) {
						$logcontent .= $buffer;
					}
				}
				else {
					$logcontent = file_get_contents($logPath);
				}

				//adding date color
				preg_match_all("/(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})/", $logcontent, $matches, PREG_PATTERN_ORDER);
				if(count($matches)>1)
				{
					if(isset($matches[0])) {
						foreach($matches[0] as $match)
						{
							$match2 = str_replace("T"," | ",$match);
							$logcontent = str_replace($match,"<br/><span style='color:blue'><strong>".$match2."</strong></span>",$logcontent);
						}
				
					}
				}
				
				//adding error type colors
				$logcontent =str_replace("Notice:","<span style='color:#FFDF7F'><strong>Notice:</strong></span>",$logcontent);
				$logcontent =str_replace("DEBUG","<span style='color:#FF1C2B'><strong>DEBUG</strong></span>",$logcontent);
				$logcontent =str_replace("Warning:","<span style='color:#FFA768'><strong>Warning:</strong></span>",$logcontent);
				$logcontent =str_replace("ERR","<strong>ERR</strong>",$logcontent);
				
				//send response
				$this->getResponse()
				->clearHeaders()
				->setHeader('Content-Type', 'text/html')
				->setBody($logcontent);
			}
		}
		else
		{
			//send response
			$this->getResponse()
			->clearHeaders()
			->setHeader('Content-Type', 'text/html')
			->setBody(Mage::helper('tangkoko_admintools')->__("Dude, this log file doesn't exist, believe me."));
		}
		
	}

	/**
	 * deleteLog Action
	 */
	public function deleteLogAction()
	{
		$typeLog = $this->getRequest()->getParam('log');
		$logPath = Mage::getBaseDir('log').DS.$typeLog;
		
		if(file_exists($logPath)) {
			try {
				unlink($logPath);
			} 
			catch (Exception $e) {
				Mage::log($e);
			}
		}
		
		$this->_redirect('*/*/index');
	}


	/**
	 * Check is allow ACL
	 *
	 * @return bool
	 */
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('tangkoko_admintools/logs');
	}
}
