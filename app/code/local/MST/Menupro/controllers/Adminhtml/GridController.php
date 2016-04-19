<?php
class MST_Menupro_Adminhtml_GridController extends Mage_Adminhtml_Controller_Action
{
	protected function indexAction(){
		
	}
	protected function editAction(){
		$menuid = $this->getRequest()->getParam("id");
		if($menuid != ""){
			$menuinfo = Mage::getModel("menupro/menupro")->load($menuid)->getData();
			
			//Check if menu type is category and use category name as menu title
			if ($menuinfo['type'] == 4 && $menuinfo['use_category_title'] == 2) {
				$categoryTitle = Mage::helper('menupro')->getCategoryTitle($menuinfo['url_value']);
				$menuinfo['title'] = $categoryTitle;
			}
			$this->getResponse()->setBody(json_encode($menuinfo));
		}
	}
}