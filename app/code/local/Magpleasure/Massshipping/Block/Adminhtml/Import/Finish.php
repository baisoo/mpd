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

class Magpleasure_Massshipping_Block_Adminhtml_Import_Finish extends Magpleasure_Massshipping_Block_Adminhtml_Import_Abstract
{
    protected $_carrirData = false;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("massshipping/import/finish.phtml");
    }

    public function getDoneButtonHtml()
    {
        return $this->_getButtonHtml(array(
            'label' => $this->__("All Done"),
            'title' => $this->__("All Done"),
            'onclick' => "{$this->_helper()->getJsObjectName()}.processDone('home', this);",
            'class' => 'ms-button blue medium next',
            'id'    => 'button-next',
        ));
    }

    public function getChangeMatchingHtml()
    {
        return $this->_getLinkHtml(array(
            'label' => $this->__("Change Matching of Columns"),
            'onclick' => "{$this->_helper()->getJsObjectName()}.wantPage('grid', this);",
            'class' => 'ms-link change-matching',
            'id'    => 'change-matching',
            'href' => '#',
        ));
    }

    public function getQuote()
    {
        $quote = parent::getQuote();

        $lastQuote = $quote->getLastQuote();

        if ($lastQuote && $lastQuote->isUnfinished()){

            $lastQuote->reactivate();
            $quote->delete();
            return $this->_quote = $lastQuote;
        }

        return $quote;
    }

    public function getSpecialMessage()
    {
        return $this->_helper()->__("<strong>Almost done.</strong> Now let's match the carrier types in your uploaded list to your Shipping Data.");
    }

    public function useTracking()
    {
        return ($this->getIsDescriptionDefined() || $this->getIsCarrierDefined() || $this->getIsNumberDefined() || $this->getStoredUseTracking());
    }

    public function getIsDescriptionDefined()
    {
        return !!$this->getQuote()->getColumnByMatchKey(Magpleasure_Massshipping_Model_Column::TYPE_TITLE);
    }

    public function getIsCarrierDefined()
    {
        return !!$this->getQuote()->getColumnByMatchKey(Magpleasure_Massshipping_Model_Column::TYPE_CARRIER_CODE);
    }

    public function getIsNumberDefined()
    {
        return !!$this->getQuote()->getColumnByMatchKey(Magpleasure_Massshipping_Model_Column::TYPE_TRACK_NUMBER);
    }

    public function getIsSendEmailDefined()
    {
        return !!$this->getQuote()->getColumnByMatchKey(Magpleasure_Massshipping_Model_Column::TYPE_SEND_EMAIL);
    }

    public function getCarrierValues()
    {
        $result = array();
        $quote = $this->getQuote();

        if ($quote->getColumnByMatchKey(Magpleasure_Massshipping_Model_Column::TYPE_CARRIER_CODE)){


            $hasEmpty = false;
            foreach ($this->getQuote()->getGroupedRows(Magpleasure_Massshipping_Model_Column::TYPE_CARRIER_CODE) as $row){
                if ($row->getValue()){
                    $carrier = new Varien_Object(array(
                        'id' => $row->getId(),
                        'label' => $row->getValue(),
                    ));
                    $result[] = $carrier;
                } else {
                    $hasEmpty = true;
                }
            }

            if ($hasEmpty){

                # Add item for missed carrier
                $result = array_merge(
                    array(new Varien_Object(array(
                        'id' => Magpleasure_Massshipping_Model_Row::EMPTY_CARRIER,
                        'label' => $this->__("For missed carriers"),
                    ))),
                    $result
                );
            }

        } else {

            $carrier = new Varien_Object(array(
                'id' => Magpleasure_Massshipping_Model_Column::COMMON_CARRIER_VALUE,
                'label' => $this->__("For all records"),
            ));

            $result[] = $carrier;
        }
        return $result;
    }

    public function getCarrierOptions()
    {
        $add = array(
            '' => '',
        );
        /** @var $type Magpleasure_Massshipping_Model_Type_Carrier_Code */
        $type = Mage::getModel('massshipping/type_carrier_code');
        return array_merge($add, $type->toOptionArray());
    }


    public function getCarriersJson()
    {
        return Zend_Json::encode($this->getCarrierOptions());
    }

    public function getProgressComplete()
    {
        return $this->getQuote()->getProgressComplete();
    }

    public function getProgressTotal()
    {
        return $this->getQuote()->getProgressTotal();
    }

    protected function _getStoredData()
    {
        if (!$this->_carrirData){
            $quote = $this->getQuote();
            try {
                $this->_carrirData = unserialize($quote->getCarrierMatch());
            } catch (Exception $e){
                $this->_carrirData = array();
            }
        }
        return $this->_carrirData;
    }

    public function getStoredUseTracking()
    {
        $data = $this->_getStoredData();
        return isset($data['add_number_common']) && $data['add_number_common'];
    }

    public function getStoredSendEmail()
    {
        $data = $this->_getStoredData();
        return isset($data['send_email_common']) && $data['send_email_common'];
    }

    public function getStoredCarrier($id)
    {
        $data = $this->_getStoredData();
        return isset($data['carrier_'.$id]) ? $data['carrier_'.$id] : null;
    }

    public function getStoredTitle($id)
    {
        $data = $this->_getStoredData();
        return isset($data['title_'.$id]) ? $data['title_'.$id] : null;
    }

    public function getStoredDefaultNumber()
    {
        $data = $this->_getStoredData();
        return isset($data['number_common']) ? $data['number_common'] : null;
    }

}