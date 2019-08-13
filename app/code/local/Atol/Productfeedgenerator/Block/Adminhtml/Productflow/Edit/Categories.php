<?php
/**
 * @category    Atol
 * @package     Atol_Productfeedgenerator
 * @copyright   Copyright (c) 2013 Atol C&D (http://www.atolcd.com)
 */
class Atol_Productfeedgenerator_Block_Adminhtml_Productflow_Edit_Categories extends Mage_Adminhtml_Block_Catalog_Category_Tree
{
    private $json_data;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('productflow/edit/categories.phtml');
    }

    public function getCategoryCollection()
    {
        $storeId = $this->getRequest()->getParam('store', $this->_getDefaultStoreId());
        $collection = $this->getData('category_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('catalog/category')->getCollection();

            /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
            $collection->addAttributeToSelect('name')
                ->addAttributeToSelect('is_active')
                ->setProductStoreId($storeId)
                ->setLoadProductCount($this->_withProductCount)
                ->setStoreId($storeId);

            $this->setData('category_collection', $collection);
        }
        $collection->addFieldToFilter('include_in_menu', array('eq' => true));
        return $collection;
    }
    
    protected function _getNodeJson($node, $level=1)
    {
        $item = parent::_getNodeJson($node, $level);

        $isParent = $this->_isParentSelectedCategory($node);

        if ($isParent) {
            $item['expanded'] = true;
        }

        if (in_array($node->getId(), $this->getCategoryIds())) {
            $item['checked'] = true;
        }
        
        return $item;
    }
    
    public function getCategoryChildrenJson($categoryId)
    {
        $category = Mage::getModel('catalog/category')->load($categoryId);
        $node = $this->getRoot($category, 1)->getTree()->getNodeById($categoryId);

        if (!$node || !$node->hasChildren()) {
            return '[]';
        }

        $children = array();
        foreach ($node->getChildren() as $child) {
            $children[] = $this->_getNodeJson($child);
        }

        return Mage::helper('core')->jsonEncode($children);
    }
    
    protected function getCategoryIds()
    {
        $data = $this->getJsonData();        
        return (isset($data) && isset($data['categories'])) ? $data['categories'] : Array();
    }

    public function getIdsString()
    {
        return implode(',', $this->getCategoryIds());
    }
    
    public function getProductflowId() {
        $model = Mage::registry('productflow_data');
        $id = null;
        if($model) {
            $id = $model->getData('flow_id');
        } 
        return $id;
    }
    
    protected function getJsonData() {
        if(!$this->json_data) {
            $model = Mage::registry('productflow_data');
            $this->json_data = (($model) ? Mage::helper('core')->jsonDecode($model->getData('json_data')) : null);
        }
        return $this->json_data;
    }
}
