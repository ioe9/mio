<?php
class Mage_Core_Model_Url_Rewrite extends Mage_Core_Model_Abstract implements Mage_Core_Model_Url_Rewrite_Interface
{
    const TYPE_CATEGORY = 1;
    const TYPE_PRODUCT  = 2;
    const TYPE_CUSTOM   = 3;
    const REWRITE_REQUEST_PATH_ALIAS = 'rewrite_request_path';

    /**
     * Cache tag for clear cache in after save and after delete
     *
     * @var mixed | array | string | boolean
     */
    protected $_cacheTag = false;

    protected function _construct()
    {
        $this->_init('core/url_rewrite');
    }

    /**
     * Clean cache for front-end menu
     *
     * @return  Mage_Core_Model_Url_Rewrite
     */
    protected function _afterSave()
    {
        if ($this->hasCategoryId()) {
            $this->_cacheTag = array(Mage_Catalog_Model_Category::CACHE_TAG, Mage_Core_Model_Store_Group::CACHE_TAG);
        }

        parent::_afterSave();

        return $this;
    }

    /**
     * Load rewrite information for request
     * If $path is array - we must load possible records and choose one matching earlier record in array
     *
     * @param   mixed $path
     * @return  Mage_Core_Model_Url_Rewrite
     */
    public function loadByRequestPath($path)
    {
        $this->setId(null);
        $this->_getResource()->loadByRequestPath($this, $path);
        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;
        return $this;
    }

    public function loadByIdPath($path)
    {
        $this->setId(null)->load($path, 'id_path');
        return $this;
    }


    public function hasOption($key)
    {
        $optArr = explode(',', $this->getOptions());

        return array_search($key, $optArr) !== false;
    }

    public function addTag($tags)
    {
        $curTags = $this->getTags();

        $addTags = is_array($tags) ? $tags : explode(',', $tags);

        foreach ($addTags as $k=>$t) {
            if (!is_numeric($k)) {
                $t = $k.'='.$t;
            }
            if (!in_array($t, $curTags)) {
                $curTags[] = $t;
            }
        }

        $this->setTags($curTags);

        return $this;
    }

    public function removeTag($tags)
    {
        $curTags = $this->getTags();

        $removeTags = is_array($tags) ? $tags : explode(',', $tags);

        foreach ($removeTags as $t) {
            if (!is_numeric($k)) {
                $t = $k.'='.$t;
            }
            if ($key = array_search($t, $curTags)) {
                unset($curTags[$key]);
            }
        }

        $this->setTags(',', $curTags);

        return $this;
    }

