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


class AW_Sarp2_Helper_Product
{
    public function createProductOption(Mage_Catalog_Model_Product $product, $type, $id, $title)
    {
        $option = Mage::getModel('catalog/product_option');
        $option->setData(
            array(
                 'option_id'          => $id,
                 'type'               => $type,
                 'is_require'         => 1,
                 'sku'                => '',
                 'max_characters'     => null,
                 'file_extension'     => null,
                 'image_size_x'       => '0',
                 'image_size_y'       => '0',
                 'sort_order'         => '0',
                 'default_title'      => $title,
                 'store_title'        => $title,
                 'title'              => $title,
                 'default_price'      => null,
                 'default_price_type' => null,
                 'store_price'        => null,
                 'store_price_type'   => null,
                 'price'              => null,
                 'price_type'         => null,
            )
        );
        $option->setProduct($product);
        return $option;
    }

    public function addProductOptionValue(
        Mage_Catalog_Model_Product_Option $productOption,
        $id, $title
    )
    {
        /**
         * @var Mage_Catalog_Model_Product_Option_Value $value
         */
        $value = Mage::getModel('catalog/product_option_value');
        $value->setData(
            array(
                 'option_type_id'     => $id,
                 'option_id'          => $productOption->getOptionId(),
                 'sku'                => null,
                 'sort_order'         => '0',
                 'default_title'      => $title,
                 'store_title'        => $title,
                 'title'              => $title,
                 'default_price'      => null,
                 'default_price_type' => null,
                 'store_price'        => null,
                 'store_price_type'   => null,
                 'price'              => null,
                 'price_type'         => null,
            )
        );
        $value->setProduct($productOption->getProduct());
        $productOption->addValue($value);
        return $value;
    }
}