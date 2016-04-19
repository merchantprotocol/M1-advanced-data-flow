<?php
/**
 * WDCA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   WDCA
 * @package    TBT_Enhancedgrid
 * @copyright  Copyright (c) 2008-2010 WDCA (http://www.wdca.ca)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Grid checkbox column renderer
 *
 * @category   WDCA
 * @package    TBT_Enhancedgrid
 * @author      WDCA <contact@wdca.ca>
 */
class Perception_Bannerpro_Block_Widget_Grid_Column_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    protected static $showImagesUrl = null;
    protected static $showByDefault = null;
    protected static $width = null;
    protected static $height = null;
    
    public function __construct() {
       
    }

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        
        return $this->_getValue($row);
    }
    
    /*
    public function renderProperty(Varien_Object $row)
    {
        $val = $row->getData($this->getColumn()->getIndex());
        $val = Mage::helper('imagebyurl')->getImageUrl($val);
        $out = parent::renderProperty(). ' onclick="showImage('.$val.')" ';
        return $out;
    }

        */
    protected function _getValue(Varien_Object $row)
    {
        
        if ($getter = $this->getColumn()->getGetter()) {
            $val = $row->$getter();
        }
        $val = $val2 = $row->getData($this->getColumn()->getIndex());
        $val = str_replace("no_selection", "", $val);
        $val2 = str_replace("no_selection", "", $val2);
        
		$url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$val;
        
        $filename = $val;
        
        $out = "<img src=". $url ." width='". 100 ."' height='". 100 ."'  />";
      
        //die( $this->helper('catalog/image')->init($_product, 'small_image')->resize(135, 135));
       
        
        return $out;
    }


}
