<?php

/**
 * @category    Atol
 * @package     Atol_Quotationplus
 * @copyright   Copyright (c) 2012 Atol C&D (http://www.atolcd.com)
 */
class Atol_Productfeedgenerator_Model_System_Config_Source_Productflowtype
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $collection = Mage::getModel('productfeedgenerator/productflow')->getCollection()
            ->addFieldToFilter('deleted_at', array('null' => true))
            ->addOrder('flow_id',Varien_Data_Collection::SORT_ORDER_ASC);
        
        $options = array_map(
            function($pf) {
                return array(
                    "value" => $pf->getData('flow_id'), 
                    "label" => $pf->getTitle()
                );
            }, 
            iterator_to_array($collection->getIterator())
        );
            
        return $options;
    }

}