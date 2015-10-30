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

class Magpleasure_Massshipping_Model_Row extends Magpleasure_Common_Model_Abstract
{
    const SALES_VERSION_LIMITATION_1 = '1.4.0.21';

    const STATUS_READY = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_FAILED = 3;
    const STATUS_FINISH = 4;

    const EMPTY_CARRIER = "__empty_carrier";

    protected $_quote;
    protected $_cells;
    protected $_order = false;

    /**
     * Helper
     *
     * @return Magpleasure_Massshipping_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('massshipping');
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_init('massshipping/row');
    }

    public function setQuote($quote)
    {
        $this->_quote = $quote;
        return $this;
    }

    /**
     * Quote
     *
     * @return Magpleasure_Massshipping_Model_Quote
     */
    public function getQuote()
    {
        if (!$this->_quote){
            /** @var $quote Magpleasure_Massshipping_Model_Quote */
            $quote = Mage::getModel('massshipping/quote')->load($this->getQuoteId());
            $this->_quote = $quote;
        }
        return $this->_quote;
    }

    /**
     * Cell by Column Id
     *
     * @param $columnId
     * @return Magpleasure_Massshipping_Model_Cell
     */
    public function getCellByColumnId($columnId)
    {
        foreach ($this->getCells() as $cell){
            /** @var $cell Magpleasure_Massshipping_Model_Cell */
            if ($cell->getColumnId() == $columnId){
                return $cell;
            }
        }
        return new Varien_Object();
    }

    public function getCells()
    {
        if (!$this->_cells){
            /** @var $cells Magpleasure_Massshipping_Model_Mysql4_Cell_Collection */
            $cells = Mage::getModel('massshipping/cell')->getCollection();
            $cells->addFieldToFilter('row_id', $this->getId());
            $this->_cells = $cells;
        }
        return $this->_cells;
    }

    /**
     * Order
     *
     * @return Magpleasure_Massshipping_Model_Order
     */
    public function getOrder()
    {
        if  (!$this->_order){
            if ($orderIncrementId = $this->_extractDataFromRow(Magpleasure_Massshipping_Model_Column::TYPE_ORDER_ID)){

                /** @var $order Magpleasure_Massshipping_Model_Order */
                $order = Mage::getModel('massshipping/order');
                $order->loadByIncrementId($orderIncrementId);
                if ($order->getId()){
                    $this->_order = $order;
                }
            }
        }
        return $this->_order;
    }

    /**
     * Decides if we need to create dummy shipment item or not
     * for eaxample we don't need create dummy parent if all
     * children are not in process
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @param array $qtys
     * @return bool
     */
    protected function _createLevel0ShipmentNeedToAddDummy($item, $qtys) {
        if ($item->getHasChildren()) {
            foreach ($item->getChildrenItems() as $child) {
                if ($child->getIsVirtual()) {
                    continue;
                }
                if ((isset($qtys[$child->getId()]) && $qtys[$child->getId()] > 0) || (!isset($qtys[$child->getId()]) && $child->getQtyToShip())) {
                    return true;
                }
            }
            return false;
        } else if($item->getParentItem()) {
            if ($item->getIsVirtual()) {
                return false;
            }
            if ((isset($qtys[$item->getParentItem()->getId()]) && $qtys[$item->getParentItem()->getId()] > 0)
                || (!isset($qtys[$item->getParentItem()->getId()]) && $item->getParentItem()->getQtyToShip())) {
                return true;
            }
            return false;
        }
        return false;
    }

