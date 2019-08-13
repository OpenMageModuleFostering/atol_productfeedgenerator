<?php

/**
 * @category    Atol
 * @package     Atol_Quotationplus
 * @copyright   Copyright (c) 2012 Atol C&D (http://www.atolcd.com)
 */
class Atol_Productfeedgenerator_Block_Adminhtml_Productflow_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
    
    private $operators; 
    
    function __construct() {
        parent::__construct();
        $this->operators = array(
            "multiselect" => array('one' => $this->__('at least one of them operator'), 'all' => $this->__('all of them operator')),
            "select" => array('eq' => $this->__('equal operator'), 'neq' => $this->__('not equal operator'), 'or' => $this->__('or operator')), 
            "boolean" => array('eq' => $this->__('equal operator'), 'neq' => $this->__('not equal operator')),
            "text" => array('eq' => $this->__('equal operator'), 'neq' => $this->__('not equal operator'), 'in' => $this->__('in operator'))

        );
    }
    
    public function getHeaderText() {
        if( Mage::registry('productflow_data') && Mage::registry('productflow_data')->getId()) {
            return Mage::helper('productfeedgenerator')->__('Edit the product flow');
        }
        else {
            return Mage::helper('productfeedgenerator')->__('Add a product flow');
        }
    }
    
    public function getProductAttributes() {
		$attributes = Mage::getResourceModel('catalog/product_attribute_collection')
			->setItemObjectClass('catalog/resource_eav_attribute');
		$attributes->getSelect()
			->distinct(true)
			->where("frontend_input in ('select','multiselect','boolean')");
		$attributes->addStoreLabel(Mage::app()->getStore()->getId())
			->setOrder('position', 'ASC')
			->load();
		
		foreach ($attributes as $a)
		{
			$a->setIsRequired(false);
		}
		
		return $attributes;
    }
    
    public function getOperators($inputType) {
        return (isset($this->operators[$inputType])) ? $this->operators[$inputType] : Array();
    }
    
    public function getCurrentProductflow() {
        return Mage::registry('productflow_data');
    }
        
}