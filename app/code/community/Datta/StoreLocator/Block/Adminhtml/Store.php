<?php
/**
 * Magento 
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
 * @category    Datta
 * @package     Datta_StoreLocator
 * @created     Dattatray Yadav  2nd Dec,2013 1:45pm
 * @author      Clarion magento team<Dattatray Yadav>   
 * @purpose     parent construct object load
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Datta_StoreLocator_Block_Adminhtml_Store extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'datta_storelocator';
        $this->_controller = 'adminhtml_store';
        $this->_headerText = $this->__('Schools');      
        parent::__construct();
    }
}