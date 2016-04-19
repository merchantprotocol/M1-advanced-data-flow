<?php
class Perception_Bannerpro_Adminhtml_BannerproController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('bannerpro/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('bannerpro/bannerpro')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('bannerpro_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('bannerpro/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			
			if (Mage::getSingleton('bannerpro/wysiwyg_config')->isEnabled()) {
				$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
            }
			
			if (Mage::getSingleton('bannerpro/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
            } 

			$this->_addContent($this->getLayout()->createBlock('bannerpro/adminhtml_bannerpro_edit'))
				->_addLeft($this->getLayout()->createBlock('bannerpro/adminhtml_bannerpro_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('bannerpro')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS . 'bannerpro' . DS ;
					$uploader->save($path, $_FILES['filename']['name'] );
					
				} catch (Exception $e) {
		      
		        }
	        	$img_name = str_replace(" ","_",$_FILES['filename']['name']); 
			    //this way the name is saved in DB
				$data['filename'] = 'bannerpro/'.$img_name; 	//$_FILES['filename']['name']; 
				$data['image_thumb'] = $img_name; 				//$_FILES['filename']['name'];
				$data['stores'] = $this->getRequest()->getPost('stores');
	  			//$data['image_thumb'] = "<img width='100' src='".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$_FILES['filename']['name']."'/>";
			}
			 else {
					if(isset($data['filename']['delete']) && $data['filename']['delete'] == 1) {
						 $data['filename'] = '';
					} else {
						unset($data['filename']);
					}
				}	
			
			/*	if($this->getRequest()->getParam('id'))
					{
					//if edit the video details, delete old images from media folder
						if($data['filename']!= '')
						{
							$imageupload = Mage::getModel('bannerpro/bannerpro')->load($this->getRequest()->getParam('id'));
							$imagePath = Mage::getBaseDir('media').DS. "bannerpro" . DS ;
							$imageName = $imageupload->getFilename();
							unlink($imagePath.$imageName);
						}
					}	
			*/
	  			
			$model = Mage::getModel('bannerpro/bannerpro');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
			//	echo '<pre>';
				//print_r($_POST['text']);
			//	die;
				
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('bannerpro')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('bannerpro')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('bannerpro/bannerpro');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $bannerproIds = $this->getRequest()->getParam('bannerpro');
        if(!is_array($bannerproIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($bannerproIds as $bannerproId) {
                    $bannerpro = Mage::getModel('bannerpro/bannerpro')->load($bannerproId);
                    $bannerpro->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($bannerproIds)
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
        $bannerproIds = $this->getRequest()->getParam('bannerpro');
        if(!is_array($bannerproIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($bannerproIds as $bannerproId) {
                    $bannerpro = Mage::getSingleton('bannerpro/bannerpro')
                        ->load($bannerproId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($bannerproIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'bannerpro.csv';
        $content    = $this->getLayout()->createBlock('bannerpro/adminhtml_bannerpro_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'bannerpro.xml';
        $content    = $this->getLayout()->createBlock('bannerpro/adminhtml_bannerpro_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}