    /**
     * Implement logic of custom rewrites
     *
     * @param   Varien_Controller_Request_Http $request
     * @param   Varien_Controller_Response_Http $response
     * @return  Mage_Core_Model_Url
     * @deprecated since 1.7.0.2. Refactored and moved to Mage_Core_Controller_Request_Rewrite
     */
    public function rewrite(Varien_Controller_Request_Http $request=null, Varien_Controller_Response_Http $response=null)
    {
        if (!Mage::isInstalled()) {
            return false;
        }
        if (is_null($request)) {
            $request = Mage::app()->getFrontController()->getRequest();
        }
        if (is_null($response)) {
            $response = Mage::app()->getFrontController()->getResponse();
        }
      
        /**
         * We have two cases of incoming paths - with and without slashes at the end ("/somepath/" and "/somepath").
         * Each of them matches two url rewrite request paths - with and without slashes at the end ("/somepath/" and "/somepath").
         * Choose any matched rewrite, but in priority order that depends on same presence of slash and query params.
         */
        $requestCases = array();
        $pathInfo = $request->getPathInfo();
        $origSlash = (substr($pathInfo, -1) == '/') ? '/' : '';
        $requestPath = trim($pathInfo, '/');

        // If there were final slash - add nothing to less priority paths. And vice versa.
        $altSlash = $origSlash ? '' : '/';

        $queryString = $this->_getQueryString(); // Query params in request, matching "path + query" has more priority
        if ($queryString) {
            $requestCases[] = $requestPath . $origSlash . '?' . $queryString;
            $requestCases[] = $requestPath . $altSlash . '?' . $queryString;
        }
        $requestCases[] = $requestPath . $origSlash;
        $requestCases[] = $requestPath . $altSlash;

        $this->loadByRequestPath($requestCases);

        /**
         * Try to find rewrite by request path at first, if no luck - try to find by id_path
         */
        if (!$this->getId() && isset($_GET['___from_store'])) {
            try {
                $fromStoreId = Mage::app()->getStore($_GET['___from_store'])->getId();
            }
            catch (Exception $e) {
                return false;
            }

            $this->setStoreId($fromStoreId)->loadByRequestPath($requestCases);
            if (!$this->getId()) {
                return false;
            }
            $currentStore = Mage::app()->getStore();
            $this->setStoreId($currentStore->getId())->loadByIdPath($this->getIdPath());

            Mage::app()->getCookie()->set(Mage_Core_Model_Store::COOKIE_NAME, $currentStore->getCode(), true);
            $targetUrl = $request->getBaseUrl(). '/' . $this->getRequestPath();

            $this->_sendRedirectHeaders($targetUrl, true);
        }

        if (!$this->getId()) {
            return false;
        }


        $request->setAlias(self::REWRITE_REQUEST_PATH_ALIAS, $this->getRequestPath());
        $external = substr($this->getTargetPath(), 0, 6);
        $isPermanentRedirectOption = $this->hasOption('RP');
        if ($external === 'http:/' || $external === 'https:') {

            Mage::app()->getCookie()->set(Mage_Core_Model_Store::COOKIE_NAME, true);

            $this->_sendRedirectHeaders($this->getTargetPath(), $isPermanentRedirectOption);
        } else {
            $targetUrl = $request->getBaseUrl(). '/' . $this->getTargetPath();
        }
        $isRedirectOption = $this->hasOption('R');
        if ($isRedirectOption || $isPermanentRedirectOption) {
            if (Mage::getStoreConfig('web/url/use_store') && $storeCode = Mage::app()->getStore()->getCode()) {
                $targetUrl = $request->getBaseUrl(). '/' . $storeCode . '/' .$this->getTargetPath();
            }

            $this->_sendRedirectHeaders($targetUrl, $isPermanentRedirectOption);
        }

        if (Mage::getStoreConfig('web/url/use_store') && $storeCode = Mage::app()->getStore()->getCode()) {
                $targetUrl = $request->getBaseUrl(). '/' . $storeCode . '/' .$this->getTargetPath();
            }

        $queryString = $this->_getQueryString();
        if ($queryString) {
            $targetUrl .= '?'.$queryString;
        }

        $request->setRequestUri($targetUrl);
        $request->setPathInfo($this->getTargetPath());

        return true;
    }

    /**
     * Prepare and return QUERY_STRING
     *
     * @return bool|string
     * @deprecated since 1.7.0.2. Refactored and moved to Mage_Core_Controller_Request_Rewrite
     */
    protected function _getQueryString()
    {
        if (!empty($_SERVER['QUERY_STRING'])) {
            $queryParams = array();
            parse_str($_SERVER['QUERY_STRING'], $queryParams);
            $hasChanges = false;
            foreach ($queryParams as $key=>$value) {
                if (substr($key, 0, 3) === '___') {
                    unset($queryParams[$key]);
                    $hasChanges = true;
                }
            }
            if ($hasChanges) {
                return http_build_query($queryParams);
            }
            else {
                return $_SERVER['QUERY_STRING'];
            }
        }
        return false;
    }

    /**
     * Add location header and disable browser page caching
     *
     * @param string $url
     * @param bool $isPermanent
     * @deprecated since 1.7.0.2. Refactored and moved to Mage_Core_Controller_Request_Rewrite
     */
    protected function _sendRedirectHeaders($url, $isPermanent = false)
    {
        if ($isPermanent) {
            header('HTTP/1.1 301 Moved Permanently');
        }

        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Pragma: no-cache');
        header('Location: ' . $url);
        exit;
    }
}
