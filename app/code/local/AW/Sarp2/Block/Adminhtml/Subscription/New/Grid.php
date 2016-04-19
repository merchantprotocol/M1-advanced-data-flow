<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Sarp2
 * @version    2.0.3
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Sarp2_Block_Adminhtml_Subscription_New_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Retrieve forbidden product type list
     *
     * @return array
     */
    public function getForbiddenProductTypeList()
    {
        $forbiddenTypeList = array();
        $forbiddenTypeList[] = Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE;
        $forbiddenTypeList[] = Mage_Catalog_Model_Product_Type::TYPE_BUNDLE;
        if (Mage::helper('aw_sarp2')->isModuleOutputEnabled('AW_Sarp')) {
            $forbiddenTypeList[] = AW_Sarp_Model_Product_Type_Simple_Subscription::TYPE_CODE;
            $forbiddenTypeList[] = AW_Sarp_Model_Product_Type_Configurable_Subscription::PRODUCT_TYPE_CONFIGURABLE;
            $forbiddenTypeList[] = AW_Sarp_Model_Product_Type_Downloadable_Subscription::PRODUCT_TYPE_DOWLOADABLE;
            $forbiddenTypeList[] = AW_Sarp_Model_Product_Type_Grouped_Subscription::TYPE_CODE;
            $forbiddenTypeList[] = AW_Sarp_Model_Product_Type_Virtual_Subscription::TYPE_CODE;
        }
        return $forbiddenTypeList;
    }

    public function setCollection($collection)
    {
        $productIds = Mage::getResourceModel('aw_sarp2/subscription_collection')->getProductIds();
        if (count($productIds)) {
            $collection->addAttributeToFilter('entity_id', array('nin' => $productIds));
        }
        $collection->addAttributeToFilter('type_id', array('nin' => $this->getForbiddenProductTypeList()));
        return parent::setCollection($collection);
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                 'header' => $this->__('ID'),
                 'width'  => '50px',
                 'index'  => 'entity_id',
            )
        );

        $this->addColumn(
            'name',
            array(
                 'header' => $this->__('Name'),
                 'index'  => 'name',
            )
        );

        if ((int)$this->getRequest()->getParam('store', 0)) {
            $this->addColumn(
                'custom_name',
                array(
                     'header' => $this->__('Name in Store'),
                     'index'  => 'custom_name'
                )
            );
        }

        $this->addColumn(
            'sku',
            array(
                 'header' => $this->__('SKU'),
                 'width'  => '80px',
                 'index'  => 'sku'
            )
        );

        $this->addColumn(
            'price',
            array(
                 'header' => $this->__('Price'),
                 'type'   => 'currency',
                 'index'  => 'price'
            )
        );

        $this->addColumn(
            'qty',
            array(
                 'header' => $this->__('Qty'),
                 'width'  => '130px',
                 'type'   => 'number',
                 'index'  => 'qty'
            )
        );

        $this->addColumn(
            'status',
            array(
                 'header'  => $this->__('Status'),
                 'width'   => '90px',
                 'index'   => 'status',
                 'type'    => 'options',
                 'source'  => 'catalog/product_status',
                 'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
            )
        );

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn(
                'websites',
                array(
                     'header'   => $this->__('Websites'),
                     'width'    => '100px',
                     'sortable' => false,
                     'index'    => 'websites',
                     'type'     => 'options',
                     'options'  => Mage::getModel('core/website')->getCollection()->toOptionHash(),
                )
            );
        }
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('product_id' => $row->getId()));
    }

    public function getMainButtonsHtml()
    {
        return $this->getBackButtonHtml() . parent::getMainButtonsHtml();
    }

    protected function _prepareMassaction()
    {
        return $this;
    }

    protected function _prepareLayout()
    {
        $backButton = $this->getLayout()->createBlock('adminhtml/widget_button');
        $backButton->setData(
            array(
                 'label'   => Mage::helper('adminhtml')->__('Back'),
                 'onclick' => 'setLocation(\'' . $this->getBackUrl() . '\')',
                 'class'   => 'back',
            )
        );
        $this->setChild('back_button', $backButton);
        return parent::_prepareLayout();
    }


    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/index');
    }
}