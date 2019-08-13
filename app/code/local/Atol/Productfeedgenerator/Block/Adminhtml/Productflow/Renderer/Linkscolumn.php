<?php

/**
 * @category    Atol
 * @package     Atol_Productfeedgenerator
 * @copyright   Copyright (c) 2013 Atol C&D (http://www.atolcd.com)
 */
class Atol_Productfeedgenerator_Block_Adminhtml_Productflow_Renderer_Linkscolumn extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    
    public function render(Varien_Object $row)
    {
        $id = $row->getData($this->getColumn()->getIndex());
        return '<a href="' . Mage::getUrl('productfeedgenerator_front/productflow/rss', array('id' => $id, '_store' => 1)) . '" target="_blank" class="rss_link" title="'.Mage::helper('productfeedgenerator')->__('RSS Feed').'"></a>';
    }
    
}