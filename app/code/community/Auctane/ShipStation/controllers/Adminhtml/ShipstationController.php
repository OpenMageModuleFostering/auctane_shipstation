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

class Auctane_ShipStation_Adminhtml_ShipstationController extends Mage_Adminhtml_Controller_Action
{

	/**
	 * Ensure all pages have a decent title
	 */
	public function preDispatch()
	{
		$this->_title($this->__('ShipStation'));
		return parent::preDispatch();
	}

	/**
	 * Display a launch page, or redirect to setup page if necessary
	 */
	public function indexAction()
	{
		if (Mage::helper('auctaneshipstation')->setupRequired()) {
			$this->_redirect('*/shipstation/setup');
			return;
		}

		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * Launch page needs a GMT value and javascript is not timezone aware
	 * This is an AJAX action
	 * 
	 * @deprecated
	 */
	public function timestampAction()
	{
		$timestamp = new DateTime();
		$timestamp->setTimezone(new DateTimeZone('UTC'));
		$this->getResponse()->setBody($timestamp->format('m/d/Y H:i:s'));
	}

	/**
	 * Display a setup page for registering
	 */
	public function setupAction()
	{
		$this->_title($this->__('Register'));
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * The setup's "linkup" button goes here, register with shipstation server.
	 */
	public function linkupAction()
	{
		try {

			$user = Mage::getModel('auctaneshipstation/user');
			$user->setData($this->getRequest()->getPost());
			$user->setRequestUrl(Mage::helper('auctaneshipstation')->getStoreDomain($user->getStoreId()));
			$user->authenticate();
			// If there is an error it will abort here, no save.
			$user->save();
			$this->_redirect('*/shipstation');

		} catch (Exception $e) {

			$this->_getSession()->addException($e, $e->getMessage());
			$this->_redirect('*/shipstation/setup');

		}
	}

}

