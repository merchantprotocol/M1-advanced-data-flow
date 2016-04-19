<?php
class MST_Menupro_Model_Categories extends Mage_Core_Model_Abstract
{
	protected $category_option = array();
	protected $optionsymbol = "";
	protected $category = "";
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('menupro/categories');
    }
	public function getChildCategoryCollection($parentId)
    {
		$categories=$this->getCategories();
		$categories->addFieldToFilter("parent_id",$parentId);
    	return $categories;
    }
    public function getCategories()
    {
    	$categories = Mage::getModel('catalog/category')
                    ->getCollection()
                    ->addAttributeToSelect('*')
                    ->addIsActiveFilter();
		$categories->addFieldToFilter ( 'include_in_menu', 1 );
    	return $categories;
    }
    /*Get all parent menu fill to select box*/
	public function selectRecursiveCategories($parentID)
	{
		$childCollection=$this->getChildCategoryCollection($parentID);
		foreach($childCollection as $value){
			$categoryId=$value->getEntityId();
			//Check this menu has child or not
			$this->optionsymbol=Mage::helper("menupro")->getCategorySpace($categoryId);
			$this->category_option[$categoryId]=$this->optionsymbol.$value->getName();
			$hasChild=$this->getChildCategoryCollection($categoryId);
			if(count($hasChild)>0)
			{
				$this->selectRecursiveCategories($categoryId);
			}
		}
	}
	public function getCategoryOptions()
	{
		$categories=$this->getCategories();
		foreach ($categories as $value) {
			if($value->getParentId()==1){
				$categoryid=$value->getEntityId();
				$this->category_option[$categoryid]=$value->getName();
				//Check has child menu or not
				$hasChild=$this->getChildCategoryCollection($categoryid);
				if(count($hasChild)>0)
				{
					$this->selectRecursiveCategories($categoryid);
				}
			}
		}
		//array_unshift($this->category_option, array('label' => '--Select category--', 'value' => ''));
		return $this->category_option;
	}
	
	public function selectRecursiveAutoShowSub($parentID)
	{
		$childCollection = $this->getChildCategoriesByParentId($parentID);
		foreach($childCollection as $value){
			$categoryid = $value['entity_id'];//$value->getEntityId();
			$name = $value['name'];//$value->getName();
			$url = $value['url'];//$value->getUrl();
			
			//Check has child menu or not
			$hasChild = $this->getChildCategoryCollection($categoryid);
			$liClass = "autosub-item ";
			$dataHover = $ulClass = '';
			if ( count($hasChild) > 0) {
				$liClass .= "mcpdropdown has-submenu";
				$dataHover .= 'data-hover="mcpdropdown"';
				$ulClass .= 'mcpdropdown-menu';
			}
			$this->_tree .= "<li class='" . $liClass . "'>";
				$this->_tree .= "<a href='" . $url . "' " . $dataHover . ">{$name}</a>";
				if( count($hasChild) > 0)
				{
					$this->_tree .= "<ul class='" . $ulClass . "'>";
						$this->selectRecursiveAutoShowSub($categoryid);
					$this->_tree .= "</ul>";
				}
			$this->_tree .= "</li>";
		}
	}
	
	public function autoShowSubCategory ($categoryId)
	{
		$categories = $this->getChildCategoriesByParentId($categoryId);
		//$this->_tree .= "<ul class='mcpdropdown-menu'>";
		foreach ($categories as $value) {
			$categoryid = $value['entity_id'];//$value->getEntityId();
			$name = $value['name'];//$value->getName();
			$url = $value['url'];//$value->getUrl();
			
			//Check has child menu or not
			$hasChild = $this->getChildCategoryCollection($categoryid);
			$liClass = "autosub-item ";
			$ulClass = $dataHover = '';
			if ( count($hasChild) > 0) {
				$liClass .= "parent mcpdropdown has-submenu";
				$dataHover .= 'data-hover="mcpdropdown"';
				$ulClass .= 'mcpdropdown-menu';
			}
			$this->_tree .= "<li class='" . $liClass . "'>";
				$this->_tree .= "<a href='" . $url . "' " . $dataHover . ">{$name}</a>";
				if( count($hasChild) > 0)
				{
					$this->_tree .= "<ul class='" . $ulClass . "'>";
						$this->selectRecursiveAutoShowSub($categoryid);
					$this->_tree .= "</ul>";
				}
			$this->_tree .= "</li>";
		}
		//$this->_tree .= "</ul>";
		return $this->_tree;
	}
	
	public function getChildCategoriesByParentId($parentid){
		/*When try to filter like: addFieldToFilter("parent_id",$parentid) is not working, so let do this*/
		$_child_collection = array();
		$categories = Mage::getModel("menupro/categories")->getCategories();
		foreach ($categories as $value){
			if($value->getParentId() == $parentid)
			{
				$_child_collection[] = array( "entity_id" => $value->getEntityId(),
											  "name" => $value->getName(),
											  "url" => $value->getUrl());
			}
		}
		return $_child_collection;
	}
	public function recursiveCategories($parentID)
	{
		$childCollection=$this->getChildCategoriesByParentId($parentID);
		foreach($childCollection as $value){
			$categoryId=$value["entity_id"];
			//Check this menu has child or not
			$hasChild=$this->getChildCategoriesByParentId($categoryId);
			$li_class="";
			if(count($hasChild)>0){
				$li_class=" parent";
			}
			$this->category.="<li id='auto-".$categoryId."' class='".$li_class."'>";
			$this->category.="<a href='".$value["url"]."'><span>".$value["name"]."</span></a>";
			if(count($hasChild)>0)
			{
				$this->category.="<ul id='c-".$categoryId."' >";
				$this->recursiveCategories($categoryId);
				$this->category.="</ul>";
			}
			$this->category.="</li>";
		}
	}
	
	public function getCategoriesById($id,$groupid, $parentid, $permission,$storeid)
	{
		$categories=$this->getChildCategoriesByParentId($id);
		$this->category.="<ul id='ca-".$id."'>";
		
		foreach ($categories as $value) {
			$categoryid=$value["entity_id"];
			//Check has child menu or not
			$hasChild=$this->getChildCategoriesByParentId($categoryid);
			$li_class="";
			if(count($hasChild)>0){
				$li_class=" parent";
			}
			$this->category.="<li id='auto-".$categoryid."' class='".$li_class."'>";
			$this->category.="<a href='".$value["url"]."'><span>".$value["name"]."</span></a>";
			
			if(count($hasChild)>0)
			{
				$this->category.="<ul id='c-".$categoryid."'>";
				$this->recursiveCategories($categoryid);
				$this->category.="</ul>";
			}
			$this->category.="</li>";
		}
		
		$this->menuRecursive($groupid, $parentid, $permission,$storeid);
		
		$this->category.="</ul>";
		return $this->category;
	}
	public function menuRecursive($groupid, $parentid, $permission,$storeid){
		$childs=Mage::getModel('menupro/menupro')->getChildMenu($groupid,$parentid,$permission,$storeid);
		foreach($childs as $value){
			$type = $value->getType();
			switch ($type) {
				case 1:
						if($value->getUrlValue()=='home'){
							$url_value=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
						}
						else {
							$url_value=Mage::Helper('cms/page')->getPageUrl($value->getUrlValue());
						}
						$allParentIds=Mage::helper('menupro')->getParentIds($value->getMenuId());
						break;
				case 2:
						$_newProduct = Mage::getModel('catalog/product')->load($value->getUrlValue());
						$url_value = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK).$_newProduct->getUrlPath();
						break;
				case 3:
						$url_value_temp =  $value->getUrlValue();
						if(strpos($url_value_temp, 'http')===false){
							$url_value=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
							$url_value.=$url_value_temp;
						}else{
							$url_value=$value->getUrlValue();
						}
						break;
				case 4:
						$url_value = Mage::getModel('catalog/category')->load($value->getUrlValue())->getUrl();
						break;
				case 6:
						$block = Mage::getSingleton('core/layout')->createBlock('cms/block')->setBlockId($value->getUrlValue());
						break;
				case 7:
						$url_value="#";
						break;
			}
			/*Check has child or not*/
			$parent=false;
			$hasChilds=Mage::getModel('menupro/menupro')->getChildMenu($value->getGroupId(),$value->getMenuId(),$permission,$storeid);
			if(count($hasChilds)>0){
				$parent=true;
			}
			/*Prepare for <li>'s tag*/
			$li_class="";
			$li_id="";
			$li_custom_width="";
			if($value->getCustomWidth()!=''){
				$li_custom_width=$value->getCustomWidth();
			}
			$li_id=$value->getLiId();
			if($li_id==""){ 
				$li_id="m-".$value->getMenuId();
			}
			$li_class=" nav-".$value->getDepth()." ";
			if($url_value==Mage::helper('menupro')->getCurrentUrl()){
				$li_class.=" active ";
			}	
			if($parent==true){
				$li_class.=" parent ";
			}
			/*Config from backend form*/
			$li_class.=$value->getClassSubfix()." ";
			/*End prepare <li> tag*/
			
			if($li_custom_width!=''){
				$this->category.="<li id='".$li_id."' class='".$li_class."' style='width:".$li_custom_width."px'>";
			}else{
				$this->category.="<li id='".$li_id."' class='".$li_class."'>";
			}

			/*Add static block*/
			if($value->getType()==6){
				$this->category.=$block->toHtml();
			}	
			/*Prepare for <a>'s tag*/
			$a_classes=$a_href=$a_image=$a_text=$a_other_attr=$a_target="";
			/*href*/
			if($value->getType()!=5 and $value->getType()!=6){ 
				$a_href=$url_value;
			}else{
				//$a_href="#";
			}
			/*text*/
			if($value->getType()!=5 and $value->getType()!=6){
				if($url_value=="#"){
					$a_text="<span class='title'>".$value->getTitle()."</span>";
				}else{
					$a_text="<span>".$value->getTitle()."</span>";
				}
				if($value->getCaption()!=""){
					$a_text.="<span class='caption'>".$value->getCaption()."</span>";
				}	
			}
			/*image*/
			if($value->getImageStatus()==1 and $value->getImage()!=""){
				$image = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'/'.$value->getImage();
				$a_image='<span class="icon" style="background-image:url('.$image.')"></span>';
			}
			/*Other attribute*/ 
			if($value->getOtherAttribute()!=""){
				$a_other_attr=$value->getOtherAttribute();
			}	
			/*<a> Target*/
			$value->getTarget()==1?$a_target="_self":$a_target="_blank";
			/*End Prepare for <a>'s tag*/
			
			if($value->getType()!=5 and $value->getType()!=6){
				$this->category.="<a href='".$a_href."' ".$a_other_attr." target='".$a_target."'>".$a_image.$a_text."</a>";
			}
			if($type==4){
				/*Auto add all sub category if it selected*/
				if($value->getAutosub()==1){
					$autosub=Mage::getModel("menupro/categories")->getCategoriesById($value->getUrlValue(),$value->getGroupId(),$value->getMenuId(),$permission,$storeid);
					$this->category.=$autosub;
				}else{

					if(count($hasChilds)>0){
						$this->category.="<ul id='u-".$value->getMenuId()."'>";
							$this->menuRecursive($value->getGroupId(),$value->getMenuId(),$permission,$storeid);
						$this->category.="</ul>";
					}	
				}
			}else{
				/* recursion menu*/
				if(count($hasChilds)>0){
					$this->category.="<ul id='u-".$value->getMenuId()."'>";
						$this->menuRecursive($value->getGroupId(),$value->getMenuId(),$permission,$storeid);
					$this->category.="</ul>";
				}
			}
			$this->category.="</li>";
		}
	}
	//--------------------------------------------------
	public function autoSub ($categoryId, $tree, $autoShowSub = null, $showMenuInLevel2, $girdContainer, $subArrow)
	{
		if ($autoShowSub == null) {
			return false;
		}
		if (!is_numeric($categoryId)) {
			return false;
		}
		$arrowSymbol = $subArrow;//'<span class="mcp-arrow-down"></span>';
		$helper = Mage::helper('menupro');
		$isDevelopMode = $helper->isDevelopMode();
		//Check current url is secure or not
		$baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
		$validUrl = Mage::helper('menupro')->getValidUrl($baseUrl, Mage::app()->getStore()->isCurrentlySecure());
		$baseUrl = $validUrl;
		
		$children = $tree[$categoryId]['children'];
		if ($children == "") {
			return;
		}
		$childIds = explode(',', $children);
		$html = "";
		$html .= "<ul>";
		foreach ($childIds as $childId) {
			//-----------------Level 1-----------------------
			$child = $tree[$childId];
			//Update url_path in enterprise version
			if (!isset($child['url_path'])) {
				$newUrlPath = Mage::getModel('catalog/category')->load($child['entity_id'])->getUrl();
				$child['url_path'] = str_replace($baseUrl, '', $newUrlPath);
			}
			$isShow = false;
			$childIds = $this->getChildIds($tree, $childId);
			$liClass = "autosub-item ";
			$ulClass = $dataHover = $arrow = '';
			if ( count($childIds) > 0) {
				$liClass .= "parent mcpdropdown has-submenu";
				$dataHover .= 'data-hover="mcpdropdown"';
				$ulClass .= 'mcpdropdown-menu';
				$arrow = $arrowSymbol;
			}
			if ($child['include_in_menu'] == 1 && $child['is_active'] == 1) {
				$isShow = true;
			}
			if ($isShow) {
				$html .= "<li class='" . $liClass . "'>";
				if ($isDevelopMode) {
					$html .= '<a title="'. $helper->__($child['name']) . '" href="' . $baseUrl . $child['url_path']  . '">' . $helper->__($child['name']) . '</a>' . $arrow;
				} else {
					$categoryName = $child['name'];
					$html .= '<a title="<?php echo $this->__("'. $categoryName .'") ?>" href="<?php echo $this->getMCPUrl()?>' . $child['url_path'] .'"><?php echo $this->__("'. $categoryName .'") ?></a>';
				}
				if ( count($childIds) > 0) {
					//--------Level 2----------
					$html .= "<div class='grid-container" . $girdContainer . "'>";
					$html .= "<ul class='" . $ulClass . "'>";
					foreach($childIds as $childId) {
						$child = $tree[$childId];
						if (!isset($child['url_path'])) {
							$newUrlPath = Mage::getModel('catalog/category')->load($child['entity_id'])->getUrl();
							$child['url_path'] = str_replace($baseUrl, '', $newUrlPath);
						}
						$isShow = false;
						$childIds = $this->getChildIds($tree, $childId);
						$liClass = "autosub-item ";
						$ulClass = $dataHover = $arrow = '';
						if ( count($childIds) > 0) {
							$liClass .= "parent mcpdropdown has-submenu";
							$dataHover .= 'data-hover="mcpdropdown"';
							$ulClass .= 'mcpdropdown-menu';
							$arrow = $arrowSymbol;
						}
						if ($child['include_in_menu'] == 1 && $child['is_active'] == 1) {
							$isShow = true;
						}
						if ($isShow) {
							$html .= "<li class='level3 " . $liClass . "'>";
                            if ($isDevelopMode) {
								$html .= '<a title="'. $helper->__($child['name']) . '" href="' . $baseUrl . $child['url_path']  . '">' . $helper->__($child['name']) . '</a>' . $arrow;
							} else {
								$categoryName = $child['name'];
								$html .= '<a title="<?php echo $this->__("'. $categoryName .'") ?>" href="<?php echo $this->getMCPUrl()?>' . $child['url_path'] .'"><?php echo $this->__("'. $categoryName .'") ?></a>';
							}
							// ------------- Level 3---------------
							if ( count($childIds) > 0) {
								$html .= "<div class='grid-container" . $girdContainer . "'>";
								$html .= "<ul class='" . $ulClass . "'>";
								foreach($childIds as $childId) {
									$child = $tree[$childId];
									if (!isset($child['url_path'])) {
										$newUrlPath = Mage::getModel('catalog/category')->load($child['entity_id'])->getUrl();
										$child['url_path'] = str_replace($baseUrl, '', $newUrlPath);
									}
									$isShow = false;
									$childIds = $this->getChildIds($tree, $childId);
									$liClass = "autosub-item ";
									$ulClass = $dataHover = $arrow = '';
									if ( count($childIds) > 0) {
										$liClass .= "parent mcpdropdown has-submenu";
										$dataHover .= 'data-hover="mcpdropdown"';
										$ulClass .= 'mcpdropdown-menu';
										$arrow = $arrowSymbol;
									}
									if ($child['include_in_menu'] == 1 && $child['is_active'] == 1) {
										$isShow = true;
									}
									if ($isShow) {
										$html .= "<li class='" . $liClass . "'>";
                                        if ($isDevelopMode) {
											$html .= '<a title="'. $helper->__($child['name']) . '" href="' . $baseUrl . $child['url_path']  . '">' . $helper->__($child['name']) . '</a>' . $arrow;
										} else {
											$categoryName = $child['name'];
											$html .= '<a title="<?php echo $this->__("'. $categoryName .'") ?>" href="<?php echo $this->getMCPUrl()?>' . $child['url_path'] .'"><?php echo $this->__("'. $categoryName .'") ?></a>';
										}
										// ------------- Level 4---------------
										if ( count($childIds) > 0) {
											$html .= "<div class='grid-container" . $girdContainer . "'>";
											$html .= "<ul class='" . $ulClass . "'>";
											foreach($childIds as $childId) {
												$child = $tree[$childId];
												if (!isset($child['url_path'])) {
													$newUrlPath = Mage::getModel('catalog/category')->load($child['entity_id'])->getUrl();
													$child['url_path'] = str_replace($baseUrl, '', $newUrlPath);
												}
												$isShow = false;
												$childIds = $this->getChildIds($tree, $childId);
												$liClass = "autosub-item ";
												$ulClass = $dataHover = $arrow = '';
												if ( count($childIds) > 0) {
													$liClass .= "parent mcpdropdown has-submenu";
													$dataHover .= 'data-hover="mcpdropdown"';
													$ulClass .= 'mcpdropdown-menu';
													$arrow = $arrowSymbol;
												}
												if ($child['include_in_menu'] == 1 && $child['is_active'] == 1) {
													$isShow = true;
												}
												if ($isShow) {
													$html .= "<li class='" . $liClass . "'>";
                                                    if ($isDevelopMode) {
														$html .= '<a title="'. $helper->__($child['name']) . '" href="' . $baseUrl . $child['url_path']  . '">' . $helper->__($child['name']) . '</a>' . $arrow;
													} else {
														$categoryName = $child['name'];
														$html .= '<a title="<?php echo $this->__("'. $categoryName .'") ?>" href="<?php echo $this->getMCPUrl()?>' . $child['url_path'] .'"><?php echo $this->__("'. $categoryName .'") ?></a>';
													}
													$html .= "</li>";
												}
											}
											$html .= "</ul>";
											$html .= "</div>";
										}
										// ------------- Level 4---------------
										$html .= "</li>";
									}
								}
								$html .= "</ul>";
								$html .= "</div>";
								// ------------- Level 3---------------
							}
							$html .= "</li>";
						}
					}
					$html .= "</ul>";
					$html .= "</div>";
					//--------Level 2----------
				}
				$html .= "</li>";
			}
			//-----------------End Level 1-------------------
		}
		$html .= "</ul>";
		return $html;
	}
	
	
	//--------------------------------------------------
	public function autoSubResponsive ($categoryId, $tree, $autoShowSub = null)
	{
		if ($autoShowSub == null) {
			return false;
		}
		if (!is_numeric($categoryId)) {
			return false;
		}
		//Check current url is secure or not
		$baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
		$validUrl = Mage::helper('menupro')->getValidUrl($baseUrl, Mage::app()->getStore()->isCurrentlySecure());
		$baseUrl = $validUrl;
		
		$children = $tree[$categoryId]['children'];
		if ($children == "") {
			return;
		}
		$childIds = explode(',', $children);
		$html = "";
		$html .= "<ul class='mcpdropdown-menu'>";
		foreach ($childIds as $childId) {
			//-----------------Level 1-----------------------
			$child = $tree[$childId];
			$isShow = false;
			$childIds = $this->getChildIds($tree, $childId);
			$liClass = "autosub-item ";
			$ulClass = $dataHover = $plus = '';
			if ( count($childIds) > 0) {
				$liClass .= "mcpdropdown";
				$dataHover .= 'class="mcpdropdown-toggle"';
				$ulClass .= 'mcpdropdown-menu';
				$plus = "<span class='icon-plus expand'></span>";
			}
			if ($child['include_in_menu'] == 1 && $child['is_active'] == 1) {
				$isShow = true;
			}
			if ($isShow) {
				$html .= "<li class='" . $liClass . "'>";
				$html .= "<a href='" . $baseUrl . $child['url_path']  . "' " . $dataHover . ">{$child['name']}</a>" . $plus;
				if ( count($childIds) > 0) {
					//--------Level 2----------
					$child = $tree[$childId];
					$isShow = false;
					$childIds = $this->getChildIds($tree, $childId);
					$liClass = "autosub-item ";
					$ulClass = $dataHover = $plus = '';
					if ( count($childIds) > 0) {
						$liClass .= "mcpdropdown";
						$dataHover .= 'class="mcpdropdown-toggle"';
						$ulClass .= 'mcpdropdown-menu';
						$plus = "<span class='icon-plus expand'></span>";
					}
					$html .= "<ul class='" . $ulClass . "'>";
					foreach($childIds as $childId) {
						$child = $tree[$childId];
						$isShow = false;
						$childIds = $this->getChildIds($tree, $childId);
						$liClass = "autosub-item ";
						$ulClass = $dataHover = $plus = '';
						if ( count($childIds) > 0) {
							$liClass .= "mcpdropdown";
							$dataHover .= 'class="mcpdropdown-toggle"';
							$ulClass .= 'mcpdropdown-menu';
							$plus = "<span class='icon-plus expand'></span>";
						}
						if ($child['include_in_menu'] == 1 && $child['is_active'] == 1) {
							$isShow = true;
						}
						if ($isShow) {
							$html .= "<li class='level3 " . $liClass . "'>";
							$html .= "<a href='" . $baseUrl . $child['url_path']  . "' " . $dataHover . ">{$child['name']}</a>" .$plus;
							// ------------- Level 3---------------
							if ( count($childIds) > 0) {
								$html .= "<ul class='" . $ulClass . "'>";
								foreach($childIds as $childId) {
									$child = $tree[$childId];
									$isShow = false;
									$childIds = $this->getChildIds($tree, $childId);
									$liClass = "autosub-item ";
									$ulClass = $dataHover = $plus = '';
									if ( count($childIds) > 0) {
										$liClass .= "mcpdropdown";
										$dataHover .= 'class="mcpdropdown-toggle"';
										$ulClass .= 'mcpdropdown-menu';
										$plus = "<span class='icon-plus expand'></span>";
									}
									if ($child['include_in_menu'] == 1 && $child['is_active'] == 1) {
										$isShow = true;
									}
									if ($isShow) {
										$html .= "<li class='" . $liClass . "'>";
										$html .= "<a href='" . $baseUrl . $child['url_path']  . "' " . $dataHover . ">{$child['name']}</a>" . $plus;
										// ------------- Level 4---------------
										if ( count($childIds) > 0) {
											$html .= "<ul class='" . $ulClass . "'>";
											foreach($childIds as $childId) {
												$child = $tree[$childId];
												$isShow = false;
												$childIds = $this->getChildIds($tree, $childId);
												$liClass = "autosub-item ";
												$ulClass = $dataHover = '';
												if ( count($childIds) > 0) {
													$liClass .= "parent mcpdropdown";
													$dataHover .= 'class="mcpdropdown-toggle"';
													$ulClass .= 'mcpdropdown-menu';
												}
												if ($child['include_in_menu'] == 1 && $child['is_active'] == 1) {
													$isShow = true;
												}
												if ($isShow) {
													$html .= "<li class='" . $liClass . "'>";
													$html .= "<a href='" . $baseUrl . $child['url_path']  . "' " . $dataHover . ">{$child['name']}</a>";
													$html .= "</li>";
												}
											}
											$html .= "</ul>";
										}
										// ------------- Level 4---------------
										$html .= "</li>";
									}
								}
								$html .= "</ul>";
								// ------------- Level 3---------------
							}
							$html .= "</li>";
						}
					}
					$html .= "</ul>";
					//--------Level 2----------
				}
				$html .= "</li>";
			}
			//-----------------End Level 1-------------------
		}
		$html .= "</ul>";
		return $html;
	}
	
	public function getChildIds($tree, $id)
	{
		if (!array_key_exists($id, $tree)) {
			return;
		}
		$childIds = explode(',', $tree[$id]['children']);
		$showChildIds = array();
		foreach ($childIds as $childId) {
			if ($childId != "") {
				if (array_key_exists ($childId, $tree)) {
					$child = $tree[$childId];
					if ($child['include_in_menu'] == 1 && $child['is_active'] == 1) {
						$showChildIds[] = $childId;
					}
				}
			}
		}
		return $showChildIds;
	}
	//--------------------------------------------------
}