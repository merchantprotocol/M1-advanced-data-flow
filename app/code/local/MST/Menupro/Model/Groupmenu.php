<?php
class MST_Menupro_Model_Groupmenu extends Mage_Core_Model_Abstract {
	public function _construct() {
		parent::_construct ();
		$this->_init ( 'menupro/groupmenu' );
	}
	public function getGroupArray() {
		$groupData = array ();
		$group_collection = Mage::getModel ( 'menupro/groupmenu' )->getCollection ();
		$group_collection->addFieldToFilter ( 'status', 1 );
		foreach ( $group_collection as $group ) {
			// $groupNames [$gname->getGroupId ()] = $gname->getTitle ();
			$groupData [] = array (
					'value' => $group->getGroupId (),
					'label' => $group->getTitle (),
					'menu_type' => $group->getMenuType () 
			);
		}
		return $groupData;
	}
	public function getAllGroupArray() {
		$groupData = array ();
		$group_collection = Mage::getModel ( 'menupro/groupmenu' )->getCollection ();
		// $group_collection->addFieldToFilter ( 'status', 1 );
		foreach ( $group_collection as $group ) {
			// $groupNames [$gname->getGroupId ()] = $gname->getTitle ();
			$groupData [] = array (
					'value' => $group->getGroupId (),
					'label' => $group->getTitle (),
					'menu_type' => $group->getMenuType () 
			);
		}
		return $groupData;
	}
	public function getOptionArray() {
		$arr_status = array (
				array (
						'value' => 1,
						'label' => Mage::helper ( 'menupro' )->__ ( 'Enabled' ) 
				),
				array (
						'value' => 2,
						'label' => Mage::helper ( 'menupro' )->__ ( 'Disabled' ) 
				) 
		);
		
		return $arr_status;
	}
	public function getMenuTypes() {
		return array (
				array (
						'value' => '',
						'label' => '--Please Select--' 
				),
				array (
						'value' => 'dropdown',
						'label' => 'Dropdown' 
				),
				array (
						'value' => 'sidebar',
						'label' => 'Sidebar' 
				),
				array (
						'value' => 'accordion',
						'label' => 'Accordion' 
				) 
		);
	}
	public function getAnimations() {
		return array (
				array (
						'value' => 'menu-creator-pro-fade',
						'label' => 'Fade (default)'
				),
				array (
						'value' => 'menu-creator-pro-scale',
						'label' => 'Scale' 
				),
				array (
						'value' => 'menu-creator-pro-flip',
						'label' => 'Flip' 
				),
				array (
						'value' => 'menu-creator-pro-slide',
						'label' => 'Slide' 
				),
				 
		);
	}
	public function getColors() {
		return array (
				array (
						'value' => 'cyan',
						'label' => 'Cyan (default)'
				),
				array (
						'value' => 'menu-creator-pro-red',
						'label' => 'Red'
				),
				array (
						'value' => 'menu-creator-pro-orange',
						'label' => 'Orange'
				),
				array (
						'value' => 'menu-creator-pro-green',
						'label' => 'Green'
				),
				array (
						'value' => 'menu-creator-pro-purple',
						'label' => 'Purple'
				),
				array (
						'value' => 'menu-creator-pro-pink',
						'label' => 'Pink'
				),array (
						'value' => 'menu-creator-pro-yellow',
						'label' => 'Yellow'
				),array (
						'value' => 'menu-creator-pro-black',
						'label' => 'Black'
				)
		);
	}
	public function getResponsives() {
		return array (
				array (
						'value' => 'menu-creator-pro-rp-switcher',
						'label' => 'Responses into switcher (default)'
				),
				array (
						'value' => 'menu-creator-pro-rp-stack',
						'label' => 'Responses into stack'
				),
				array (
						'value' => 'menu-creator-pro-rp-icons',
						'label' => 'Responses into icons'
				),
				array (
						'value' => 'menu-creator-pro-rp-switcher side-panel',
						'label' => 'Responsive side panel'
				),
				array (
						'value' => 'disable',
						'label' => 'No responsiveness' 
				)
				
		);
	}
	public function getPositions() {
		return array (
				array (
						'value' => 'menu-creator-pro',
						'label' => 'Top (default)'
				),
				array (
						'value' => 'menu-creator-pro menu-creator-pro-left',
						'label' => 'Left'
				),
				array (
						'value' => 'menu-creator-pro menu-creator-pro-right',
						'label' => 'Right'
				),
				array (
						'value' => 'menu-creator-pro menu-creator-pro-bottom',
						'label' => 'Bottom'
				),
				array (
						'value' => 'menu-creator-pro menu-creator-pro-top-fixed',
						'label' => 'Top Fixed'
				),
				array (
						'value' => 'menu-creator-pro menu-creator-pro-left-fixed',
						'label' => 'Left Fixed'
				),
				array (
						'value' => 'menu-creator-pro menu-creator-pro-right-fixed',
						'label' => 'Right Fixed'
				),
				array (
						'value' => 'menu-creator-pro menu-creator-pro-bottom-fixed',
						'label' => 'Bottom Fixed'
				),
				array (
						'value' => 'menu-creator-pro menu-creator-pro-accordion',
						'label' => 'Accordion'
				)
		);
	}
	public function installGuide($position, $groupId) {
		$html = '1, Insert into Static block or CMS page for developing: <br/><br/>{{block type="menupro/menu" name="menupro_group_' . $groupId . '" group_id="' . $groupId . '" template="menupro/menupro.phtml" }}<br/><br/>';
		$html .= '2, Reference via XML layout file( to replace the default menu or other purpose):<br/><br/>';
		$html .= '<block type="menupro/menu" name="menupro_group_' . $groupId . '" ifconfig="menupro/setting/enable" template="menupro/menupro.phtml"><br/>';
		$html .= '    <action method="setData"><name>group_id</name><value>' . $groupId . '</value></action><br/>';
		$html .= '</block>';
		$html .= '<br/><br/>3, Call via frontend template file: <br/><br/><?php echo $this->getLayout()->createBlock("menupro/menu")->setGroup_id('.$groupId.')->setTemplate("menupro/menupro.phtml")->toHtml(); ?>';
		return $html;
	}
}