<?php
/**
* Magento Support Team.
* @category   MST
* @package    MST_Menupro
* @version    2.0
* @author     Magebay Developer Team <info@magebay.com>
* @copyright  Copyright (c) 2009-2013 MAGEBAY.COM. (http://www.magebay.com)
*/
class MST_Menupro_Helper_Data extends Mage_Core_Helper_Abstract {
    protected static $egridImgDir = null;
    protected static $egridImgURL = null;
    protected static $egridImgThumb = null;
    protected static $egridImgThumbWidth = null;
    protected $_allowedExtensions = Array();
	protected static $separatorLine = '--------------------';
	
    public function __construct() {
        self::$egridImgDir = Mage::getBaseDir('media') . DS;
        self::$egridImgURL = Mage::getBaseUrl('media');
        self::$egridImgThumb = "thumb/";
        self::$egridImgThumbWidth = 25;
    }
	public function getSeparatorLine()
	{
		return self::$separatorLine;
	}
	
	public function getSubCategories($parentId, $sorted=false, $asCollection=false, $toLoad=true)
    {
        $category = Mage::getModel('catalog/category');
        /* @var $category Mage_Catalog_Model_Category */
        if (!$category->checkId($parentId)) {
            if ($asCollection) {
                return new Varien_Data_Collection();
            }
            return array();
        }

        $tree = $category->getTreeModel();
        /* @var $tree Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Tree */

        $nodes = $tree->loadNode($parentId)
            ->loadChildren()
            ->getChildren();

        $tree->addCollectionData(null, $sorted, $parentId, $toLoad, true);

        if ($asCollection) {
            return $tree->getCollection();
        } else {
            return $nodes;
        }
    }
	
    public function updateDirSepereator($path){
        return str_replace('\\', DS, $path);
    }
    public function getImageUrl($image_file) {
        $url = false;
        if (file_exists(self::$egridImgDir . self::$egridImgThumb . $this->updateDirSepereator($image_file)))
            $url = self::$egridImgURL . self::$egridImgThumb . $image_file;
        else
            $url = self::$egridImgURL . $image_file;
        return $url;
    }
    public function getFileExists($image_file) {
        $file_exists = false;
        $file_exists = file_exists(self::$egridImgDir . $this->updateDirSepereator($image_file));
        return $file_exists;
    }
    public function getImageThumbSize($image_file) {
        $img_file = $this->updateDirSepereator(self::$egridImgDir . $image_file);
        if ($image_file == '' || !file_exists($img_file))
            return false;
        list($width, $height, $type, $attr) = getimagesize($img_file);
        $a_height = (int) ((self::$egridImgThumbWidth / $width) * $height);
        return Array('width' => self::$egridImgThumbWidth, 'height' => $a_height);
    }
    public function deleteFiles($image_file) {
        $pass = true;
        if (!unlink(self::$egridImgDir . $image_file))
            $pass = false;
        if (!unlink(self::$egridImgDir . self::$egridImgThumb . $image_file))
            $pass = false;
        return $pass;
    }
    public function getCurrentUrl() {
		return Mage::helper('core/url')->getCurrentUrl();
	}
	public function getValidUrl ($url, $isSecure) {
		//$isSecure = return Mage::app()->getStore()->isCurrentlySecure();
		if ($isSecure) {
			//If current page in secure mode, but menu url not in secure, => change menu to secure
			//secure mode your current URL is HTTPS
			if (!strpos($url, 'https://')) {
				$validUrl = str_replace('http://', 'https://', $url);
				$url = $validUrl;
			}
		} else {
			//page is in HTTP mode
			if (!strpos($url, 'http://')) {
				$validUrl = str_replace('https://', 'http://', $url);
				$url = $validUrl;
			}
		}
		return $url;
	}
	
	public function getCategoryTitle ($categoryId) {
		$categoryInfo = Mage::getModel('catalog/category')->load($categoryId)->getData();
		return $categoryInfo['name'];
	}
	
