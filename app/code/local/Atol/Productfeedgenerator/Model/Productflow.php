<?php

/**
 * @category    Atol
 * @package     Atol_Productfeedgenerator
 * @copyright   Copyright (c) 2013 Atol C&D (http://www.atolcd.com)
 */
class Atol_Productfeedgenerator_Model_Productflow extends Mage_Core_Model_Abstract
{    
    protected $_attributes;
    protected $_filters;
    protected $_categories;
    protected $_limit;
    protected $_order;

    public function _construct()
    {
        parent::_construct();
        $this->_init('productfeedgenerator/productflow');
    }   
    
    public function getAttributes() {
        return $_attributes;
    }
    
    public function getSpecial() {
        return $_filters;
    }
    
    public function getCategories() {
        return $_categories;
    }
    
    public function getOrder() {
        return $_order;
    }
    
    public function getLimit() {
        return $_limit;
    }
}