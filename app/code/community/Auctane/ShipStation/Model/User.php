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

class Auctane_ShipStation_Model_User extends Mage_Core_Model_Abstract
{

	protected function _construct()
	{
		$this->_init('auctaneshipstation/user');
	}

	public function load($user, $field=null)
	{
		if (is_object($user)) {
			return parent::load($user->getUserId(), 'admin_user_id');
		}
		else {
			return parent::load($user, $field);
		}
	}

	/**
	 * Perform authentication with remote server
	 * @return unknown
	 */
	public function authenticate()
	{
		$client = new Varien_Http_Client(Mage::getStoreConfig('auctane/shipstation/authenticate_url'), array(
			'storeresponse'	=>	true
		));
		$request = '<AuthenticationRequest StoreUrl="' . $this->getRequestUrl() . '"'
	             . ' Username="' . $this->getRequestUsername() . '"'
	             . ' Password="' . $this->getRequestPassword() . '"'
	             . ' />';
	    $client->setRawData($request, 'text/xml')
	           ->request(Varien_Http_Client::POST);

		$response = $client->getLastResponse();
		if ($response->isError()) {
			throw new Exception($response->getMessage());
		}

		$xml = new SimpleXMLElement($response->getBody());
		if ($xml['Success'] != 'true') {
			if ($xml['ErrorMessage'])
				throw new Exception($xml['ErrorMessage']);
			else
				throw new Exception(Mage::helper('auctaneshipstation')->__('Unable to authenticate'));
		}

		$this->setAuthToken($xml['AuthToken'])
			->setAuthUrl($xml['AuthUrl']);
		if (!$this->hasAdminUserId()) {
			$user = Mage::helper('auctaneshipstation')->getAdminUser();
			if ($user) {
				$this->setAdminUserId($user->getUserId());
			}
		}

		return $this;
	}

	// Shortcuts for encrypted field
	public function getAuthToken()
	{
		return Mage::helper('auctaneshipstation')->decrypt($this->getAuthTokenEnc());
	}

	public function setAuthToken($token)
	{
		return $this->setAuthTokenEnc(Mage::helper('auctaneshipstation')->encrypt($token));
	}

	public function hasAuthToken()
	{
		return $this->hasAuthTokenEnc();
	}

	public function unsAuthToken()
	{
		return $this->unsAuthTokenEnc();
	}

}
