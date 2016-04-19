<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Customerattribute
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Customerattribute Adminhtml ManagecustomerController
 * 
 * @category    Magestore
 * @package     Magestore_Customerattribute
 * @author      Magestore Developer
 */
class Magestore_Customerattribute_Adminhtml_ManagecustomerController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Customerattribute_Adminhtml_CustomerattributeController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('customer/managecustomer/customer')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Items Manager'),
                Mage::helper('adminhtml')->__('Item Manager')
            );
        return $this;
    }
 
    /**
     * index action
     */
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * view and edit item action
     */
    public function editAction()
    {
        $customerattributeId     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('customerattribute/customerattribute')->load($customerattributeId);

        if ($model->getId() || $customerattributeId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('customerattribute_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('customerattribute/customerattribute');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Item Manager'),
                Mage::helper('adminhtml')->__('Item Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Item News'),
                Mage::helper('adminhtml')->__('Item News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('customerattribute/adminhtml_customerattribute_edit'))
                ->_addLeft($this->getLayout()->createBlock('customerattribute/adminhtml_customerattribute_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('customerattribute')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }
 
    public function newAction()
    {
		$this->_redirect('adminhtml/customer/new');
    }
 
    /**
     * save item action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
                try {
                    /* Starting upload */    
                    $uploader = new Varien_File_Uploader('filename');
                    
                    // Any extention would work
                       $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);
                    
                    // Set the file upload mode 
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders 
                    //    (file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);
                            
                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') . DS ;
                    $result = $uploader->save($path, $_FILES['filename']['name'] );
                    $data['filename'] = $result['file'];
                } catch (Exception $e) {
                    $data['filename'] = $_FILES['filename']['name'];
                }
            }
              
            $model = Mage::getModel('customerattribute/customerattribute');        
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));
            
            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('customerattribute')->__('Item was successfully saved')
                );
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
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('customerattribute')->__('Unable to find item to save')
        );
        $this->_redirect('*/*/');
    }
 
    /**
     * delete item action
     */
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('customerattribute/customerattribute');
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Item was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

	
	/*
	mass create by thinhnd
	*/
	public function massSubscribeAction()
    {
        $customersIds = $this->getRequest()->getParam('customerattribute');
        if(!is_array($customersIds)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer(s).'));

        } else {
            try {
                foreach ($customersIds as $customerId) {
                    $customer = Mage::getModel('customer/customer')->load($customerId);
                    $customer->setIsSubscribed(true);
                    $customer->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were updated.', count($customersIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massUnsubscribeAction()
    {
        $customersIds = $this->getRequest()->getParam('customerattribute');
        if(!is_array($customersIds)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer(s).'));
        } else {
            try {
                foreach ($customersIds as $customerId) {
                    $customer = Mage::getModel('customer/customer')->load($customerId);
                    $customer->setIsSubscribed(false);
                    $customer->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were updated.', count($customersIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function massDeleteAction()
    {
        $customersIds = $this->getRequest()->getParam('customerattribute');
        if(!is_array($customersIds)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer(s).'));
        } else {
            try {
                $customer = Mage::getModel('customer/customer');
                foreach ($customersIds as $customerId) {
                    $customer->reset()
                        ->load($customerId)
                        ->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were deleted.', count($customersIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function massAssignGroupAction()
    {
        $customersIds = $this->getRequest()->getParam('customerattribute');
        if(!is_array($customersIds)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer(s).'));
        } else {
            try {
                foreach ($customersIds as $customerId) {
                    $customer = Mage::getModel('customer/customer')->load($customerId);
                    $customer->setGroupId($this->getRequest()->getParam('group'));
                    $customer->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were updated.', count($customersIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }
	/*End*/
	
    /**
     * mass delete item(s) action
     */
	 
    public function massDelete1Action()
    {
        $customerattributeIds = $this->getRequest()->getParam('customerattribute');
        if (!is_array($customerattributeIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($customerattributeIds as $customerattributeId) {
                    $customerattribute = Mage::getModel('customerattribute/customerattribute')->load($customerattributeId);
                    $customerattribute->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted',
                    count($customerattributeIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    /**
     * mass change status for item(s) action
     */
    public function massStatusAction()
    {
        $customerattributeIds = $this->getRequest()->getParam('customerattribute');
        if (!is_array($customerattributeIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($customerattributeIds as $customerattributeId) {
                    Mage::getSingleton('customerattribute/customerattribute')
                        ->load($customerattributeId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($customerattributeIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName   = 'customerattribute.csv';
        $content    = $this->getLayout()
                           ->createBlock('customerattribute/adminhtml_managecustomer_grid')
                           ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName   = 'customerattribute.xml';
        $content    = $this->getLayout()
                           ->createBlock('customerattribute/adminhtml_managecustomer_grid')
                           ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

}