    protected function _createLevel0Shipment($qtys = array())
    {
        $order = $this->getOrder();

        /** @var $convertor Mage_Sales_Model_Convert_Order */
        $convertor  = Mage::getModel('sales/convert_order');
        $shipment    = $convertor->toShipment($order);

        foreach ($order->getAllItems() as $orderItem) {
            if (!$orderItem->isDummy(true) && !$orderItem->getQtyToShip()) {
                continue;
            }

            if ($orderItem->isDummy(true) && !$this->_createLevel0ShipmentNeedToAddDummy($orderItem, $qtys)) {
                continue;
            }

            if ($orderItem->getIsVirtual()) {
                continue;
            }

            $item = $convertor->itemToShipmentItem($orderItem);
            if (isset($qtys[$orderItem->getId()])) {
                if ($qtys[$orderItem->getId()] > 0) {
                    $qty = $qtys[$orderItem->getId()];
                } else {
                    continue;
                }
            } else {
                if ($orderItem->isDummy(true)) {
                    $qty = 1;
                } else {
                    $qty = $orderItem->getQtyToShip();
                }
            }

            $item->setQty($qty);
            $shipment->addItem($item);
        }

        $shipment->register();
        $shipment->getOrder()->setIsInProcess(true);
        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($shipment)
            ->addObject($shipment->getOrder())
            ->save();

        return $shipment;
    }

    protected function _createLevel1Shipment($qtys = array())
    {
        $order = $this->getOrder();

        /** @var $service Mage_Sales_Model_Service_Order */
        $service = Mage::getModel('sales/service_order', $order);

        /** @var $shipment Mage_Sales_Model_Order_Shipment */
        $shipment = $service->prepareShipment($qtys);

        $shipment->register();

        # Notify by email
        if ($this->_getNotifyCustomer()){
            $shipment->setEmailSent(true);
            $shipment->getOrder()->setCustomerNoteNotify(true);
        }

        $this->getOrder()->setIsInProcess(true);

        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($shipment)
            ->addObject($shipment->getOrder())
            ->save();


        return $shipment;
    }

    /**
     * Create Shipment
     *
     * @param array $qtys
     * @return Mage_Sales_Model_Order_Shipment
     */
    protected function _createShipment($qtys = array())
    {
        $salesVersion = $this->_helper()->getCommon()->getMagento()->getModuleVersion("Mage_Sales");

        if (version_compare(self::SALES_VERSION_LIMITATION_1, $salesVersion, ">=")){
            return $this->_createLevel0Shipment($qtys);
        } else {
            return $this->_createLevel1Shipment($qtys);
        }
    }

    /**
     * Retrieves row value
     *
     * @param string $valueKey
     * @return mixed
     */
    public function findRowValue($valueKey)
    {
        return $this->_extractDataFromRow($valueKey);
    }

    protected function _findValue($valueKey)
    {
        if ($data = $this->_extractDataFromRow($valueKey)){
            return $data;
        } else {
            return $this->_getCarrierData($valueKey);
        }
    }

    protected function _getCarrierData($dataKey, $isCommon = true)
    {
        try {
            if ($carrierMatch = $this->getQuote()->getCarrierMatch()){
                $carrierData = unserialize($carrierMatch);
                $key = $dataKey.($isCommon ? "_common" : "");
                return isset($carrierData[$key]) ? $carrierData[$key] : false;
            }
        } catch (Exception $e){  }
        return false;
    }

    protected function _getNotifyCustomer()
    {
        return !!$this->_findValue(Magpleasure_Massshipping_Model_Column::TYPE_SEND_EMAIL);
    }

    protected function _findTrackCarrierTitleAssociation()
    {
        try {
            if ($carrierMatch = $this->getQuote()->getCarrierMatch()){
                $carrierData = unserialize($carrierMatch);

                if (!$this->getQuote()->getColumnByMatchKey(Magpleasure_Massshipping_Model_Column::TYPE_CARRIER_CODE)){
                    return isset($carrierData[Magpleasure_Massshipping_Model_Column::TYPE_TITLE."_common"]) ? $carrierData[Magpleasure_Massshipping_Model_Column::TYPE_TITLE."_common"] : false;
                } else {
                    $rowId = false;

                    foreach ($this->getQuote()->getGroupedRows(Magpleasure_Massshipping_Model_Column::TYPE_CARRIER_CODE) as $row){

                        $carrierValue = $this->_getTrackCarrierValue();
                        if ($row->getValue()){
                            if ($row->getValue() == $carrierValue){
                                $rowId = $row->getId();
                            }
                        } else {
                            $rowId = self::EMPTY_CARRIER;
                        }

                    }

                    if ($rowId){
                        foreach ($carrierData as $key=>$value){
                            if ($key == Magpleasure_Massshipping_Model_Column::TYPE_TITLE."_".$rowId){
                                return $value;
                            }
                        }
                    }
                }
            }
        } catch (Exception $e){  }
        return false;
    }

