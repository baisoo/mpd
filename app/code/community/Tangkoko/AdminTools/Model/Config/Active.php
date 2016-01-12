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

class Tangkoko_AdminTools_Model_Config_Active
{
    /**
     * Provide available zoom config options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>'Yes'),
            array('value'=>0, 'label'=>'No'),
        );
    }
}