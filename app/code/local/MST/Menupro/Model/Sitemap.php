<?php
/**
* Magento Support Team.
* @category   MST
* @package    MST_Menupro
* @version    2.0
* @author     Magebay Developer Team <info@magebay.com>
* @copyright  Copyright (c) 2009-2013 MAGEBAY.COM. (http://www.magebay.com)
*/
class MST_Menupro_Model_Sitemap extends Mage_Core_Model_Abstract
{
	protected $_grid_nav = "";
	protected $_groupstatus = 0;
    public function getChildMenuCollection ($parentId)
    {
    	$chilMenu = Mage::getModel('menupro/menupro')->getCollection()->setOrder("position","asc");
    	//$chilMenu->addFieldToFilter('status','1');
        $chilMenu->addFieldToFilter('parent_id',$parentId);
        return $chilMenu;
    }
    public function getAllMenu()
    {
    	$menus = Mage::getModel('menupro/menupro')->getCollection()->setOrder("group_id","asc")->setOrder("position","asc");
		//$menus->addFieldToFilter('group_id',$group_Id);
    	//$menus->addFieldToFilter('status','1');
    	return $menus;
    }
	public function recursiveMenu ($parentid){
    	$chils = $this->getChildMenuCollection($parentid);
    	foreach($chils as $value) {
    		$haschild=$this->getChildMenuCollection($value->getMenuId());
    		$this->_grid_nav .= "<li id='m-" . $value->getMenuId() . "' store='" . $value->getStoreids() . "' class='" 
				. ((count($haschild) > 0) ? 'sm_liOpen' : '') 
				. (($value->getStatus() != 1) ? ' disabled' : '') 
				. (($value->getType() == 8) ? " separator-line" : "") . "'>";
    			//Is current url is secure or not
    			$edit_link = Mage::helper("adminhtml")->getUrl("menupro/adminhtml_grid/edit/",array("id"=>$value->getMenuId()));
    			$validUrl = Mage::helper('menupro')->getValidUrl($edit_link, Mage::app()->getStore()->isCurrentlySecure());
    			$edit_link = $validUrl;
    			$this->_grid_nav .= '<dl class="sm_s_published">';
    			$this->_grid_nav .= '<a href="#"class="sm_expander">&nbsp;</a>';
    			//Use category title as default
				$menuTitle = $value->getTitle();
				if ($value->getType() == 4 && $value->getUseCategoryTitle() == 2) {
					$menuTitle = Mage::helper('menupro')->getCategoryTitle($value->getUrlValue());
				}
    			$this->_grid_nav .= '<dt><a class="sm_title" href="#" onclick="MCP.editMenu(\'' . $edit_link.'\')">' . $menuTitle . '</a></dt>';
    			$this->_grid_nav .= '<dd class="sm_actions">';
    			$this->_grid_nav .= ' <span class="sm_move" title="Move">Move</span>';
    			$this->_grid_nav .= ' <span class="sm_delete" title="Delete">Delete</span>';
    			$this->_grid_nav .= ' <a href="#" class="sm_addChild" title="Add Child">Add Child</a>';
    			$this->_grid_nav .= '</dd>';
    			$this->_grid_nav .= '<dd class="sm_status"><span class="' . (($value->getStatus() == 1) ? 'sm_pub' : 'sm_unpub') . '" ></span></dd>';
    			$this->_grid_nav .= '</dl>';
    			if(count($haschild)>0){
    				$this->_grid_nav.="<ul>";
    					$this->recursiveMenu($value->getMenuId());
    				$this->_grid_nav.="</ul>";
    			}
    		$this->_grid_nav.="</li>";
    	}
    }
    /*Nav(tree view) on grid.phtml*/
    public function menuLists()
    {
    	//Mage::helper("adminhtml")->getUrl("mymodule/adminhtml_mycontroller/myaction/",array("param1"=>1,"param2"=>2));
    	$_menu = $this->getAllMenu();
		if(count($_menu)>0){
			$this->_grid_nav .= "<ul id='sitemap'>";
				//Store switcher dropdown
				$this->_grid_nav .= "<div class='menupro-switcher'>";
				$this->_grid_nav .= "<span class='store-view'>Choose Store View:</span>";
				$store_switcher = Mage::getModel("menupro/menupro")->storeSwitcher();
				$this->_grid_nav .= $store_switcher;
				$this->_grid_nav .= "</div>";
				foreach ($_menu as $value){
					if ($value->getParentId() == 0) {
						$groupid = $value->getGroupId();
						$haschild = $this->getChildMenuCollection($value->getMenuId());
						//Group title of each group
						if($this->_groupstatus != $groupid){
							$group = Mage::getModel("menupro/groupmenu")->load($groupid);
							$this->_grid_nav .= "<h4 class='group-menu' id='group-".$value->getGroupId()."'>" . strtoupper($group->getTitle()) ."</h4>";
							
							$this->_groupstatus = $groupid;
						}
 						$this->_grid_nav .= "<li id='m-" . $value->getMenuId() . "' store='" . $value->getStoreids() . "' class='" 
							. ((count($haschild) > 0) ? 'sm_liOpen' : '') 
							. (($value->getStatus() != 1) ? ' disabled' : '') 
							. (($value->getType() == 8) ? " separator-line" : "") . "'>";
							
 							//Is current url is secure or not
			    			$edit_link = Mage::helper("adminhtml")->getUrl("menupro/adminhtml_grid/edit/",array("id"=>$value->getMenuId()));
			    			$validUrl = Mage::helper('menupro')->getValidUrl($edit_link, Mage::app()->getStore()->isCurrentlySecure());
			    			$edit_link = $validUrl;
							$this->_grid_nav .= '<dl class="sm_s_published">';
								$this->_grid_nav .= '<a href="#"class="sm_expander">&nbsp;</a>';
								//Use category title as default
								$menuTitle = $value->getTitle();
								if ($value->getType() == 4 && $value->getUseCategoryTitle() == 2) {
									$menuTitle = Mage::helper('menupro')->getCategoryTitle($value->getUrlValue());
								}
								$this->_grid_nav .= '<dt><a class="sm_title" href="#" onclick="MCP.editMenu(\'' . $edit_link.'\')">' . $menuTitle . '</a></dt>';
								$this->_grid_nav .= '<dd class="sm_actions">';
								$this->_grid_nav .= ' <span class="sm_move" title="Move">Move</span>';
								$this->_grid_nav .= ' <span class="sm_delete" title="Delete">Delete</span>';
								$this->_grid_nav .= ' <a href="#" class="sm_addChild" title="Add Child">Add Child</a>';
								$this->_grid_nav .= '</dd>';
								$this->_grid_nav .= '<dd class="sm_status"><span class="' . (($value->getStatus() == 1) ? 'sm_pub' : 'sm_unpub') . '"></span></dd>';
							$this->_grid_nav .= '</dl>';
							if (count($haschild) > 0) {
								$this->_grid_nav .= "<ul>";
									$this->recursiveMenu($value->getMenuId());
								$this->_grid_nav .= "</ul>";
							}
						$this->_grid_nav .= "</li>";
					}
				}
			$this->_grid_nav .= "</ul>";
		}else{
			$this->_grid_nav .= "<span class='no-menu' style='margin-left: 75px;'><em>There is no menu on this group <br/></em></span>";
		} 
    	return $this->_grid_nav;
    }  
}