    protected function _findTrackCarrierAssociation($carrierValue)
    {
        try {
            if ($carrierMatch = $this->getQuote()->getCarrierMatch()){
                $carrierData = unserialize($carrierMatch);
                if (!$this->getQuote()->getColumnByMatchKey(Magpleasure_Massshipping_Model_Column::TYPE_CARRIER_CODE)){
                    return isset($carrierData[Magpleasure_Massshipping_Model_Column::TYPE_CARRIER_CODE."_common"]) ? $carrierData[Magpleasure_Massshipping_Model_Column::TYPE_CARRIER_CODE."_common"] : false;
                } else {

                    $rowId = false;
                    foreach ($this->getQuote()->getGroupedRows(Magpleasure_Massshipping_Model_Column::TYPE_CARRIER_CODE) as $row){
                        if ($row->getValue()){
                            if ($row->getValue() == $carrierValue){
                                $rowId = $row->getId();
                            }
                        } else {
                            $rowId = self::EMPTY_CARRIER;
                        }
                    }

                    if ($rowId){
                        foreach ($carrierData as $key=>$value){
                            if ($key == Magpleasure_Massshipping_Model_Column::TYPE_CARRIER_CODE."_".$rowId){
                                return $value;
                            }
                        }
                    }
                }
            }
        } catch (Exception $e){
            $this->_helper()->getCommon()->getException()->logException($e);
        }
        return false;
    }

    protected function _getCommonTrackCarrier()
    {
        $valueKey = Magpleasure_Massshipping_Model_Column::TYPE_CARRIER_CODE;
        return $this->_getCarrierData($valueKey);
    }

    protected function _getTrackCarrier()
    {
        $valueKey = Magpleasure_Massshipping_Model_Column::TYPE_CARRIER_CODE;
        if ($data = $this->_extractDataFromRow($valueKey)){
            return $data;
        } else {
            return self::EMPTY_CARRIER;
        }
    }

    protected function _getTrackCarrierValue()
    {
        return $this->_extractDataFromRow(Magpleasure_Massshipping_Model_Column::TYPE_CARRIER_CODE);
    }

    protected function _findCarrierTitleAssociation()
    {
        return $this->_findTrackCarrierTitleAssociation();
    }

    public function _getTrackTitle()
    {
        if ($data = $this->_extractDataFromRow(Magpleasure_Massshipping_Model_Column::TYPE_TITLE)){
            return $data;
        } else {
            return $this->_findCarrierTitleAssociation();
        }
    }

    protected function _getTrackNumber()
    {
        return $this->_findValue(Magpleasure_Massshipping_Model_Column::TYPE_TRACK_NUMBER);
    }

    protected function _addTrack()
    {
        return !!$this->_getCarrierData('add_number');
    }

    protected function _getCommonTrackNumber()
    {
        return $this->_getCarrierData(Magpleasure_Massshipping_Model_Column::TYPE_TRACK_NUMBER);
    }

    protected function _getThisRowTrackNumber()
    {
        return $this->_extractDataFromRow(Magpleasure_Massshipping_Model_Column::TYPE_TRACK_NUMBER);
    }

