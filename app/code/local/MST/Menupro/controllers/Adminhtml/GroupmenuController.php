<?php

class MST_Menupro_Adminhtml_GroupmenuController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('menupro/groupmenu')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Menu Group'), Mage::helper('adminhtml')->__('Manage Menu Group'));

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('menupro/adminhtml_groupmenu'));
        $this->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('menupro/groupmenu')->load($id);
        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
				
            }
            Mage::register('groupmenu_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('menupro/groupmenu');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Menu Group'), Mage::helper('adminhtml')->__('Manage Menu Group'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Menu Group'), Mage::helper('adminhtml')->__('Manage Menu Group'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			
			$this->_addContent($this->getLayout()->createBlock("core/template")->setTemplate("menupro/js.phtml"));
            $this->_addContent($this->getLayout()->createBlock('menupro/adminhtml_groupmenu_edit'))
                ->_addLeft($this->getLayout()->createBlock('menupro/adminhtml_groupmenu_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('menupro')->__('Menu group does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction() {
      
           if ($data = $this->getRequest()->getPost()) {
            	$model = Mage::getModel('menupro/groupmenu');
            	$model->setData($data)->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                            ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
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
//					Mage::getSingleton('adminhtml/session')->addError( 'Please enter license key !' );
//					Mage::getSingleton('adminhtml/session')->setFormData($data);
//					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
//					return;
//				}
//				}
				// end edit by david

                $model->save();
				
				//Update install guide
				$model1 = Mage::getModel('menupro/groupmenu')->load($model->getGroupId());
				$installGuide = Mage::getModel('menupro/groupmenu')->installGuide($model->getPosition(), $model->getGroupId());
				$model1->setDescription($installGuide);
				$model1->save();
				
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('menupro')->__('Group menu was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('menupro')->__('Unable to find group menu to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('menupro/groupmenu')->load($this->getRequest()->getParam('id'));
                $model->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Group menu was successfully deleted'));
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
                    $model = Mage::getModel('menupro/groupmenu')->load($itemId);
                    $model->delete();
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
                    $menupro = Mage::getSingleton('menupro/groupmenu')
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
        $fileName = 'groupmenu.csv';
        $content = $this->getLayout()->createBlock('menupro/adminhtml_groupmenu_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName = 'groupmenu.xml';
        $content = $this->getLayout()->createBlock('menupro/adminhtml_groupmenu_grid')
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
}