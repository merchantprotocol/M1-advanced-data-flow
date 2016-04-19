<?php
class MST_Menupro_IndexController extends Mage_Core_Controller_Front_Action
{
	protected $error="";
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
	public function updatemenuAction()
	{
		//Some time GET not work, so change it to POST
		//$saveString=$this->getRequest()->getParam("saveString");
		$saveString=$_POST['saveString'];
		$menu=explode(",", $saveString);
		try {
			$position=0;
			foreach($menu as $value){
				if($value!=""){
					$temp=explode("-", $value);
					$id=$temp[0];
					$groupid=$temp[1];
					$parentid=$temp[2];
					/*Update menu*/
					$model=Mage::getModel("menupro/menupro")->load($id);
					$model->setParentId($parentid);
					$model->setGroupId($groupid);
					$model->setPosition($position);
					$model->save();
					$position++;
				}
			}
		} catch (Exception $e) {
			$this->error="ERROR WHEN TRYING TO SAVE MENU";
		}
		if($this->error!=""){
			echo $this->error;
		}else {
			echo "Ok";
		}
	}
	public function deletemenuAction(){
		$ids = $this->getRequest()->getParam("ids");
		try {
			$temp = explode(",", $ids);
			foreach ($temp as $value){
				if($value != ""){
					$id = explode('-', $value);
					$model = Mage::getModel("menupro/menupro")->load($id[1]);
					$model->delete();
				}
			}
			
		} catch (Exception $e) {
			$this->error="ERROR WHEN TRYING TO DELETE MENU";
		}
		if($this->error!=""){
			echo $this->error;
		}else{
			echo "Ok";
		}
	}
	public function renamemenuAction(){
		$id=$this->getRequest()->getParam("id");
		$newname=$this->getRequest()->getParam("newname");
		try {
			$model=Mage::getModel("menupro/menupro")->load($id);
			$model->setTitle($newname);
			$model->save();
		} catch (Exception $e) {
			$this->error="ERROR WHEN TRYING TO RENAME MENU";
		}
		if($this->error!=""){
			echo $this->error;
		}else{
			echo "Ok";
		}
	}
	public function testAction(){
		echo "Hi there <pre>";
		$menuCollection = Mage::getModel('menupro/menupro')->getAllMenuArr(1, -1, 0);
		$finalArr = $this->getFinalMenuArr($menuCollection);
		//echo count($finalArr);
		print_r($finalArr);
		
 		$categories = Mage::getModel('catalog/category')->getCollection();
		foreach ($categories as $category)
		{
			echo $category->getEntityId() . '-' . $category->getChildren() . '<br>';
		}
	}
	
	public function getFinalMenuArr ($menuArr)
	{
		$finalArr = array();
		foreach ($menuArr as $menu) {
			$children = $this->getChildMenus($menuArr, $menu['parent_id']);
			$menu['children'] = $children;
			$finalArr[$menu['menu_id']] = $menu;
		}
		return $finalArr;
	}
	public function getChildMenus ($menuArr, $parentId)
	{
		$childIds = array();
		foreach ($menuArr as $child) {
			if ($child['menu_id'] == $parentId) {
				$childIds[] = $child['menu_id'];
			}
		}
		return join(',', $childIds);
	}
	public function refreshAction()
	{
		Mage::getSingleton('core/session')->setCategoryTree('');
		$message = "Refresh process done!";
		//Remove all static html file
		$menuObj = new MST_Menupro_Block_Menu();
		if (!$menuObj->removeAllStaticHtml()) {
			$message = "Refresh process error!";
		}
		Mage::app()->getResponse()->setBody($message);
	}
}