    protected function _addTrackToShipment(Mage_Sales_Model_Order_Shipment $shipment, $trackNumber, $isCommon = true)
    {
        if ($isCommon){
            $trackCarrier = $this->_getCommonTrackCarrier();
        } else {
            $trackCarrier = $this->_getTrackCarrier();
        }

        if ($trackCarrier){

            $trackTitle = $this->_getTrackTitle();

            /** @var Mage_Sales_Model_Order_Shipment_Track $track  */
            $track = Mage::getModel('sales/order_shipment_track')
                ->setNumber($trackNumber)
                ->setCarrierCode($trackCarrier)
                ->setTitle($trackTitle)
            ;

            $shipment
                ->addTrack($track)
                ->save();

            $track->save();


        } else {
            Mage::throwException($this->_helper()->__("Track Carrier is not defined."));
        }

        return $this;
    }

    protected function _findLinkedRowsFor(Mage_Sales_Model_Order $order, $trackNumber = null)
    {
        /** @var $rows Magpleasure_Massshipping_Model_Mysql4_Row_Collection */
        $rows = Mage::getModel('massshipping/row')->getCollection();
        $rows
            ->addQuoteData($this->getQuote())
            ->addQuoteFilter($this->getQuote()->getId())
            ->addColumnFilter(Magpleasure_Massshipping_Model_Column::TYPE_ORDER_ID, $order->getIncrementId())
            ->addFieldToFilter('status', self::STATUS_READY)
            ->addFieldToFilter('row_id', array('nin' => $this->getId()))
            ;

        if ($trackNumber){
            $rows->addColumnFilter(Magpleasure_Massshipping_Model_Column::TYPE_TRACK_NUMBER, $trackNumber);
        }

        return $rows;
    }

    protected function _extractDataFromRow($dataKey)
    {
        if ($column = $this->getQuote()->getColumnByMatchKey($dataKey)){
            if ($cell = $this->getCellByColumnId($column->getId())){
                return $cell->getValue();
            }
        }
        return false;
    }

    protected function _getThisRowItemSku()
    {
        return $this->_extractDataFromRow(Magpleasure_Massshipping_Model_Column::TYPE_ITEM_SKU);
    }

    public function collectItemData()
    {

        $items = array();

        /** @var $row Magpleasure_Massshipping_Model_Row */
        $itemSkus = $this->findRowValue(Magpleasure_Massshipping_Model_Column::TYPE_ITEM_SKU);
        $itemSkus = explode(",", $itemSkus);

        foreach ($itemSkus as $itemSku){
            $rowQty = $this->findRowValue(Magpleasure_Massshipping_Model_Column::TYPE_ITEM_QTY);

            $sku = trim($itemSku);
            $item = $this->getOrder()->getItemBySku($sku);
            $qty = false;

            if ($rowQty){
                $unshippedQty = $this->getOrder()->getUnshippedQty($sku);

                if ($rowQty > $unshippedQty){
                    Mage::throwException($this->_helper()->__("Process error: It's impossible to ship %s items for %s", $rowQty, $sku));
                }

                if ($unshippedQty){
                    $qty = $rowQty;
                }

            } else {
                if ($unshippedQty = $this->getOrder()->getUnshippedQty($sku)){
                    $qty = $unshippedQty;
                }
            }

            if ($sku && $qty){
                $items[$item->getId()] = $qty;
            } else {
                Mage::throwException($this->_helper()->__("Process error: Item with SKU '%s' can not be shipped for requested quantity."));
            }
        }

        return $items;
    }

