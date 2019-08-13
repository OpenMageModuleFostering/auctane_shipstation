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

class Auctane_ShipStation_Block_Adminhtml_Linkup_Form extends Mage_Adminhtml_Block_Widget_Form
{

	protected function _prepareForm()
	{
		$stores = Mage::getSingleton('adminhtml/system_config_source_store')->toOptionArray();

		$form = new Varien_Data_Form(array(
			'id'	=>	'edit_form',
			'action'=>	$this->getUrl('*/shipstation/linkup'),
			'method'=>	'post'
		));
		if (count($stores) > 1) {
			$form->addField('store_id', 'select', array(
				'name'		=>	'store_id',
				'label'		=>	$this->__('Store View:'),
				'values'	=>	$stores
			));
		}
		$form->addField('request_username', 'text', array(
			'name'	=>	'request_username',
			'label'	=>	$this->__('ShipStation Username:')
		));
		$form->addField('request_password', 'password', array(
			'name'	=>	'request_password',
			'label'	=> $this->__('Password:')
		));
		if (($id = $this->getRequest()->getParam('id'))) {
			$form->setValues(Mage::getModel('auctaneshipstation/user')->load($id)->getData());
		}
		$form->setUseContainer(true);

		$this->setForm($form);
		return parent::_prepareForm();
	}

}
