<?php
/**
* Magento Support Team.
* @category   MST
* @package    MST_Menupro
* @version    2.0
* @author     Magebay Developer Team <info@magebay.com>
* @copyright  Copyright (c) 2009-2013 MAGEBAY.COM. (http://www.magebay.com)
*/
class MST_Menupro_Model_Menupro extends Mage_Core_Model_Abstract
{
	protected $menustr = ""; 
	protected $optionData = "";
	protected $parentoption = array();
	protected $_grid_nav = "";
	protected $_groupstatus = 0;
	public $_currentStore;

    public function _construct()
    {
        parent::_construct();
        $this->_init('menupro/menupro');
		$this->_currentStore = Mage::app ()->getStore ()->getStoreId ();
    }
	public function getMenuCollectionByStore() {
    	$collection = $this->getCollection()->setOrder('position', 'asc');
    	//Get all menu_id in this store
    	$validMenuIds = array();
    	foreach ($collection as $menuItem) {
    		$tempStore = $menuItem->getStoreids();
    		$tempStoreArr = explode(',', $tempStore);
    		if (in_array($this->_currentStore, $tempStoreArr) || in_array('0', $tempStoreArr)) {
    			$validMenuIds[] = $menuItem->getMenuId();
    		} 
    	}
    	//Add Filter to collection
		$newCollection = $this->getCollection();
    	$newCollection->addFieldToFilter('menu_id', array ("in" => $validMenuIds));
		$newCollection->addFieldToFilter('status', '1');
		$newCollection->setOrder('position', 'asc');
    	return $newCollection;
    }
	function getMenuByGroupId ($group_id, $permission) 
	{
		$menu = $this->getMenuCollectionByStore();
		$menu->addFieldToFilter('group_id', $group_id);
		$menu->addFieldToFilter('permission', array ("in" => $permission));
		
		return $menu;
	}
	public function getAllMenuArr($group_id, $permission)
	{
		$menuArr = array();
		$collection = $this->getMenuByGroupId($group_id, $permission);
		foreach ($collection as $menu) {
			$menuData = $menu->getData();
			$menuArr[$menuData['menu_id']] = $menuData;
		}
		return $menuArr;
	}
	/**
	* Get a collection, get all items have status = 1
	* @param parentId, groupId, permission, storeid
	* @return collection
	*/
 	public function getChildMenu ($group_id, $parent_id, $permission) 
	{
    	$childMenu = $this->getMenuByGroupId($group_id, $permission);
    	$childMenu->addFieldToFilter('group_id', $group_id);
		$childMenu->addFieldToFilter('parent_id', $parent_id);
		$childMenu->addFieldToFilter('permission', array ("in" => $permission));
    	return $childMenu;
    }
	/**
	* Get a collection, get all item even status = 2 (even disabled) for backend
	* @param parentId 
	* @return collection
	*/
    public function getChildMenuCollection ($parentId)
    {
    	$chilMenu= Mage::getModel('menupro/menupro')->getCollection()->setOrder("position","asc");
    	//$chilMenu->addFieldToFilter('status','1');
        $chilMenu->addFieldToFilter('parent_id',$parentId);
        return $chilMenu;
    }
    public function getMenus()
    {
    	$menus=Mage::getModel('menupro/menupro')->getCollection()->setOrder("group_id","asc")->setOrder("position","asc");
    	return $menus;
    }
	public function recursiveMenu ($parentid){
    	$chils=$this->getChildMenuCollection($parentid);
    	foreach($chils as $value){
    		$this->_grid_nav.="<li id='m-".$value->getMenuId()."' store='".$value->getStoreids()."'>";
    			$edit_link=Mage::helper("adminhtml")->getUrl("menupro/adminhtml_grid/edit/",array("id"=>$value->getMenuId()));
    			$this->_grid_nav.='<a href="#" onclick="MCP.editMenu(\''.$edit_link.'\')">'.$value->getTitle().'</a>';
				if($value->getAutosub()==1){
					$this->_grid_nav.="<span><em>...[Auto show sub categories]</em></span>";
				}
    			$haschild=$this->getChildMenuCollection($value->getMenuId());
    			if(count($haschild)>0){
    				$this->_grid_nav.="<ul>";
    					$this->recursiveMenu($value->getMenuId());
    				$this->_grid_nav.="</ul>";
    			}
    		$this->_grid_nav.="</li>";
    	}
    }
    /*Store switcher dropdown*/
    public function storeSwitcher()
    {
    	$store_info=Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true);
		$store_switcher="";
		$store_switcher.="<select id='store_switcher' onChange='MCP.storeFilter(this.value)'>";
			foreach($store_info as $value){
				
				if($value['value']==0){
					$store_switcher.="<option value='0'>".$value['label']."</option>";
				}else{
					$store_switcher.="<optgroup label='".$value['label']."'></optgroup>";
					if(!empty($value['value'])){
						foreach ($value['value'] as $option){
							$store_switcher.="<option value='".$option['value']."'>&nbsp;&nbsp;&nbsp;&nbsp;".$option['label']."</option>";
						}
					}
				}
			}
		$store_switcher.="</select>";
		return $store_switcher;
    }
    /*Store switcher filter dropdown*/
    public function storeSwitcherMulti()
    {
    	$store_info=Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true);
		$store_switcher="";
		$store_switcher.="<select id='storeids' class='required-entry span3' multiple='multiple' name='storeids[]'>";
			foreach($store_info as $value){
				
				if($value['value']==0){
					$store_switcher.="<option selected='selected' value='0'>" . $value['label'] . "</option>";
				}else{
					$store_switcher.="<optgroup label='".$value['label']."'></optgroup>";
					if(!empty($value['value'])){
						foreach ($value['value'] as $option){
							$store_switcher.="<option value='".$option['value']."'>&nbsp;&nbsp;&nbsp;&nbsp;".$option['label']."</option>";
						}
					}
				}
			}
		$store_switcher.="</select>";
		return $store_switcher;
    }
    /*Nav(tree view) on grid.phtml*/
    public function menuLists()
    {
    	//Mage::helper("adminhtml")->getUrl("mymodule/adminhtml_mycontroller/myaction/",array("param1"=>1,"param2"=>2));
    	$_menu=$this->getMenus();
		if(count($_menu)>0){
			$this->_grid_nav.="<ul id='mst-nav' class='dhtmlgoodies_tree'>";
				$this->_grid_nav.='<li id="0" noDrag="true" noSiblings="true" noDelete="true" noRename="true"><a href="#">Root note</a>';
					$this->_grid_nav.="<ul>";
					foreach ($_menu as $value){
						if($value->getParentId()==0){
							$groupid=$value->getGroupId();
							if($this->_groupstatus!=$groupid){
								$group=Mage::getModel("menupro/groupmenu")->load($groupid);
								$this->_grid_nav.="<h4 id='group-".$value->getGroupId()."'>".strtoupper($group->getTitle())."</h4>";
								$this->_groupstatus=$groupid;
							}
							$this->_grid_nav.="<li id='m-".$value->getMenuId()."' store='".$value->getStoreids()."'>";
								$edit_link=Mage::helper("adminhtml")->getUrl("menupro/adminhtml_grid/edit/",array("id"=>$value->getMenuId()));
								$this->_grid_nav.='<a href="#" onclick="MCP.editMenu(\''.$edit_link.'\')">'.$value->getTitle().'</a>';
								if($value->getAutosub()==1){
									$this->_grid_nav.="<span><em>...[Auto show sub categories]</em></span>";
								}
								$haschild=$this->getChildMenuCollection($value->getMenuId());
								if(count($haschild)>0){
									$this->_grid_nav.="<ul>";
										$this->recursiveMenu($value->getMenuId());
									$this->_grid_nav.="</ul>";
								}
							$this->_grid_nav.="</li>";
						}
					}
					$this->_grid_nav.="</ul>";
				$this->_grid_nav.="</li>";	
			$this->_grid_nav.="</ul>";
		}else{
			$this->_grid_nav.="<span class='no-menu'><em>There is no menu on this group <br/></em></span>";
		}
    	return $this->_grid_nav;
    }
    public function getMenuByUrlValue ($url_value)
    {
    	$menu = Mage::getModel('menupro/menupro')->getCollection();
		$menu->addFieldToFilter('status','1');
		$menu->addFieldToFilter('url_value',$url_value);
        return $menu;
    }
    /*Get all parent menu fill to select box*/
	public function selectRecursive ($parentID)
	{
		$childCollection=$this->getChildMenuCollection($parentID);
		foreach($childCollection as $value){
			$menuId = $value->getMenuId();
			//Check this menu has child or not
			$this->optionData = Mage::helper("menupro")->getMenuSpace($menuId);
			$this->parentoption[$menuId] = array('title' => '&nbsp;&nbsp;&nbsp;&nbsp;' . $this->optionData['blank_space'] . $value->getTitle(), 'group_id' => $value->getGroupId(), 'level' => $this->optionData['level']);
			$hasChild = $this->getChildMenuCollection($menuId);
			if(count($hasChild)>0)
			{
				$this->selectRecursive($menuId);
			}
		}
	}
	/*Return an array which use in add or edit form. Parent Id-name*/
	public function getParentOptions()
	{
		$menus=$this->getMenus();
		$this->parentoption[0]=array('title'=>"Root",'group_id'=>'','level' => 0);
		foreach ($menus as $value) {
			if($value->getParentId() == 0)
			{
				$menuid=$value->getMenuId();
				$this->parentoption[$menuid] = array('title'=>'&nbsp;&nbsp;&nbsp;&nbsp;' . $value->getTitle(),'group_id'=>$value->getGroupId(),'level' => 1);
				//Check has child menu or not
				$hasChild=$this->getChildMenuCollection($menuid);
				if(count($hasChild)>0)
				{
					$this->selectRecursive($menuid);
				}
			}
		}
		return $this->parentoption;
	}
	/**
	* Get parent options with json format.
	* @param 
	* @return json
	*/
	/* public function getParentOptionJson()
	{
		$parentNames = Mage::getModel('menupro/menupro')->getParentOptions();
		return json_encode($parentNames);
	} */
	
    static public function getOptionArray()
    {
        $arr_status = array(
                array('value' => 1,'label' => Mage::helper('menupro')->__('Enabled'),),
                array('value' => 2,'label' => Mage::helper('menupro')->__('Disabled'),),
            );
        return  $arr_status;
    }
}