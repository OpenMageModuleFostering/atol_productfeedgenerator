<?php

/**
 * @category    Atol
 * @package     Atol_Productfeedgenerator
 * @copyright   Copyright (c) 2013 Atol C&D (http://www.atolcd.com)
 */
class Atol_Productfeedgenerator_Model_Mysql4_Productflow_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('productfeedgenerator/productflow');
    }
}