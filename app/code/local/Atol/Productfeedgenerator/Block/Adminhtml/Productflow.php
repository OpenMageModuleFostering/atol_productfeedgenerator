<?php

/**
 * @category    Atol
 * @package     Atol_Productfeedgenerator
 * @copyright   Copyright (c) 2013 Atol C&D (http://www.atolcd.com)
 */
class Atol_Productfeedgenerator_Block_Adminhtml_Productflow extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_productflow';
        $this->_blockGroup = 'productfeedgenerator';
        $this->_headerText = Mage::helper('productfeedgenerator')->__('Product flow management');
        $this->_addButtonLabel = Mage::helper('productfeedgenerator')->__('Add a Product flow');
    }
}