<?php
/**
* Magento Support Team.
* @category   MST
* @package    MST_Menupro
* @version    2.0
* @author     Magebay Developer Team <info@magebay.com>
* @copyright  Copyright (c) 2009-2013 MAGEBAY.COM. (http://www.magebay.com)
*/
class MST_Menupro_Adminhtml_MenuproController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('menupro/menupro')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Menus Manager'), Mage::helper('adminhtml')->__('Menu Manager'));
        return $this;
    }
    public function indexAction()
    {
        $this->_initAction();
        //$this->_addContent($this->getLayout()->createBlock('menupro/adminhtml_menupro'));
		$this->loadLayout();
		//$this->getLayout()->createBlock('menupro/adminhtml_menupro');
        $this->renderLayout();
    }
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('menupro/menupro')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('menupro_data', $model);
            $this->loadLayout();
            $this->_setActiveMenu('menupro/menupro');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Menu Manager'), Mage::helper('adminhtml')->__('Menu Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Menu Manager'), Mage::helper('adminhtml')->__('Menu Manager'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('menupro/adminhtml_menupro_edit'))
                ->_addLeft($this->getLayout()->createBlock('menupro/adminhtml_menupro_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('menupro')->__('Menu does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction() {
		$message = new MST_Menupro_Block_Adminhtml_Messages();
        $imagedata = array();
        if (!empty($_FILES['image']['name'])) {
            try {
                $ext = substr($_FILES['image']['name'], strrpos($_FILES['image']['name'], '.') + 1);
                $fname = 'Image-' . time() . '.' . $ext;
                $uploader = new Varien_File_Uploader('image');
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png')); // or pdf or anything
				
				/* $size=filesize($_FILES['image']['tmp_name']);
				$test=getimagesize($_FILES['image']['tmp_name']); */
                
				$uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);

                $path = Mage::getBaseDir('media').DS.'menupro';

                $uploader->save($path, $fname);

                $imagedata['image'] = 'menupro/'.$fname;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        if ($data = $this->getRequest()->getPost()) {
            if (!empty($imagedata['image'])) {
                $data['image'] = $imagedata['image'];
            } else {
                if (isset($data['image']['delete']) && $data['image']['delete'] == 1) {
                    if ($data['image']['value'] != '') {
                        $_helper = Mage::helper('menupro');
                        $this->removeFile(Mage::getBaseDir('media').DS.$_helper->updateDirSepereator($data['image']['value']));
                    }
                    $data['image'] = '';
                } else {
                    unset($data['image']);
                }
            }
            $model = Mage::getModel('menupro/menupro');
            if ($data['menu_id'] == "") {
            	$model->setData($data)
            	->setId(NULL);
            } else {
            	$model->setData($data)
            	->setId($data['menu_id']);
            }
            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                            ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }
				/*Multiselect-Permission*/
				/* $permission="";
				if(isset($_POST['permission'])){
					foreach($_POST['permission'] as $value){
						$permission.=$value.",";
					}
				}else{
					//Allow all
					$permission="-1";
				}
				$model->setPermission($permission); */
				/*Multiselect-StoreIds*/
            	$storeids="";
				if(isset($_POST['storeids'])){
					foreach($_POST['storeids'] as $value){
						$storeids.=$value.",";
						//If select all store view (value=0)
						if($value=="0"){
							//Load all store view id
							$storeids="0".",";
							$allStores = Mage::app()->getStores();
							foreach ($allStores as $_eachStoreId => $val) 
							{
								$_storeId = Mage::app()->getStore($_eachStoreId)->getId();
								$storeids.=$_storeId.",";
							}
						}
					}
					$model->setStoreids($storeids);
				}else{
					$storeids="";
					$allStores = Mage::app()->getStores();
					foreach ($allStores as $_eachStoreId => $val)
					{
						$_storeId = Mage::app()->getStore($_eachStoreId)->getId();
						$storeids.=$_storeId.",";
					}
					
					$model->setStoreids("0,".$storeids);
				}
				if(isset($_POST['autosub'])){
					$model->setAutosub($_POST['autosub']);
				}else{
					$model->setAutosub(2);
				}
				
				if(isset($_POST['use_category_title'])){
					$model->setUseCategoryTitle($_POST['use_category_title']);
				}else{
					$model->setUseCategoryTitle(2);
				}
				
				// edit by david
//				$main_domain = Mage::helper('menupro')->get_domain( $_SERVER['SERVER_NAME'] );
//				if ( $main_domain != 'dev' ) {
//				$rakes = Mage::getModel('menupro/license')->getCollection();
//				$rakes->addFieldToFilter('path', 'menupro/license/key' );
//				$valid = false;
//				if ( count($rakes) > 0 ) {
//					foreach ( $rakes as $rake )  {
//						if ( $rake->getExtensionCode() == md5($main_domain.trim(Mage::getStoreConfig('menupro/license/key')) ) ) {
//							$valid = true;	
//						}
//					}
//				}
//				if ( $valid == false )  {  
//					//Mage::getSingleton('adminhtml/session')->addError( 'Please enter license key !' );
//					$message->setMenuMessage('error', 'Please enter license key !');
//					Mage::getSingleton('adminhtml/session')->setShowMessage(MST_Menupro_Block_Adminhtml_Messages::LICENSE_CHECK);
//					//Mage::getSingleton('adminhtml/session')->setFormData( $data );
//					$this->_redirect('*/*/');
//					return;
//				}
//				}
				// end edit by david
				
                $model->save();
				Mage::getSingleton('core/session')->setActiveMenuId("m-" . $model->getMenuId());// format of li id is : m-[id]
                //Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('menupro')->__('Menu was successfully saved'));
				$message->setMenuMessage('success', 'Menu was successfully saved');
				Mage::getSingleton('adminhtml/session')->setShowMessage(1);
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                //Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$message->setMenuMessage('error', $e->getMessage());
				Mage::getSingleton('adminhtml/session')->setShowMessage(1);
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        //Mage::getSingleton('adminhtml/session')->addError(Mage::helper('menupro')->__('Unable to find menu to save'));
		$message->setMenuMessage('error', 'Unable to find menu to save');
		Mage::getSingleton('adminhtml/session')->setShowMessage(1);
        $this->_redirect('*/*/');
    }
    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('menupro/menupro')->load($this->getRequest()->getParam('id'));
                $_helper = Mage::helper('menupro');
                $filePath = Mage::getBaseDir('media').DS.$_helper->updateDirSepereator($model->getImage());
                $model->delete();
                //$this->removeFile($filePath);
                unlink($filePath);

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Menu was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
    public function massDeleteAction() {
        $itemIds = $this->getRequest()->getParam('menupro');
        if (!is_array($itemIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($itemIds as $itemId) {
                    $model = Mage::getModel('menupro/menupro')->load($itemId);
                    $_helper = Mage::helper('menupro');
                    $filePath = Mage::getBaseDir('media').DS.$_helper->updateDirSepereator($model->getImage());
                    $model->delete();
                    //$this->removeFile($filePath);
                    unlink($filePath);
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($itemIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    public function massStatusAction()
    {
        $menuIds = $this->getRequest()->getParam('menupro');
        if (!is_array($menuIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select menu(s)'));
        } else {
            try {
                foreach ($menuIds as $menuId) {
                    $menupro = Mage::getSingleton('menupro/menupro')
                            ->load($menuId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($menuIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName = 'menupro.csv';
        $content = $this->getLayout()->createBlock('menupro/adminhtml_menupro_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName = 'menupro.xml';
        $content = $this->getLayout()->createBlock('menupro/adminhtml_menupro_grid')
                ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
    protected function removeFile($file) {
        try {
            $io = new Varien_Io_File();
            $result = $io->rmdir($file, true);
        } catch (Exception $e) {

        }
    }
}