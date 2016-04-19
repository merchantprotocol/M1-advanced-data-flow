<?php
class MST_Menupro_Block_System_Config_Form_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /*
     * Set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('menupro/system/config/button.phtml');
    }
 
    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }
 
    /**
     * Return ajax url for button
     *
     * @return string
     */
    public function getAjaxCheckUrl()
    {
        //return Mage::helper('adminhtml')->getUrl('menupro/adminhtml_menupro/check');
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . "menupro/index/refresh";
    }
 
    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
            'id'        => 'refresh_button',
            'label'     => $this->helper('adminhtml')->__('Refresh'),
            'onclick'   => 'javascript:check(); return false;'
        ));
 
        return $button->toHtml();
    }
}