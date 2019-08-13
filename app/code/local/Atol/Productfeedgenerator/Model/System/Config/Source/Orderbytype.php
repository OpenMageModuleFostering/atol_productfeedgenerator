<?php

/**
 * @category    Atol
 * @package     Atol_Quotationplus
 * @copyright   Copyright (c) 2012 Atol C&D (http://www.atolcd.com)
 */
class Atol_Productfeedgenerator_Model_System_Config_Source_Orderbytype
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                "value" => 0,
                "label" => Mage::helper('productfeedgenerator')->__('No order')
            ),
            array(
                "value" => "product_final_price", //final_price attribute is renamed in the query
                "label" => Mage::helper('productfeedgenerator')->__('Price')
            ),
            array(
                "value" => "name",
                "label" => Mage::helper('productfeedgenerator')->__('Name')
            ),
            array(
                "value" => "news_from_date",
                "label" => Mage::helper('productfeedgenerator')->__('News from date')
            ),
            array(
                "value" => "news_to_date",
                "label" => Mage::helper('productfeedgenerator')->__('News to date')
            )
        );
    }

}