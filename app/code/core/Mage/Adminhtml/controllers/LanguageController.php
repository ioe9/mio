<?php
class Mage_Adminhtml_LanguageController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Retrieve session model
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * Init actions
     *
     * @return Mage_Adminhtml_Cms_SlideController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('cms/slide')
            ->_addBreadcrumb(Mage::helper('cms')->__('CMS'), Mage::helper('cms')->__('CMS'))
            ->_addBreadcrumb(Mage::helper('cms')->__('Slides'), Mage::helper('cms')->__('Slides'))
        ;
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_title($this->__('CMS'))->_title($this->__('Slides'));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Create new CMS slide
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit CMS slide
     */
    public function editAction()
    {
        $this->_title($this->__('CMS'))->_title($this->__('Slides'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('slide_id');
        $model = Mage::getModel('cms/slide');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('This slide no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Slide'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in slides
        Mage::register('cms_slide', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb($id ? Mage::helper('cms')->__('Edit Slide') : Mage::helper('cms')->__('New Slide'), $id ? Mage::helper('cms')->__('Edit Slide') : Mage::helper('cms')->__('New Slide'))
            ->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {

            $id = $this->getRequest()->getParam('slide_id');
            $model = Mage::getModel('cms/slide')->load($id);
            if (!$model->getId() && $id) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('This slide no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }

            // init model and set data

            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('The slide has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('slide_id' => $model->getId()));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('slide_id' => $this->getRequest()->getParam('slide_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('slide_id')) {
            $title = "";
            try {
                // init model and delete
                $model = Mage::getModel('cms/slide');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('The slide has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('slide_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('Unable to find a slide to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/slide');
    }
}
