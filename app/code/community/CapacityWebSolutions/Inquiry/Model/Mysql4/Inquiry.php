<?php

/***************************************************************************
	@extension	: Dealer Inquiry Extension.
	@copyright	: Copyright (c) 2014 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com	
***************************************************************************/
 
class CapacityWebSolutions_Inquiry_Model_Mysql4_Inquiry extends Mage_Core_Model_Mysql4_Abstract
{
  	public function _construct()
	{
  		$this->_init('inquiry/inquiry', 'dealerid');
	}
}

