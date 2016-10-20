<?php
class Mage_Adminhtml_UrlrewriteController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Instantiate urlrewrite, post and category
     *
     * @return Mage_Adminhtml_UrlrewriteController
     */
    protected function _initRegistry()
    {
        $this->_title($this->__('Rewrite Rules'));

        // initialize urlrewrite, post and category models
        Mage::register('current_urlrewrite', Mage::getSingleton('core/factory')->getUrlRewriteInstance()
            ->load($this->getRequest()->getParam('id', 0))
        );
        $postId  = $this->getRequest()->getParam('post', 0);
        $categoryId = $this->getRequest()->getParam('category', 0);
        if (Mage::registry('current_urlrewrite')->getId()) {
            $postId  = Mage::registry('current_urlrewrite')->getPostId();
            $categoryId = Mage::registry('current_urlrewrite')->getCategoryId();
        }

        Mage::register('current_post', Mage::getModel('catalog/post')->load($postId));
        Mage::register('current_category', Mage::getModel('catalog/category')->load($categoryId));

        return $this;
    }

    /**
     * Show urlrewrites index page
     *
     */
    public function indexAction()
    {
        $this->_initRegistry();
        $this->loadLayout();
        $this->_setActiveMenu('catalog/urlrewrite');
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/urlrewrite')
        );
        $this->renderLayout();
    }

    /**
     * Show urlrewrite edit/create page
     *
     */
    public function editAction()
    {
        $this->_initRegistry();

        $this->_title($this->__('URL Rewrite'));

        $this->loadLayout();
        $this->_setActiveMenu('catalog/urlrewrite');
        $this->_addContent($this->getLayout()->createBlock('adminhtml/urlrewrite_edit'));
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    /**
     * Ajax posts grid action
     *
     */
    public function postGridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/urlrewrite_post_grid')->toHtml());
    }

    /**
     * Ajax categories tree loader action
     *
     */
    public function categoriesJsonAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $this->getResponse()->setBody(Mage::getBlockSingleton('adminhtml/urlrewrite_category_tree')
            ->getTreeArray($id, true, 1)
        );
    }

    /**
     * Urlrewrite save action
     *
     */
    public function saveAction()
    {
        $this->_initRegistry();

        if ($data = $this->getRequest()->getPost()) {
            $session = Mage::getSingleton('adminhtml/session');
            try {
                // set basic urlrewrite data
                $model = Mage::registry('current_urlrewrite');

                // Validate request path
                $requestPath = $this->getRequest()->getParam('request_path');
                Mage::helper('core/url_rewrite')->validateRequestPath($requestPath);

                // Proceed and save request
                $model->setIdPath($this->getRequest()->getParam('id_path'))
                    ->setTargetPath($this->getRequest()->getParam('target_path'))
                    ->setOptions($this->getRequest()->getParam('options'))
                    ->setDescription($this->getRequest()->getParam('description'))
                    ->setRequestPath($requestPath);

                if (!$model->getId()) {
                    $model->setIsSystem(0);
                }
               

                // override urlrewrite data, basing on current registry combination
                $category = Mage::registry('current_category')->getId() ? Mage::registry('current_category') : null;
                if ($category) {
                    $model->setCategoryId($category->getId());
                }
                $post  = Mage::registry('current_post')->getId() ? Mage::registry('current_post') : null;
                if ($post) {
                    $model->setPostId($post->getId());
                }
                if ($post || $category) {
                    $catalogUrlModel = Mage::getSingleton('catalog/url');
                    $idPath = $catalogUrlModel->generatePath('id', $post, $category);

                    // if redirect specified try to find friendly URL
                    $found = false;
                    if (in_array($model->getOptions(), array('R', 'RP'))) {
                        $rewrite = Mage::getResourceModel('catalog/url')
                            ->getRewriteByIdPath($idPath, $model->getStoreId());
                        if (!$rewrite) {
                            $exceptionTxt = 'Chosen post does not associated with the chosen store or category.';
                            Mage::throwException($exceptionTxt);
                        }
                        if($rewrite->getId() && $rewrite->getId() != $model->getId()) {
                            $model->setIdPath($idPath);
                            $model->setTargetPath($rewrite->getRequestPath());
                            $found = true;
                        }
                    }

                    if (!$found) {
                        $model->setIdPath($idPath);
                        $model->setTargetPath($catalogUrlModel->generatePath('target', $post, $category));
                    }
                }

                // save and redirect
                $model->save();
                $session->addSuccess(Mage::helper('adminhtml')->__('The URL Rewrite has been saved.'));
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage())
                    ->setUrlrewriteData($data);
            } catch (Exception $e) {
                $session->addException($e, Mage::helper('adminhtml')->__('An error occurred while saving URL Rewrite.'))
                    ->setUrlrewriteData($data);
                // return intentionally omitted
            }
        }
        $this->_redirectReferer();
    }

    /**
     * Urlrewrite delete action
     *
     */
    public function deleteAction()
    {
        $this->_initRegistry();

        if (Mage::registry('current_urlrewrite')->getId()) {
            try {
                Mage::registry('current_urlrewrite')->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('The URL Rewrite has been deleted.')
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')
                    ->addException($e, Mage::helper('adminhtml')->__('An error occurred while deleting URL Rewrite.'));
                $this->_redirect('*/*/edit/', array('id'=>Mage::registry('current_urlrewrite')->getId()));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Check whether this contoller is allowed in admin permissions
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/urlrewrite');
    }
}
