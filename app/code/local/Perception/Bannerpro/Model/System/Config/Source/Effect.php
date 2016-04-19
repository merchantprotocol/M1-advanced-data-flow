<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
/**
 * Used in creating options for Yes|No config value selection
 *
 */
class Perception_Bannerpro_Model_System_Config_Source_Effect
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
			array('value' => 'none', 'label'=>Mage::helper('bannerpro')->__('None')),
            array('value' => 'blindX', 'label'=>Mage::helper('bannerpro')->__('Blind X')),
            array('value' => 'blindY', 'label'=>Mage::helper('bannerpro')->__('Blind Y')),
            array('value' => 'blindZ', 'label'=>Mage::helper('bannerpro')->__('Blind Z')),
            array('value' => 'cover', 'label'=>Mage::helper('bannerpro')->__('Cover')),
            array('value' => 'curtainX', 'label'=>Mage::helper('bannerpro')->__('Curtain X')),
            array('value' => 'curtainY', 'label'=>Mage::helper('bannerpro')->__('Curtain Y')),
			array('value' => 'fade', 'label'=>Mage::helper('bannerpro')->__('Fade')),
			array('value' => 'fadeZoom', 'label'=>Mage::helper('bannerpro')->__('Fade Zoom')),			
			array('value' => 'growX', 'label'=>Mage::helper('bannerpro')->__('Grow X')),
			array('value' => 'growY', 'label'=>Mage::helper('bannerpro')->__('Grow Y')),			
			array('value' => 'scrollUp', 'label'=>Mage::helper('bannerpro')->__('Scroll Up')),
			array('value' => 'scrollDown', 'label'=>Mage::helper('bannerpro')->__('Scroll Down')),
			array('value' => 'scrollLeft', 'label'=>Mage::helper('bannerpro')->__('Scroll Left')),
			array('value' => 'scrollRight', 'label'=>Mage::helper('bannerpro')->__('Scroll Right')),
			array('value' => 'scrollHorz', 'label'=>Mage::helper('bannerpro')->__('Scroll Horizontal')),
			array('value' => 'scrollVert', 'label'=>Mage::helper('bannerpro')->__('Scroll Vertical')),
			array('value' => 'shuffle', 'label'=>Mage::helper('bannerpro')->__('Shuffle')),
			array('value' => 'toss', 'label'=>Mage::helper('bannerpro')->__('Toss')),
			array('value' => 'turnUp', 'label'=>Mage::helper('bannerpro')->__('Turn Up')),
			array('value' => 'turnDown', 'label'=>Mage::helper('bannerpro')->__('Turn Down')),
			array('value' => 'turnLeft', 'label'=>Mage::helper('bannerpro')->__('Turn Left')),
			array('value' => 'turnRight', 'label'=>Mage::helper('bannerpro')->__('Turn Right')),
			array('value' => 'uncover', 'label'=>Mage::helper('bannerpro')->__('Uncover')),
			array('value' => 'wipe', 'label'=>Mage::helper('bannerpro')->__('Wipe')),
			array('value' => 'zoom', 'label'=>Mage::helper('bannerpro')->__('Zoom'))
        );
    } 
}
