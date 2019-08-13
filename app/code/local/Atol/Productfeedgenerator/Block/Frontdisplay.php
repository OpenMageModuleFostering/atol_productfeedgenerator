<?php
class Atol_Productfeedgenerator_Block_Frontdisplay
    extends Mage_Catalog_Block_Product_List
    implements Mage_Widget_Block_Interface
{

    protected function _toHtml()
    {
        $columns = self::getData('columns');

        $template = 'catalog/product/list.phtml';
        /*
        To avoid displaying the list marker on product list widget, add the following code to CMS_page/Design/Page Layout/Layout Update XML
        Pour ne pas avoir le bug des puces apparantes dans la liste de produits, ajouter le code suivant dans Page/Design/Page Layout/Layout Update XML
        <reference name="content">
            <remove name="cms.wrapper" />
            <block type="cms/page" name="cms_page"/>
        </reference>
         */

        $this->setColumnCount($columns);
        $this->setTemplate($template);

        return parent::_toHtml();
    }

    protected function _getProductCollection()
    {
        $productFlowId = self::getData('flux_id');
        $productFlow = Mage::getModel('productfeedgenerator/productflow')->load($productFlowId);
        $json = $productFlow->getData('json_data');


        $this->_productCollection =  Mage::helper('productfeedgenerator')->getProductCollectionFromJson($json);

        return $this->_productCollection;
    }

}