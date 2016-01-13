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

class Tangkoko_AdminTools_Block_Adminhtml_Logs extends Mage_Core_Block_Template
{

    /**
     * Get All Logs Files
     *
     * @return array
     */
    public function getLogs()
    {
        $logsPath = Mage::getBaseDir('log');
        $dir = opendir($logsPath);
        $logsArray = array();

        while(($file = readdir($dir)) !== false) {
            $path = "log/".$file;
            $infos = pathinfo($path);
            $extension = $infos['extension'];

            if($file != "." && $file != ".." && !is_dir($file) && $extension == "log") {
                $logsArray[] = $file;
            }
        }
        return $logsArray;
    }
}
