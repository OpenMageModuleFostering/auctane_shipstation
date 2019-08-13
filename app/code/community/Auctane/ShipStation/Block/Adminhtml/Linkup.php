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

class Auctane_ShipStation_Block_Adminhtml_Linkup extends Mage_Adminhtml_Block_Widget_Form_Container
{

	protected $_blockGroup = 'auctaneshipstation';
	protected $_controller = 'adminhtml';
	protected $_mode = 'linkup';

	public function __construct()
	{
		parent::__construct();
		$this->updateButton('save', 'label', $this->__('Link up with ShipStation'));
	}

}
