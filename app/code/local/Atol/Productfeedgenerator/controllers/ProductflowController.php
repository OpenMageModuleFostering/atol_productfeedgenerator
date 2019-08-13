<?php
/**
 * @category    Atol
 * @package     Atol_Productfeedgenerator
 * @copyright   Copyright (c) 2013 Atol C&D (http://www.atolcd.com)
 */

class Atol_Productfeedgenerator_ProductflowController extends Mage_Core_Controller_Front_Action {

    protected function isFeedEnable()
    {
        return Mage::getStoreConfig('rss/productfeedgenerator/active');
    }

    protected function checkFeedEnable()
    {
        if ($this->isFeedEnable()) {
            $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
            return true;
        } else {
            return $this->getResponse()->setHeader('HTTP/1.1','403 Forbidden');
        }
    }

    public function rssAction() {
        $productFlowId = (int) $this->getRequest()->getParam('id',false);
        if($productFlowId) {
            $productFlow = Mage::getModel('productfeedgenerator/productflow')->load($productFlowId);

            if($productFlow && $productFlow->getData('flow_id') && !$productFlow->getData('deleted_at')) {
                Mage::register('product_flow', $productFlow);
                $this->checkFeedEnable();
                //$this->checkSecurity();
                $this->loadLayout(false);
                $this->renderLayout();
            } else {
                return $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
            }
        } else {
            return $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
        }
    }
}
