<?php

/**
 * @category    Atol
 * @package     Atol_Productfeedgenerator
 * @copyright   Copyright (c) 2013 Atol C&D (http://www.atolcd.com)
 */
class Atol_Productfeedgenerator_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Return all products concerned by all parameters
     *
     * @var $attributes array
     *      $attributes is an array containing the list of attributes to filter
     *      Each attribute is linked to an array containing the operator with the value
     *      If an attribute is not referenced in the $AUTHORIZED_ATTRIBUTES list, it will be ignored
     *      For example : $attributes = array("price" => array("eq" => 2.0))
     * @var $filters array
     *      $filters indicates if discounted or new products should be considered by the query
     *      For example : $filters = array('new','discounted')
     * @var $categoriesId array
     *      $categoriesId contains all categories id whose products belongs to
     *      For example : $categoriesId = [1, 5, 10, 70]
     * @var $orders array
     *      $orders is an array indicating the orders of the data flow
     *      For example, $orders = array(array('attribute' => 'price', 'direction' => 'desc'), array('attribute' => 'name', 'direction' => 'asc'))
     * @var $limit
     *      $limit indicates the number of products returned by the query
     */
    public function getProductCollection(Array $attributes, Array $filters = null, $categoriesId = null, Array $orders = null, $limit = null) {
        $product = Mage::getModel('catalog/product');

        $products = $product->getCollection()
            ->setStoreId(Mage::app()->getStore()->getId())
            ->addStoreFilter()
            ->addAttributeToFilter('visibility', 4)
            ->addAttributeToSelect(array('name', 'short_description', 'description', 'thumbnail'), 'inner')
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addAttributeToSelect(
                array(
                    'price', 'special_price', 'special_from_date', 'special_to_date', 'news_from_date', 'news_to_date',
                    'msrp_enabled', 'msrp_display_actual_price_type', 'msrp'
                ),
                'left'
            )
        ;  
        $products->getSelect()
            ->join('catalog_product_index_price','e.entity_id = catalog_product_index_price.entity_id', array('product_price' => 'price', 'product_final_price' => 'final_price'))
            ->where('catalog_product_index_price.customer_group_id = 0')
            ->group('e.entity_id');
        
        
        if($filters) {
            $todayDate = $product->getResource()->formatDate(time());
            foreach($filters as $filter){
                switch($filter) {
                    case 'discounted' : 
                        $products->addPriceDataFieldFilter('%s < %s', array('special_price','price'));
                        $products->addAttributeToFilter('special_from_date', array('date'=>true, 'to'=> $todayDate))
                            ->addAttributeToFilter(
                                array(
                                     array('attribute'=>'special_to_date', 'date'=>true, 'from'=>$todayDate),
                                     array('attribute'=>'special_to_date', 'is' => new Zend_Db_Expr('null'))
                                ),
                                '',
                                'left'
                            );
                        break;
                    case 'new' : 
                        $products->addAttributeToFilter('news_from_date', array('date'=>true, 'to'=> $todayDate))
                            ->addAttributeToFilter(
                                array(
                                     array('attribute'=>'news_to_date', 'date'=>true, 'from'=>$todayDate),
                                     array('attribute'=>'news_to_date', 'is' => new Zend_Db_Expr('null'))
                                ),
                                '',
                                'left'
                            )
                            ->addAttributeToSort('news_from_date','desc');
                        break;
                    default : 
                        break;
                }
            }
        }       

        if($categoriesId) {
            $cats = implode(',', $categoriesId);
            $products->joinTable('catalog/category_product', 'product_id=entity_id', array('product_id' => 'product_id') , '{{table}}.category_id IN ( '. $cats .' )' );
        }
        
        foreach($attributes as $attribute) {
            $attributeCode = $attribute['attribute_code'];
            $expr = $attribute['expr'];
            $products->addAttributeToFilter($attributeCode, $expr);            
        }
        
        if($limit) {
            $products->getSelect()->limit($limit, 0);
        }
        
        if($orders) {
            foreach($orders as $orderby) {
                if(isset($orderby['attribute']) && $orderby['attribute'] && isset($orderby['direction'])) {
                    $products->getSelect()->order($orderby['attribute'], $orderby['direction']);
                }
            }
        } 
        
        return $products;
    }
    
    public function getProductCollectionFromJson($json) {
        try {
            $data = Mage::helper('core')->jsonDecode($json);
        } catch(Exception $e) {
            Mage::logException($e);
            return null;
        }
            
        if($data && isset($data['attributes']) && isset($data['categories']) && isset($data['filters']) && isset($data['others'])) {
            $attributes = $data['attributes'];
            $attributesQuery = Array();
            foreach($attributes as $attribute) {
                $attributeModel = Mage::getModel('eav/entity_attribute')->load($attribute['id']);
                $attributeCode = $attributeModel->getData('attribute_code'); 
                if($attributeCode && isset($attribute['operator']) && isset($attribute['values']) && count($attribute['values']) > 0) {
                    $expr = null;
                    switch($attribute['operator']) {
                        case 'or' : 
                            $attributesQuery[] = array("attribute_code" => $attributeCode, "expr" => array('in' => $attribute['values'])); 
                            break;
                        case 'one' :
                            foreach($attribute['values'] as $value) {
                                $expr[] = array('finset'  => $value);
                            }
                            $attributesQuery[] = array("attribute_code" => $attributeCode, "expr" => $expr);  
                            break;
                        case 'all' : 
                            foreach($attribute['values'] as $value) {
                                $attributesQuery[] = array("attribute_code" => $attributeCode, "expr" => array('finset'  => $value));
                            }
                            break;
                        case 'eq' : 
                        case 'neq' : 
                        default : 
                            $attributesQuery[] = array("attribute_code" => $attributeCode, "expr" => array($attribute['operator'] => $attribute['values'][0])); 
                    }                                      
                }                
            }
            
            $filters = $data['filters'];
            $filtersQuery = Array();
            foreach($filters as $key => $value) {
                if($value) {
                    $filtersQuery[] = $key;
                }
            }
            
            $categoriesId = $data['categories'];
            $categoriesQuery = Array();
            foreach($categoriesId as $categoryId) {
                if($categoryId && !in_array($categoryId, $categoriesQuery)) {
                    $categoriesQuery[] = $categoryId;
                }
            }
            
            $orderQueryTmp = $data['orderBy'];
            $orderQuery = Array();
            foreach($orderQueryTmp as $orderBy) {                
                if(isset($orderBy['attribute']) && isset($orderBy['direction']) &&  ($orderBy['direction'] == 'asc' || $orderBy['direction'] == 'desc')) {
                    $orderQuery[] = $orderBy;
                }
            }
            
            $limitQuery = null;
            if(isset($data['others']['limit'])) {
                $limitQuery = $data['others']['limit'];
            }    
            
            return $this->getProductCollection($attributesQuery, $filtersQuery, $categoriesQuery, $orderQuery, $limitQuery);
        } else {
            return null;
        }
    }

    public function getProductCollectionFromId()
    {
        $id = Mage::app()->getRequest()->getParam('id');
        $productFlow = Mage::getModel('productfeedgenerator/productflow')->load($id);
        $json = $productFlow->getData('json_data');
        return $this->getProductCollectionFromJson($json);
    }
}