    public function process()
    {
        if ($this->getId()){

            try {

                if (!$this->canProcess()){
                    # Skip this row
                    return $this;
                }

                $this
                    ->setStatus(self::STATUS_PROCESSING)
                    ->save();

                if ($order = $this->getOrder()){

                    # Check possibility to ship order
                    if ($order->canShip()){

                        #################
                        ## Process Row ##
                        #################

                        # Has Personal Tracking Number
                        if ($trackNumber = $this->_getThisRowTrackNumber()){

                            $linkedRows = $this->_findLinkedRowsFor($order, $trackNumber);

                            # Has Personal Item SKU
                            if ($itemSku = $this->_getThisRowItemSku()){
                                $itemData = $this->collectItemData() + $linkedRows->collectItemData();

                            # Item SKU isn't defined
                            } else {
                                $itemData = $order->collectItemData();
                            }

                            if (!empty($itemData)){

                                /** @var $shipment Mage_Sales_Model_Order_Shipment */
                                $shipment = $this->_createShipment($itemData);

                                if ($this->_addTrack()){
                                    $this->_addTrackToShipment($shipment, $trackNumber, false);
                                }

                                if ($this->_getNotifyCustomer()){
                                    $shipment->sendEmail();
                                }
                            } else {
                                Mage::throwException($this->_helper()->__("Process error: Could not find item data for '%s'", $itemSku));
                            }

                        # Track Number isn't defined per Row
                        } else {

                            $linkedRows = $this->_findLinkedRowsFor($order);

                            # Has Personal Item SKU
                            if ($itemSku = $this->_getThisRowItemSku()){
                                $itemData = $this->collectItemData() + $linkedRows->collectItemData();

                                # Item SKU isn't defined
                            } else {
                                $itemData = $order->collectItemData();
                            }

                            if (!empty($itemData)){

                                /** @var $shipment Mage_Sales_Model_Order_Shipment */
                                $shipment = $this->_createShipment($itemData);

                                if ($this->_addTrack()){
                                    if ($trackNumber = $this->_getCommonTrackNumber()){
                                        $this->_addTrackToShipment($shipment, $trackNumber, true);
                                    }
                                }

                                if ($this->_getNotifyCustomer()){
                                    $shipment->sendEmail();
                                }
                            }
                        }

                        $this->success($linkedRows->getAllIds());

                    } else {
                        Mage::throwException("Process error: Order can't be shipped.");
                    }

                } else {
                    $this->failed($this->_helper()->__("Process error: Order is not found."), isset($linkedRows) ? $linkedRows->getAllIds() : array());
                }
            } catch (Exception $e) {
                $this->_helper()->getCommon()->getException()->logException($e);
                $this->failed($e->getMessage(), isset($linkedRows) ? $linkedRows->getAllIds() : array());
            }

        } else {
            Mage::throwException($this->_helper()->__("Sorry. Can't continue."));
        }
        return $this;
    }

    public function failed($message = null, array $linkedRows = array())
    {
        if (!empty($linkedRows)){

            /** @var $rows Magpleasure_Massshipping_Model_Mysql4_Row_Collection */
            $rows = Mage::getModel('massshipping/row')->getCollection();
            $rows->addFieldToFilter('row_id', array('in'=>$linkedRows));
            foreach ($rows as $row){
                /** @var $row Magpleasure_Massshipping_Model_Row */
                $row = $row->load($row->getId());
                if ($row->getId()){
                    $row->failed($message);
                }
            }
        }

        $this
            ->setStatus(self::STATUS_FAILED)
            ->setMessage($message)
            ->save();


        return $this;
    }

    public function success(array $linkedRows = array())
    {
        if (!empty($linkedRows)){

            /** @var $rows Magpleasure_Massshipping_Model_Mysql4_Row_Collection */
            $rows = Mage::getModel('massshipping/row')->getCollection();
            $rows->addFieldToFilter('row_id', array('in'=>$linkedRows));
            foreach ($rows as $row){
                /** @var $row Magpleasure_Massshipping_Model_Row */
                $row = $row->load($row->getId());
                if ($row->getId()){
                    $row->success();
                } else {
                    Mage::throwException($this->_helper()->__("Internal error: Row couldn't be found while process Parent."));
                }
            }

        }

        $this
            ->setStatus(self::STATUS_FINISH)
            ->save();

        return $this;
    }

    public function canProcess()
    {
        return $this->getStatus() == self::STATUS_READY;
    }

}