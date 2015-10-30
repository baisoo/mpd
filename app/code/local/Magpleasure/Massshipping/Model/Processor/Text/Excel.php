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

class Magpleasure_Massshipping_Model_Processor_Text_Excel extends Magpleasure_Massshipping_Model_Processor_Abstract
{
    public function process($data)
    {
        $result = array();
        $rows = explode("\n", $data);
        $lastRow = false;
        if ($data){
            foreach ($rows as $row){
                $dataCells = explode("\t", $row);
                if (!$lastRow || (count($dataCells) == count($lastRow))){
                    $result[] = $dataCells;
                }
                $lastRow = $dataCells;
            }
        }
        return $result;
    }
}