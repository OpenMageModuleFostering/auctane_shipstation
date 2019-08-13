<?php
/**
 * ShipStation
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@auctane.com so we can send you a copy immediately.
 *
 * @category   Shipping
 * @package    Auctane_ShipStation
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Auctane_ShipStation_Model_Observer {

	public function appendDashboardSalesHtml(Varien_Event_Observer $observer)
	{
		$block = $observer->getBlock();
		$transport = $observer->getTransport();
		/* @var $block Mage_Adminhtml_Block_Dashboard_Sales */
		if ($block->getType() == 'adminhtml/dashboard_sales') {
			$transport->setHtml($transport->getHtml() . $block->getLayout()->getBlock('pendingorders')->toHtml());
		}
	}

}
