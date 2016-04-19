<?php
class MST_Menupro_Block_Adminhtml_Groupmenu_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('groupmenu_form', array('legend' => Mage::helper('menupro')->__('Group Information')));
        
        $groupMenuTypes = Mage::getSingleton('menupro/groupmenu')->getMenuTypes();
        $animations = Mage::getSingleton('menupro/groupmenu')->getAnimations();
        $positions = Mage::getSingleton('menupro/groupmenu')->getPositions();
        $responsives = Mage::getSingleton('menupro/groupmenu')->getResponsives();
        $colors = Mage::getSingleton('menupro/groupmenu')->getColors();
        
        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('menupro')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));
        /* $fieldset->addField('menu_type', 'select', array(
        		'label' => Mage::helper('menupro')->__('Menu Type'),
        		'name' => 'menu_type',
        		'class' => 'required-entry',
        		'required' => true,
        		'values' => $groupMenuTypes
        )); */
        $fieldset->addField('animation', 'select', array(
        		'label' => Mage::helper('menupro')->__('Animations'),
        		'name' => 'animation',
        		'class' => 'required-entry',
        		'required' => true,
        		'values' => $animations
        ));
        $fieldset->addField('position', 'select', array(
        		'label' => Mage::helper('menupro')->__('Positions'),
        		'name' => 'position',
        		'class' => 'required-entry',
        		'required' => true,
        		'values' => $positions
        ));
        $fieldset->addField('responsive', 'select', array(
        		'label' => Mage::helper('menupro')->__('Responsive'),
        		'name' => 'responsive',
        		'class' => 'required-entry',
        		'required' => true,
        		'values' => $responsives
        ));
        $fieldset->addField('color', 'select', array(
        		'label' => Mage::helper('menupro')->__('Color schemes'),
        		'name' => 'color',
        		'class' => 'required-entry',
        		'required' => true,
        		'values' => $colors
        ));
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('menupro')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('menupro')->__('Enabled'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('menupro')->__('Disabled'),
                ),
            ),
        ));
	    $fieldset->addField('description', 'textarea', array(
	        'name'      => 'description',
			'readonly'	=> true,
			'after_element_html'	=> '<small class="help-install" style="color: red; font-size: 20px;"><div class="config-heading">Press "Save And Continue Edit" to saved and get the embed code(XML or Widget block)</div></small>'.'<small class="help-note" style="display:none;"><div class="config-heading">Copy the embed code to replace the default menu or any position where you want to display this menu group ( 3 options).</div></small>',
	        'label'     => Mage::helper('menupro')->__('How to embed?'),
	        'title'     => Mage::helper('menupro')->__('How to embed?'),
	        'style'     => 'width:730px; height:150px;',
	        'wysiwyg'   => false,
	        'required'  => false,
	    ));
        if (Mage::getSingleton('adminhtml/session')->getMenuData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getMenuData());
            Mage::getSingleton('adminhtml/session')->setMenuData(null);
        } elseif (Mage::registry('groupmenu_data')) {
            $form->setValues(Mage::registry('groupmenu_data')->getData());
        }
        return parent::_prepareForm();
    }
}