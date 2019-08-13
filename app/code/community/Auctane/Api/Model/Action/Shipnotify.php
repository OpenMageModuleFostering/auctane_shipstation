<?php

class Auctane_Api_Model_Action_Shipnotify {

	/**
	 * Perform a notify using POSTed data.
	 *
	 * See Auctane API specification.
	 *
	 * @param Mage_Core_Controller_Request_Http $request
	 * @throws Exception
	 */
	public function process(Mage_Core_Controller_Request_Http $request)
	{
		// Raw XML is POSTed to this stream
		$xml = simplexml_load_file('php://input');

		// load some objects
		$order = $this->_getOrder($xml->OrderNumber);
		$carrier = $this->_getCarrier(@$xml->Carrier);
		$qtys = $this->_getOrderItemQtys(@$xml->Items, $order);
		$shipment = $this->_getOrderShipment($order, $qtys);

		// this is where tracking is actually added
		$track = Mage::getModel('sales/order_shipment_track')
			->setNumber($xml->TrackingNumber)
			->setCarrierCode($xml->Carrier)
			->setTitle($xml->Service);
		$shipment->addTrack($track);

		// 'NotifyCustomer' must be "true" or "yes" to trigger an email
		$notify = filter_var(@$xml->NotifyCustomer, FILTER_VALIDATE_BOOLEAN);

		$capture = filter_var($request->getParam('capture'), FILTER_VALIDATE_BOOLEAN);
		if ($capture && $order->canInvoice()) {
			$invoice = $order->prepareInvoice($qtys);
			$invoice->setRequestedCaptureCase($invoice->canCapture() ? 'online' : 'offline')
					->register() // captures & updates order totals
					->addComment($this->_getInvoiceComment(), $notify)
					->sendEmail($notify); // always send to store manager, and optionally notify customer too
			$order->setIsInProcess(true); // updates status on save
		}

		// Internal notes are only visible to admin
		if (@$xml->InternalNotes) {
			$shipment->addComment($xml->InternalNotes);
		}
		// Customer notes have 'Visible On Frontend' set
		if ($notify) {
			// if no NotesToCustomer then comment is empty string
			$shipment->sendEmail(true, (string) @$xml->NotesToCustomer)
					 ->setEmailSent(true);
		}
		if (@$xml->NotesToCustomer) {
			$shipment->addComment($xml->NotesToCustomer, $notify, true);
		}

		$transaction = Mage::getModel('core/resource_transaction');
		$transaction->addObject($shipment)
					->addObject($track);
		if (isset($invoice)) {
			// order has been captured, therefore has been modified
			$transaction->addObject($invoice)
						->addObject($order);
		}
		$transaction->save();

		if( $order->canInvoice() && !$order->canShip() ){ // then silently invoice if order is shipped to move status to "Complete")
			$invoice = $order->prepareInvoice();
			$invoice->setRequestedCaptureCase($invoice->canCapture() ? 'online' : 'offline')
					->register() // captures & updates order totals
					->addComment($this->_getInvoiceComment(), false)
					->sendEmail(false); // always send to store manager, and optionally notify customer too
			$order->setIsInProcess(true); // updates status on save

			$transaction = Mage::getModel('core/resource_transaction');
			if (isset($invoice)) {
				// order has been captured, therefore has been modified
				$transaction->addObject($invoice)
							->addObject($order);
			}
			$transaction->save();
		}

	}

	/**
	 * Configurable comment, in case language needs to be changed.
	 *
	 * @return string
	 */
	protected function _getInvoiceComment()
	{
		return Mage::getStoreConfig('auctaneapi/config/invoiceComment');
	}

	/**
	 * @param string $carrierCode
	 * @return Mage_Shipping_Model_Carrier_Interface
	 */
	protected function _getCarrier($carrierCode)
	{
		$carrierCode = strtolower($carrierCode);
		$carrierModel = Mage::getStoreConfig("carriers/{$carrierCode}/model");
		if (!$carrierModel) throw new Exception('Invalid carrier specified.', 400);
		/* @var $carrier Mage_Shipping_Model_Carrier_Interface */
		$carrier = Mage::getModel($carrierModel);
		if (!$carrier) throw new Exception('Invalid carrier specified.', 400);
		if (!$carrier->isTrackingAvailable()) throw new Exception('Carrier does not supported tracking.', 400);
		return $carrier;
	}

	/**
	 * @param string $orderIncrementId
	 * @return Mage_Sales_Model_Order
	 */
	protected function _getOrder($orderIncrementId)
	{
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
		if ($order->isObjectNew()) throw new Exception("Order '{$orderIncrementId}' does not exist", 400);
		return $order;
	}

	protected function _getOrderItemQtys(SimpleXMLElement $xmlItems, Mage_Sales_Model_Order $order)
	{
		/* @var $items Mage_Sales_Model_Mysql4_Order_Item_Collection */
		$items = $order->getItemsCollection();
		$qtys = array();
		/* @var $item Mage_Sales_Model_Order_Item */
		foreach ($items as $item) {
			// search for item by SKU
			@list($xmlItem) = $xmlItems->xpath(sprintf('//Item/SKU[text()="%s"]/..',
				addslashes($item->getSku())));
			if ($xmlItem) {
				// store quantity by order item ID, not by SKU
				$qtys[$item->getId()] = (float) $xmlItem->Quantity;
			}
		}
		return $qtys;
	}

	/**
	 * @param Mage_Sales_ModelOrder $order
	 * @param array $qtys
	 * @return Mage_Sales_Model_Order_Shipment
	 */
	protected function _getOrderShipment(Mage_Sales_Model_Order $order, $qtys)
	{
		$shipment = $order->prepareShipment($qtys);
		$shipment->register();
		$order->setIsInProgress(true);

		// shipment must have an ID before proceeding
		Mage::getModel('core/resource_transaction')
			->addObject($shipment)
			->addObject($order)
			->save();

		return $shipment;
	}

}