	public function isDisableResponsive() {
		$isDisabled = Mage::getStoreConfig('menupro/setting/responsive');
		if ($isDisabled == 1 ) {
			return true;
		}
		return false;
	}
	//Get all parent id(like gran->father->son)
	public function getParentIds($menu_id)
	{
		$menu = Mage::getModel('menupro/menupro');
		$p_id=$menu->load($menu_id)->getParentId();
		$p_ids=$p_id;
		//Stop this function when it parent is root node
		if($p_id!=0)
		{
			$p_ids=$p_ids."-".$this->getParentIds($p_id);
		}
		return $p_ids;
	}
	public function getMenuSpace($menu_id)
	{
		$space="";
		$parentIds=explode("-", $this->getParentIds($menu_id));
		for($i=1; $i<count($parentIds);$i++)
		{
			$space = $space."&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		return array(
			'blank_space' 	=> $space,
			'level'			=> count($parentIds)
		);
		
	}
	public function getCategorySpace($categoryid)
	{
		$path = Mage::getModel('catalog/category')->load($categoryid)->getPath();
		$space="";
		$num=explode("/", $path);
		for($i=1; $i<count($num);$i++)
		{
			$space=$space."&nbsp;&nbsp;&nbsp;";
		}
		return $space;
	}
/*  	public function getMenuIdByUrlValue($url_value)
	{
		$menu = Mage::getModel('menupro/menupro')->getCollection();
		$menu->addFieldToFilter('status','1');
		$menu->addFieldToFilter('url_value',$url_value);
        return $menu;
	}
	public function getMenuId($url_id)//$url_id such as: category_id, product_id or cms_page id 
	{
		$menu_id=$this->getMenuIdByUrlValue($url_id);
		foreach($menu_id as $value)
		{
			return $value->getMenuId();
		}
	} */
	
	/**
	* Check current user permission
	* @return array permission id
	*/
	public function authenticate()
	{
		$permission = array(); 
		$permission [] = -1; // Pulbic as default. For all user
		$customerGroup = null;
		if (Mage::helper('customer')->isLoggedIn()) {
			$customerGroup = Mage::getSingleton('customer/session')->getCustomerGroupId();
			/**
			* User who logged in can see their group permission and registered group as well.
			*/
			$permission [] = -2; //Registered
			$permission [] = $customerGroup; //Registered
		} else {
			$permission [] = Mage::getSingleton('customer/session')->getCustomerGroupId();
		}
		return $permission;
	}
	
	public function getMenuTypes()
	{
		return array(
			'1' => 'CMS Page',
			'4' => 'Category Page',
			'6' => 'Static Block',
			'5' => 'Product Page',
			'3' => 'Custom Url',
			'7' => 'Alias [href=#]',
			'8'	=> 'Separator Line'
		);
	}
	public function getMostUsedLinks()
	{
		return array(
			'account' 	=> 'My Account',
			'cart' 		=> 'My Cart',
			'wishlist' 	=> 'My Wishlist',
			'checkout' 	=> 'Checkout',
			'login' 	=> 'Login',
			'logout' 	=> 'Logout',
			'register' 	=> 'Register',
			'contact' 	=> 'Contact Us'
		);
	}
	public function getMostUsedUrl()
	{
		$baseUrl = Mage::getBaseUrl (Mage_Core_Model_Store::URL_TYPE_LINK);
		return array(
			'account' 	=> $baseUrl . 'customer/account/',
			'cart' 		=> $baseUrl . 'checkout/cart/',
			'wishlist' 	=> $baseUrl . 'wishlist/',
			'checkout' 	=> $baseUrl . 'checkout/',
			'login' 	=> $baseUrl . 'customer/account/login/',
			'logout' 	=> $baseUrl . 'customer/account/logout/',
			'register' 	=> $baseUrl . 'customer/account/create/',
			'contact' 	=> $baseUrl . 'contacts/'
		);
	}
	
	/* edit by David */
	public function get_content_id($file,$id){
		$h1tags = preg_match_all("/(<div id=\"{$id}\">)(.*?)(<\/div>)/ismU",$file,$patterns);
		$res = array();
		array_push($res,$patterns[2]);
		array_push($res,count($patterns[2]));
		return $res;
	}
	
	public function get_div($file,$id){
	    $h1tags = preg_match_all("/(<div.*>)(\w.*)(<\/div>)/ismU",$file,$patterns);
	    $res = array();
	    array_push($res,$patterns[2]);
	    array_push($res,count($patterns[2]));
	    return $res;
	} 
	function get_domain($url)   {   
		$dev = 'dev';
		if ( !preg_match("/^http/", $url) )
			$url = 'http://' . $url;
		if ( $url[strlen($url)-1] != '/' )
			$url .= '/';
		$pieces = parse_url($url);
		$domain = isset($pieces['host']) ? $pieces['host'] : ''; 
		if ( preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs) ) { 
			$res = preg_replace('/^www\./', '', $regs['domain'] );
			return $res;
		}   
		return $dev;
	}
	/* end */
	public function isDevelopMode() {
		$developMode = Mage::getStoreConfig('menupro/performance/develop_mode');
		if ($developMode) {
			return true;
		}
		return false;
	}
}
