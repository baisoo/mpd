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

/** Dictationary */
class Magpleasure_Common_Model_Type_Dictionary_String extends Magpleasure_Common_Model_Type_Dictionary
{
    const MIN_SIMILARITY_DISTANCE = 1;

    public function strlen($value)
    {
        return $this->_commonHelper()->getStrings()->strlen($value);
    }

    public function getSimilarKey($target)
    {
        foreach ($this->keys() as $source){
            $distance = $this->_commonHelper()->getStrings()->dlWordDistance($source, $target);
            if ($distance <= self::MIN_SIMILARITY_DISTANCE){
                return $source;
            }
        }
        return false;
    }
}