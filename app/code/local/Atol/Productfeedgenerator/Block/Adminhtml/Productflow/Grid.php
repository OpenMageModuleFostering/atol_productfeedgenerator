<?php

/**
 * @category    Atol
 * @package     Atol_Productfeedgenerator
 * @copyright   Copyright (c) 2013 Atol C&D (http://www.atolcd.com)
 */
class Atol_Productfeedgenerator_Block_Adminhtml_Productflow_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
       parent::__construct();
       $this->setId('productfeedgeneratorGrid');
       $this->setDefaultSort('type_id');
       $this->setDefaultDir('DESC');
       $this->setSaveParametersInSession(true);
    }
    
    protected function _prepareCollection()
    {    
        $collection = Mage::getModel('productfeedgenerator/productflow')->getCollection()
            ->addFieldToFilter('deleted_at', array('null' => true))
            ->addOrder('flow_id',Varien_Data_Collection::SORT_ORDER_ASC);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {    
        $this->addColumn('flow_id',
            array(
                'header' => Mage::helper('productfeedgenerator')->__('ID'),
                'align' =>  'right',
                'width' =>  '50px',
                'index' =>  'flow_id',
            ));
        $this->addColumn('title', 
            array(
                'header' => Mage::helper('productfeedgenerator')->__('Title'),
                'align' =>  'left',
                'index' =>  'title',
            ));
        $this->addColumn('note', 
            array(
                'header' => Mage::helper('productfeedgenerator')->__('Comment'),
                'align' =>  'left',
                'index' =>  'note',
            ));
        $this->addColumn('links', 
            array(
                'header' => Mage::helper('productfeedgenerator')->__('Links'),
                'align' =>  'left',
                'index' =>  'flow_id',
                'renderer' => 'Atol_Productfeedgenerator_Block_Adminhtml_Productflow_Renderer_Linkscolumn'
            ));
        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}