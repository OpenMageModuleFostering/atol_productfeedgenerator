<?php
/**
 * @category    Atol
 * @package     Atol_Productfeedgenerator
 * @copyright   Copyright (c) 2013 Atol C&D (http://www.atolcd.com)
 */

class Atol_Productfeedgenerator_Block_Productflow_Rss extends Mage_Rss_Block_Catalog_New
{
    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_catalog_new_'.$this->_getStoreId());
        $this->setCacheLifetime(600);
    }
    
    protected function _toHtml()
    {        
        $productFlow = Mage::registry('product_flow');
        if($productFlow) {
            $rssObj = Mage::getModel('rss/rss');
            $data = array('title' => Mage::helper('productfeedgenerator')->__('Products list from %s',$productFlow->getTitle()),
                    'description' => $productFlow->getNote(),
                    'link'        => Mage::getUrl('productfeedgenerator/catalog/products'),
                    'charset'     => 'UTF-8',
                    'language'    => Mage::getStoreConfig('general/locale/code')
                    );
            $rssObj->_addHeader($data);
            
            $products = ($productFlow) ? Mage::helper('productfeedgenerator')->getProductCollectionFromJson($productFlow->getData('json_data')) : null;
            
            $product = Mage::getModel('catalog/product');
            
            Mage::getSingleton('core/resource_iterator')->walk(
                    $products->getSelect(),
                    array(array($this, 'addNewItemXmlCallback')),
                    array('rssObj'=> $rssObj, 'product'=> $product)
            );

            return $rssObj->createRssXml();
        } else {
            return '';
        }
    }

    /**
     * Preparing data and adding to rss object
     *
     * @param array $args
     */
    public function addNewItemXmlCallback($args)
    {
        $_coreHelper = Mage::helper('core');
        $_taxHelper  = Mage::helper('tax');
        
        $product = $args['product'];        
        $product->setData($args['row']);
        
        $_price = $_taxHelper->getPrice($product, $product->getPrice());
        $_finalPrice = $_taxHelper->getPrice($product, $product->getData('product_final_price')); 
        
        $product->setAllowedInRss(true);
        $product->setAllowedPriceInRss(false);
        Mage::dispatchEvent('rss_catalog_new_xml_callback', $args);

        if (!$product->getAllowedInRss()) {
            //Skip adding product to RSS
            return;
        }
        $url = Mage::getUrl('catalog/product/view',array("id" => $product->getId()));
        $allowedPriceInRss = $product->getAllowedPriceInRss();
        
        
        $description = 
            '<table>'.
                '<tr>'.
                    '<td>'.
                        '<a href="'.$url.'">'.
                            '<img src="'. $this->helper('catalog/image')->init($product, 'thumbnail')->resize(75, 75).'" border="0" align="left" height="75" width="75">' .
                        '</a>'.
                    '</td>'.
                    '<td  style="text-decoration:none;">' 
                        . $product->getDescription() . '<br /><br />'
                        . $this->__('Price : ') . $_coreHelper->currency($_price,true,false) . '<br />'
                        . $this->__('Final price : ') . $_coreHelper->currency($_finalPrice,true,false) ;
                        

        if ($allowedPriceInRss) {
            $description .= $this->getPriceHtml($product,true);
        }

        $description .= 
                    '</td>'.
                '</tr>'.
            '</table>';

        $rssObj = $args['rssObj'];
        $data = array(
                'title'         => $product->getName(),
                'link'          => $url,
                'description'   => $description,
            );
        $rssObj->_addEntry($data);
    }
}