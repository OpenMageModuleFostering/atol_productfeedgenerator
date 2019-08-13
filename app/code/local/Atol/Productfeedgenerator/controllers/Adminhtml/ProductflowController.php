<?php

/**
 * @category    Atol
 * @package     Atol_Quotationplus
 * @copyright   Copyright (c) 2012 Atol C&D (http://www.atolcd.com)
 */
class Atol_Productfeedgenerator_Adminhtml_ProductflowController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('catalog/productflow')
            ->_addBreadcrumb(Mage::helper('productfeedgenerator')->__('Product flow Manager'), Mage::helper('productfeedgenerator')->__('Product flow Manager'));

        return $this;
    }
    
    public function indexAction() { 
        $this->_initAction()
            ->renderLayout();
    }
    
    public function editAction() {    
        $id = $this->getRequest()->getParam('id', null);
        $model = Mage::getModel('productfeedgenerator/productflow');
        if ($id) {
            $model->load((int) $id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productfeedgenerator')->__('The product flow does not exist'));
                $this->_redirect('*/*/');
            }
        }
        Mage::register('productflow_data', $model);
 
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }
    
    public function newAction() {
        $this->_forward('edit');
    }
    
    public function saveAction() {
        if ($this->getRequest()->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $request = $this->getRequest();
            $id = $request->getPost('id');
            $name = Mage::helper('core')->escapeHtml($request->getPost('name'));
            $comment = Mage::helper('core')->escapeHtml($request->getPost('comment'));
            $data = Mage::helper('core')->jsonDecode($request->getPost('data'));
            
            
            if($name && $data && isset($data['attributes']) && isset($data['categories']) && isset($data['filters']) && isset($data['others'])) {            
                try {          
                    $now = date('Y-M-d H:i:s');          
                    $productFlow = Mage::getModel('productfeedgenerator/productflow');
                    if($id) {
                        $productFlow = $productFlow->load($id);
                    } else {
                        $productFlow->setData('created_at', $now);
                    }                    
                    $productFlow->setData('title', $name);
                    $productFlow->setData('note', $comment);
                    $productFlow->setData('json_data', $request->getPost('data'));
                    $productFlow->setData('updated_at', $now);
                    
                    $productFlow->save();                                   
                    
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('productfeedgenerator')->__('The product flow was successfully updated'));
                    return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array("success" => Mage::helper('productfeedgenerator')->__('The product flow was successfully updated'))));
                } catch(Exception $e) {                    
                    return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array("error" => $e->getMessage())));    
                }                
            } else {
                return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array("error" => Mage::helper('productfeedgenerator')->__('Invalid data'))));
            }
        } else {
            return $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
        }
    }
    
    public function deleteAction() {
        if ($this->getRequest()->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $id = $this->getRequest()->getPost('id');
            if( $id > 0 ) {
                try {
                    $productFlow = Mage::getModel('productfeedgenerator/productflow')->load($id);
                    
                    $now = date('Y-M-d H:i:s');
                    $productFlow->setData('deleted_at', $now);
                    $productFlow->setData('updated_at', $now);
                    
                    $productFlow->save();
                    
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('productfeedgenerator')->__('The product flow was successfully deleted'));
                    return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array("success" => Mage::helper('productfeedgenerator')->__('The product flow was successfully deleted'))));
                } catch(Exception $e) {
                    return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array("error" => $e->getMessage())));
                }
            } else {
                return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array("error" => Mage::helper('productfeedgenerator')->__('Invalid data'))));
            }
        } else {
            return $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
        }
    }
    
    public function testAction() {
        if ($this->getRequest()->isPost() && $this->getRequest()->isXmlHttpRequest()) {
            $request = $this->getRequest();
            $collection = Mage::helper('productfeedgenerator')->getProductCollectionFromJson($request->getPost('data'));
            
            if($collection) {    
                return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('count' => $collection->count())));
            } else {
                return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('error' => Mage::helper('productfeedgenerator')->__('Invalid data'))));
            }
        } else {
            return $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
        }
    }
    
    /**
     * Initialize requested category and put it into registry.
     * Root category can be returned, if inappropriate store/category is specified
     *
     * @param bool $getRootInstead
     * @return Mage_Catalog_Model_Category
     */
    protected function _initCategory($getRootInstead = false)
    {
        $this->_title($this->__('Catalog'))
             ->_title($this->__('Categories'))
             ->_title($this->__('Manage Categories'));

        $categoryId = (int) $this->getRequest()->getParam('id',false);
        $storeId    = (int) $this->getRequest()->getParam('store');
        $category = Mage::getModel('catalog/category');
        $category->setStoreId($storeId);

        if ($categoryId) {
            $category->load($categoryId);
            if ($storeId) {
                $rootId = Mage::app()->getStore($storeId)->getRootCategoryId();
                if (!in_array($rootId, $category->getPathIds())) {
                    // load root category instead wrong one
                    if ($getRootInstead) {
                        $category->load($rootId);
                    }
                    else {
                        $this->_redirect('*/*/', array('_current'=>true, 'id'=>null));
                        return false;
                    }
                }
            }
        }

        if ($activeTabId = (string) $this->getRequest()->getParam('active_tab_id')) {
            Mage::getSingleton('admin/session')->setActiveTabId($activeTabId);
        }

        Mage::register('category', $category);
        Mage::register('current_category', $category);
        Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
        return $category;
    }
    
    /**
     * Get tree node (Ajax version)
     */
    public function categoriesJsonAction()
    {    
        $productFlowId = (int) $this->getRequest()->getPost('productflow',false);
        if($productFlowId) {
            $productFlow = Mage::getModel('productfeedgenerator/productflow')->load($productFlowId);
            Mage::register('productflow_data',$productFlow);
        }
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('productfeedgenerator/adminhtml_productflow_edit_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }
}