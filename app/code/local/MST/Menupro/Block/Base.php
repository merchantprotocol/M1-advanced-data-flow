<?php
/**
* Magento Support Team.
* @category   MST
* @package    MST_Menupro
* @version    2.0
* @author     Magebay Developer Team <info@magebay.com>
* @copyright  Copyright (c) 2009-2013 MAGEBAY.COM. (http://www.magebay.com)
*/
class MST_Menupro_Block_Base extends Mage_Core_Block_Template {
	protected $_plus = '<span class="mcp-icon fa-angle-plus-square expand"></span>';
	protected $_data_hover = "data-hover='mcpdropdown'";
	protected $_dropdown_toggle = "class='mcpdropdown-toggle'";
	protected $_rightIcon = '<i class="mcp-icon fa-chevron-right"></i>';
	protected $_urlValue;
	protected $_liClasses;
	protected $_aHref;
	protected $_aImage;
	protected $_aText;
    protected $_aTitle;
	protected $_aIcon;
	protected $_aTarget;
	protected $_parent = false;
	protected $_block;
	// protected $_type;
	protected $_menuLink;
	// protected $_itemUrlValue;
	/**
	* If li has sub item, then we need to add some class such as: parent,
	* has-sub, etc... Need a space before or after each class
	*/
	// protected $_liHasSubClasses = 'mcpdropdown parent ';
	protected $_liHasSubClasses = ' mcpdropdown parent ';
	// A class of li that has a link being actived.
	protected $_liActiveClass = ' active ';
	// Column layout classes: sub_one, sub_two...
	protected $_columnLayout = array (
			1 => 'one',
			2 => 'two',
			3 => 'three',
			4 => 'four',
			5 => 'five',
			6 => 'six',
			100 => 'full' 
	);
	//---Auto Show Sub--
	protected $_tree = array();
	public $categoryObject;
	public function __construct()
	{
        /*$this->setCacheLifetime(3600);
        $this->addData(array(
            'cache_lifetime' => 3600,
            'cache_tags'        => array(Mage_Core_Model_Store::CACHE_TAG, Mage_Cms_Model_Block::CACHE_TAG)
        ));*/
		/**
		 * Speed up connection ....
		 */
		$categoryTree = Mage::getSingleton('core/session')->getCategoryTree();
		$this->categoryObject = Mage::getModel("menupro/categories");
		//Save in session
		//Moi khi thay doi store view thi phai xoa session cu di, de menu co link chinh xac theo tung store
		$currentStoreViewCode = Mage::getSingleton('core/session')->getCurrentStoreViewCode();
		$currentCode = Mage::app()->getStore()->getCode();
		if (!$categoryTree || $currentStoreViewCode != $currentCode) {
			//die('Correct... Testing ... ' . $currentCode);
			$categories = $this->categoryObject->getCategories();
			foreach ($categories as $category)
			{
				$catData = $category->getData();
				//Sorted child 
				$allChild = $category->getChildrenCategories();
				$childString = "";
				if (count($allChild) > 0) {
					$child = array();
					foreach ($allChild as $cate) {
						$child[] = $cate->getData('entity_id');
					}
					//print_r($child);
					$childString = join(',' , $child);
				}
				$catData['children'] = $childString;
				$allCategories[$category->getEntityId()] = $catData;
			}
			Mage::getSingleton('core/session')->setCategoryTree($allCategories);
			Mage::getSingleton('core/session')->setCurrentStoreViewCode($currentCode);
			$this->_tree = $allCategories;
		} else {
			$this->_tree = $categoryTree;
		}

	}
	public function getSortChildCollection($id)
	{
		$collection = Mage::getModel('catalog/category')
					->getCollection()
					->addAttributeToSelect('all_children')
					->addAttributeToFilter('entity_id', $id)
					->load();
		return $collection->toArray();
	}
	
	
	public function resetMenuItemVar() {
		$this->_liClasses = '';
		$this->_aHref = '';
		$this->_aImage = '';
		$this->_aText = '';
        $this->_aTitle = '';
		$this->_aTarget = '';
		$this->_block = '';
		$this->_aIcon = '';
		$this->_parent = false;
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
	
	public function getChildMenu($groupId, $menuId, $permission) {
		return Mage::getModel ( 'menupro/menupro' )->getChildMenu ( $groupId, $menuId, $permission );
	}
	/* public function getCategoriesById($itemUrlValue, $groupId, $menuId, $permission, $storeId) {
		$autosub = Mage::getModel ( "menupro/categories" )->getCategoriesById ( $itemUrlValue, $groupId, $menuId, $permission, $storeId );
		return $autosub;
	} */
	public function getMenuCollection($groupId, $permission) {
		$isEnabled = Mage::getModel('menupro/groupmenu')->load($groupId)->getStatus();
		if ($isEnabled == 1) {
			return Mage::getModel ( "menupro/menupro" )->getMenuByGroupId ( $groupId, $permission);
		}
		return;
	}
	/**
	* @param $type as menu type: cms,block,category,custom @param $itemUrlValue
	* value of item input by user return Link of menu item
	*/
	public function getMenuLink($itemUrlValue, $type) {
		$isDevelopMode = Mage::helper('menupro');
		$store_id = Mage::app()->getStore()->getStoreId();
		$defaultStoreId = Mage::app()->getWebsite()->getDefaultGroup()->getDefaultStoreId();
		// Default store id = 11;
		// If current store is default, then remove store code from menu url
		switch ($type) {
			case 1 :
				if ($itemUrlValue == 'home') {
					if ($store_id == $defaultStoreId) {
						$this->_urlValue = Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_WEB );
					} else {
						$this->_urlValue = Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_LINK );
					}
				} else {
					$this->_urlValue = Mage::Helper ( 'cms/page' )->getPageUrl ( $itemUrlValue );
				}
				break;
			
			case 3 :
				if (strpos ( $itemUrlValue, 'http' ) === false) {
					if ($store_id == $defaultStoreId) {
						$this->_urlValue = Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_WEB ) . $itemUrlValue;
					} else {
						$this->_urlValue = Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_LINK ) . $itemUrlValue;
					}
				} else {
					$this->_urlValue = $itemUrlValue;
				}
				break;
			
			case 4 :
				$rootId = Mage::app()->getStore()->getRootCategoryId();
				if ($itemUrlValue == $rootId) {
					$this->_urlValue = "#";
				} else {
					$this->_urlValue = Mage::getModel('catalog/category')->load($itemUrlValue)->getUrl();
				}
				break;
			
			case 5 :
				$_product = Mage::getModel('catalog/product')->load($itemUrlValue);
				//$this->_urlValue = $_product->getProductUrl(true);// Still Working
				if ($store_id == $defaultStoreId) {
					$baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
				} else {
					$baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
				}
				$this->_urlValue = $baseUrl . $_product->getUrlPath();
				break;			
			case 6 :
				$this->_urlValue = Mage::getSingleton ( 'core/layout' )->createBlock ( 'cms/block' )->setBlockId ( $itemUrlValue );
				break;
			
			case 7 :
				$this->_urlValue = '#';
				break;
			
			default :
				$mostUsedUrl = Mage::helper('menupro')->getMostUsedUrl();
				if (array_key_exists($itemUrlValue, $mostUsedUrl)) {
					$this->_urlValue = $mostUsedUrl[$itemUrlValue];
				} else {
					$this->_urlValue = "";
				}
				break;
		}
		if ($type != 6) {
			//echo "<div style='display:none' class='testtest'><pre>" . print_r(Mage::app()->getStore(Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID)->getData()) . "</pre></div>";
			// Check current page is secure or not
			$validUrl = Mage::helper('menupro')->getValidUrl($this->_urlValue, Mage::app()->getStore()->isCurrentlySecure());
			$this->_urlValue = $validUrl;
		}	
		return $this->_urlValue;
	}
	//If menu don't have any sub
	public function getAutoSubMenuUl(
		$parentCategoryId, $autosub, 
		$childrenWrapClass = '', 
		$parentClass = 'parent mcpdropdown has-submenu ', 
		$ulClass = 'mcpdropdown-menu', 
		$aAttribute = 'data-hover="mcpdropdown"', 
		$extraElement)
	{
		$html = "";
		if ($autosub == 1) {
			$subCollection = Mage::getModel('menupro/categories')->getChildCategoryCollection($parentCategoryId);
			if (count($subCollection) > 0) {
				/* $object = new MST_Menupro_Block_Autosub();
				$html .= "<ul class='" . $ulClass . "'>";//old class : dropdown-menu
				foreach($subCollection as $_category){
					$html .= $object->drawItem($_category, 0, $childrenWrapClass = '', $parentClass, $ulClass, $aAttribute, $extraElement);
				}
				$html .= "</ul>"; */
				$html .= "<ul class='" . $ulClass . "'>";//old class : dropdown-menu
				$html .= Mage::getModel('menupro/categories')->autoShowSubCategory($parentCategoryId);
				$html .= "</ul>";
			}
		}	
		return $html;
	}
	
	public function autoSub($categoryId, $autoShowSub, $showMenuInLevel2 = false, $girdContainer = 3, $subArrow)
	{
		//$categoryTree = Mage::getSingleton('core/session')->getCategoryTree();
		/* $html = $this->categoryObject->autoSub($categoryId, $this->_tree, $autoShowSub);
		return $html; */
		$html = $this->categoryObject->autoSub($categoryId, $this->_tree, $autoShowSub, $showMenuInLevel2, $girdContainer, $subArrow);
		return $html; 
	}
	
	public function autoSubResponsive($categoryId, $autoShowSub)
	{
		//$categoryTree = Mage::getSingleton('core/session')->getCategoryTree();
		/* $html = $this->categoryObject->autoSubResponsive($categoryId, $this->_tree, $autoShowSub);
		return $html; */
		$html = $this->categoryObject->autoSubResponsive($categoryId, $this->_tree, $autoShowSub);
		return $html;
	}
	
	//If menu have sub
	public function getAutoSubMenuLi(
		$parentCategoryId, $autosub, 
		$childrenWrapClass = '', 
		$parentClass = 'parent mcpdropdown has-submenu ', 
		$ulClass = 'mcpdropdown-menu', 
		$aAttribute = 'data-hover="mcpdropdown"', 
		$extraElement = '')
	{
		$html = "";
		if ($autosub == 1) {
			$subCollection = Mage::getModel('menupro/categories')->getChildCategoryCollection($parentCategoryId);
			if (count($subCollection) > 0) {
				/* $object = new MST_Menupro_Block_Autosub();
				foreach($subCollection as $_category){
					$html .= $object->drawItem($_category, 0, $childrenWrapClass = '', $parentClass, $ulClass, $aAttribute, $extraElement);
				} */
				$html .= Mage::getModel('menupro/categories')->autoShowSubCategory($parentCategoryId);
			}
		}
		return $html;
	}
	
	//Accoridion ------------------
	public function getAccoridionAutoSubMenuUl($parentCategoryId, $autosub)
	{
		$html = $this->getAutoSubMenuUl(
			$parentCategoryId, $autosub, 
			'', 
			'parent mcpdropdown ', 
			'mcpdropdown-menu', 
			'class="mcpdropdown-toggle"', 
			'<span class="mcp-icon fa-angle-plus-square expand"></span>'
		);
		return $html;
	}
	//If menu have sub
	public function getAccoridionAutoSubMenuLi($parentCategoryId, $autosub)
	{
		$html = $this->getAutoSubMenuLi(
			$parentCategoryId, $autosub,
			'', 
			'parent mcpdropdown ', 
			'mcpdropdown-menu', 
			'class="mcpdropdown-toggle"', 
			'<span class="mcp-icon fa-angle-plus-square expand"></span>'
		);
		return $html;
	}
	
	/**
	* Only allow block (type = 6) show in level 2 only.
	* @param groupId, menuId, permission, storeid
	* @return array
	*/
	public function getNormalType($groupId, $menuId, $permission, $storeId)
	{
		$childMenu = $this->getChildMenu($groupId, $menuId, $permission, $storeId); 
		$normalArray = null;
		foreach ($childMenu as $menuItem) {
			if ($menuItem->getType() != 6)	{
				$normalArray [] = $menuItem->getMenuId();
			}								
		}
		return $normalArray;
	}
	public function getGroupInfo ($groupId)
	{
		return Mage::getModel('menupro/groupmenu')->load($groupId)->getData();
	}
	public function getGroupOptions($groupId)
	{
		$data = $this->getGroupInfo($groupId);
//		var_dump($data);
		$options['position'] = $data['position'];
		$options['animation'] = $data['animation'];
		$options['responsive'] = $data['responsive'];
		if ($data['color'] != "cyan") {
			//Cyan is default color
			$options['color'] = $data['color'];
		} else {
			$options['color'] = "menu-creator-pro-cyan";
		}
		return join(" ", $options);
	}
	public function getArrow($position) {
		$data = array(
			'menu-creator-pro' => '<span class="mcp-icon fa-angle-down"></span>',
			'menu-creator-pro menu-creator-pro-top-fixed' => '<span class="mcp-icon fa-angle-down"></span>',
			'menu-creator-pro menu-creator-pro-left' => '<span class="mcp-icon fa-angle-right"></span>',
			'menu-creator-pro menu-creator-pro-left-fixed' => '<span class="mcp-icon fa-angle-right"></span>',
			'menu-creator-pro menu-creator-pro-right' => '<span class="mcp-icon fa-angle-left"></span>',
			'menu-creator-pro menu-creator-pro-right-fixed' => '<span class="mcp-icon fa-angle-left"></span>',
			'menu-creator-pro menu-creator-pro-bottom' => '<span class="mcp-icon fa-angle-up"></span>',
			'menu-creator-pro menu-creator-pro-bottom-fixed' => '<span class="mcp-icon fa-angle-up"></span>',
			'menu-creator-pro menu-creator-pro-accordion' => '<span class="mcp-icon fa-angle-down"></span>',
		);
		return $data[$position];
	}
	
	public function getSubArrow ($position) {
		return $this->getArrow($position);
	}
	public function getMCPBaseUrl() {
		$url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
		$validUrl = Mage::helper('menupro')->getValidUrl($url, Mage::app()->getStore()->isCurrentlySecure());
		return $validUrl;
	}
	/** Base url for auto show sub category**/
	public function getMCPUrl() {
		$url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
		$validUrl = Mage::helper('menupro')->getValidUrl($url, Mage::app()->getStore()->isCurrentlySecure());
		return $validUrl;
	}
	public function getStoreId() {
		return Mage::app()->getStore()->getStoreId();
	}
	public function getMenuFilename($groupId) {
		$storeId = $this->getStoreId();
		$extraPath = array($storeId, $groupId);
		$extraPathName =  join('_', $extraPath);
		$name = "menupro_" . $extraPathName . "" . ".phtml";
		return $name;
	}
	public function menuDesignDir() {
		$path = Mage::getBaseDir('design') .DS."frontend".DS."base".DS."default".DS."template".DS."menupro".DS."static".DS;
		return $path;
	}
	public function exportMenupro($menuproHtml, $groupId) {
		$temp1 = str_replace("</div>", "\n</div>\n", $menuproHtml);
		$temp2 = str_replace("</ul>", "</ul>\n", $temp1);
		$temp3 = str_replace("</li>", "\n</li>", $temp2);
		try {
			ob_start();
			echo "<div class='mst'>";
			echo $temp3;
			echo "</div>";
			$content = ob_get_contents();
			//ob_end_flush();
			ob_end_clean();// Will not display the content
			$storeId = $this->getStoreId();
			$path = $this->menuDesignDir();
			$filename = $this->getMenuFilename($groupId);
			file_put_contents($path . $filename, $content);
		} catch (Exception $e) {
			//die($e);
			Mage::log($e, null, 'menupro.log');
		}
		$response = array();
		if (file_exists($path . $filename)) {
			$response['success'] = true;
			$response['filename'] = $filename;
		} else {
			$response['error'] = true;
			$response['message'] = "Can not export menu file! Something went wrong ...";
		}
		return $response;
	}
}