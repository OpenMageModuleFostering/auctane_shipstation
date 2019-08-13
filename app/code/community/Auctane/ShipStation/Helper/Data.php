<?php

class Auctane_ShipStation_Helper_Data extends Mage_Core_Helper_Data
{

	/**
	 * Get current admin from session
	 * 
	 * @return Mage_Admin_Model_User
	 */
	public function getAdminUser()
	{
		$session = Mage::getSingleton('admin/session');
		if (!$session->isLoggedIn()) {
			return false;
		}

		return $session->getUser();
	}

	/**
	 * Get ShipStation user object from current admin session
	 * 
	 * @param Mage_Admin_Model_User $admin
	 * @return Auctane_ShipStation_Model_User
	 */
	public function getUser($admin = null)
	{
		if (!$admin) $admin = $this->getAdminUser();
		return Mage::getModel('auctaneshipstation/user')->load($admin);
	}

	public function setupRequired()
	{
		$admin = $this->getAdminUser();
		if (!$admin) return false;

		$user = $this->getUser($admin);

		return $user->isObjectNew() || !$user->hasAuthToken() || !$user->hasAuthUrl();
	}

	public function getStoreDomain($storeId)
	{
		$url = Mage::getStoreConfig('web/unsecure/base_url', $storeId);
		return preg_replace('/^https?:\/\/([[:alnum:]\.]+).*/', '\\1', $url);
	}

}
