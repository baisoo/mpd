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
 * @package    Magpleasure_Common
 * @version    0.6.0
 * @copyright  Copyright (c) 2012-2013 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */
class Magpleasure_Common_Helper_Config extends Mage_Core_Helper_Abstract
{
    /**
     * Reprieves Value from Path
     *
     * @param $path
     * @return boolean|string
     */
    public function getValueFromPath($path)
    {
        $data = Mage::app()->getConfig()->getNode($path);
        $result = (string)$data;
        if ($result){
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Rtrieves Array of Values from Path
     *
     * @param $path
     * @return boolean|array
     */
    public function getArrayFromPath($path)
    {
        $data = Mage::app()->getConfig()->getNode($path);
        $result = (array)$data;
        if ($result && is_array($result)){
            $out = array();
            foreach ($result as $name => $attrs){
                if ($name){
                    $out[] = $name;
                }
            }
            return $out;
        } else {
            return false;
        }
    }
}