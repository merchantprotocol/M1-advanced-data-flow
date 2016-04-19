<?php
 /**
 * Magento 
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 * 
 * @category    Enterprise
 * @package     Datta_StoreLocator
 * @created     Dattatray Yadav  2nd Dec,2013 2:05pm
 * @author      Clarion magento team<Dattatray Yadav>   
 * @purpose     Store display action in admin area
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License   
 */
class Datta_StoreLocator_Adminhtml_StoreController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_initAction()->renderLayout();
    }  
    public function newAction()
    {
        $this->_forward('edit');
    } 
    public function editAction()
    {
        $this->_initAction();
        $id  = $this->getRequest()->getParam('entity_id');
        $model = Mage::getModel('datta_storelocator/store');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This store no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }     
        $this->_title($model->getId() ? $model->getName() : $this->__('New Store'));
        $data = Mage::getSingleton('adminhtml/session')->getStoreData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        Mage::register('datta_storelocator', $model);
        $this->_initAction()
            ->_addBreadcrumb($id ? Mage::helper('datta_storelocator')->__('Edit Store') : Mage::helper('datta_storelocator')->__('New Store'), $id ? Mage::helper('datta_storelocator')->__('Edit Store') : Mage::helper('datta_storelocator')->__('New Store'))
            ->renderLayout();  
    }           
    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            if(isset($_FILES['image']['name']) and (file_exists($_FILES['image']['tmp_name']))) {
                try {
                    $uploader = new Varien_File_Uploader('image');
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $locImg = 'storelocator/images/';
                    $path = Mage::getBaseDir('media') . DS . $locImg ;
                    $uploader->save($path, $_FILES['image']['name']);
                    $postData['image'] = $_FILES['image']['name'];
                }catch(Exception $e) {

                }
            }else{
                if(isset($postData['image']['delete']) && $postData['image']['delete'] == 1){
                    $postData['image'] = '';
                }else{
                    unset($postData['image']);
                }
            }
            /// check file is exist
            if(isset($_FILES['marker']['name']) and (file_exists($_FILES['marker']['tmp_name']))) {
                try {
                    $uploader = new Varien_File_Uploader('marker');
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $locMarker = 'storelocator/markers/';
                    $path = Mage::getBaseDir('media') . DS . $locMarker ;
                    $uploader->save($path, $_FILES['marker']['name']);
                    $postData['marker'] = $_FILES['marker']['name'];
                }catch(Exception $e) {
            
                }
            }else{
                if(isset($postData['marker']['delete']) && $postData['marker']['delete'] == 1){
                    $postData['marker'] = '';
                }else{
                    unset($postData['marker']);
                }
            }                      
            $model = Mage::getSingleton('datta_storelocator/store');
            if ($id = $this->getRequest()->getParam('entity_id')) {
                $model->load($id);
            }

            $model->setData($postData);

            try {
                if(is_null($model->getCreatedTime()) || $model->getCreatedTime() == ''){
                    $model->setCreatedTime(time());
                }
                $model->setUpdateTime(time());                
                if(substr_compare($model->getStoreUrl(), "http://", 0, 7) > 0 && substr_compare($model->getStoreUrl(), "https://", 0, 8) > 0){
                    $model->setStoreUrl("http://".$model->getStoreUrl());
                }

                if(!is_null($model->getImage()) && $model->getImage() != ''){
                    $filename = str_replace(" ", "_", $model->getImage());
                    $filename = str_replace(":", "_", $filename);
                    $model->setImage($locImg.$filename);
                }
                
                if(!is_null($model->getMarker()) && $model->getMarker() != ''){
                    $filename = str_replace(" ", "_", $model->getMarker());
                    $filename = str_replace(":", "_", $filename);
                    $model->setMarker($locMarker.$filename);
                }                   
                $model->save();                 
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The store has been saved.'));
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('entity_id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this store.'));
            }

            Mage::getSingleton('adminhtml/session')->setStoreData($postData);
            $this->_redirectReferer();
        }
    } 
    public function deleteAction() {
        if( $this->getRequest()->getParam('entity_id') > 0 ) {
            try {
                $model = Mage::getModel('datta_storelocator/store');

                $model->setId($this->getRequest()->getParam('entity_id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('datta_storelocator')->__('Store was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $storelocatorIds = $this->getRequest()->getParam('datta_storelocator');
        if(!is_array($storelocatorIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('datta_storelocator')->__('Please select store(s)'));
        } else {
            try {
                foreach ($storelocatorIds as $storelocatorId) {
                    $storelocator = Mage::getModel('datta_storelocator/store')->load($storelocatorId);
                    $storelocator->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('datta_storelocator')->__(
                        'Total of %d record(s) were successfully deleted', count($storelocatorIds)
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
        $storelocatorIds = $this->getRequest()->getParam('datta_storelocator');
        if(!is_array($storelocatorIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select store(s)'));
        } else {
            try {
                foreach ($storelocatorIds as $storelocatorId) {
                    $storelocator = Mage::getSingleton('datta_storelocator/store')
                        ->load($storelocatorId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setUpdateTime(time())
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($storelocatorIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    } 
    public function messageAction()
    {
        $data = Mage::getModel('datta_storelocator/store')->load($this->getRequest()->getParam('entity_id'));
        echo $data->getContent();
    } 
    /**
     * Initialize action   
     * Here, we set the breadcrumbs and the active menu
     * return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout()
        // Make the active menu match the menu config nodes (without 'children' inbetween)
            ->_setActiveMenu('datta/datta_storelocator')
            ->_title($this->__('Datta'))->_title($this->__('Store'))
            ->_addBreadcrumb($this->__('Datta'), $this->__('Datta'))
            ->_addBreadcrumb($this->__('Store'), $this->__('Store'));
        return $this;
    }
    /**
     * Check currently called action by permissions for current user
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('datta/datta_storelocator');
    }     
    /**
     * Export order grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'stores.csv';
        $grid    = $this->getLayout()->createBlock('datta_storelocator/adminhtml_store_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
}