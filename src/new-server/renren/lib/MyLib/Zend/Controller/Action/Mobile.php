<?php

/** @see Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

/**
 * Mobile Base Controller
 * user must login, identity not empty
 *
 * @package    MyLib_Zend_Controller
 * @subpackage Action
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/05/14    Huch
 */
class MyLib_Zend_Controller_Action_Mobile extends Zend_Controller_Action
{
    /**
     * base url of website
     * @var string
     */
    protected $_baseUrl;

    protected $_APP_ID;
    
    protected $_USER_ID;
    
    protected $_mixiMobileUrl = 'http://ma.mixi.net/';
    
    /**
     * user info
     * @var object (stdClass)
     */
    protected $_user;
    
    protected $_ua;

    /**
     * initialize basic data
     * @return void
     */
    public function initData()
    {        
        $this->_baseUrl = Zend_Registry::get('host');
        $this->_staticUrl = Zend_Registry::get('static');

        $this->_APP_ID = $this->_request->getParam('opensocial_app_id');
        
        if (empty($this->_APP_ID)) {
            $cfappid = $this->_request->getParam('cfappid');
            $this->_APP_ID = str_replace("?opensocial_app_id=", "", $cfappid);
        }
        
        $this->_USER_ID = $this->_request->getParam('opensocial_owner_id');
        
        $this->_ua = Zend_Registry::get('ua');
        
        $param = ($this->_ua == 1) ? '?guid=ON&url=' : '?url=';
        Zend_Registry::set('MIXI_APP_REQUEST_URL', $this->_mixiMobileUrl . $this->_APP_ID . '/' . $param);
        Zend_Registry::set('opensocial_app_id', $this->_APP_ID);
        
        $this->view->APP_ID = $this->_APP_ID;
        $this->view->ua = $this->_ua;
        
        require_once 'Bll/User.php';
        $this->_user = Bll_User::getPerson($this->_USER_ID);

        if (empty($this->_user)) {
            exit(0);
        }
    }

    /**
     * initialize object
     * override
     * @return void
     */
    public function init()
    {
        $this->initData();
        parent::init();
    }

    /**
     * initialize view render data
     * @return void
     */
    protected function renderData()
    {
        $this->view->baseUrl = Zend_Registry::get('host');
        $this->view->staticUrl = Zend_Registry::get('static');
        $this->view->hostUrl = Zend_Registry::get('host');
    }
    
    /**
     * Redirect to another URL
     * mixi app must request mixi for proxy
     *
     * @param string $url
     * @return void
     */
    protected function _redirect($url, array $options = array())
    {        
        if (!preg_match('|^[a-z]+://|', $url)) {
            $url = Zend_Registry::get('host') . $url;
        }
           
        $joinchar = (stripos($url,'?') === false) ? '?' : '&';
        $url = $url . $joinchar . "cfappid=";
           
        parent::_redirect($url, $options);

        /*
        $joinchar = (stripos($url,'?') === false) ? '?' : '&';
        parent::_redirect($url . $joinchar . 'opensocial_owner_id=' . $this->_USER_ID . '&opensocial_app_id=' . $this->_APP_ID, $options);*/
    }
    
    /**
     * pre-Render
     * called before parent::render method.
     * it can override
     * @return void
     */
    public function preRender()
    {
    }

    /**
     * Render a view
     * override
     * @see Zend_Controller_Action::render()
     * @param string|null $action Defaults to action registered in request object
     * @param string|null $name Response object named path segment to use; defaults to null
     * @param bool $noController  Defaults to false; i.e. use controller name as subdir in which to search for view script
     * @return void
     */
    public function render($action = null, $name = null, $noController = false)
    {
        $this->renderData();
        $this->preRender();
        parent::render($action, $name, $noController);
    }
    
    public function getParam($key, $default = null)
    {
        $value = $this->_request->getParam($key);
        
        if (null == $value) {
            return $default;
        }
      
        if ($this->_ua == 1) {
            if (!empty($value) and is_string($value)) {  
                $value = mb_convert_encoding($value, 'UTF-8', 'SJIS,SJIS-win,UTF-8');
            }
        }
        
        return $value;
    }
    
    public function getPost($key, $default = null)
    {
        $value = $this->_request->getPost($key);
        
        if (null == $value) {
            return $default;
        }
        
        if ($this->_ua == 1) {
            if (!empty($value) and is_string($value)) {
                $value = mb_convert_encoding($value, 'UTF-8', 'SJIS,SJIS-win');
            }
        }
        
        return $value;
    }
    
    public function checkFlashLite()
    {
        $flashlite = new MyLib_Mobile_Japan_FlashLite($this->_ua);
        return $flashlite->isValid();
    }

    /**
     * proxy for undefined methods
     * override
     * @param string $methodName
     * @param array $args
     */
    public function __call($methodName, $args)
    {
        $this->_forward('notfound', 'error', 'mobile');
        return;
    }
}