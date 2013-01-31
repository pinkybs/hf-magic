<?php

/** @see Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

/**
 * Default Base Controller
 * user must login, identity not empty
 *
 * @package    MyLib_Zend_Controller
 * @subpackage Action
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2008/07/18     Hulj
 */
class MyLib_Zend_Controller_Action_Default extends Zend_Controller_Action
{
    /**
     * base url of website
     * @var string
     */
    protected $_baseUrl;

    protected $_staticUrl;

    /**
     * current user
     * contain id,displayName,thumbnailUrl,profileUrl
     *
     * @var array
     */
    protected $_user;
    
    protected $_appId;

    /**
     * initialize basic data
     * @return void
     */
    public function initData()
    {
        $this->_baseUrl = $this->_request->getBaseUrl();
        $this->_staticUrl = Zend_Registry::get('static');
         
               
        $auth = Zend_Auth::getInstance();
        $id = 14;
        $auth->getStorage()->write($id);
        
        if ($auth->hasIdentity()) {
        	$id = $auth->getIdentity();
        }
        else {
        	$id = $_COOKIE['app_mixi_uid'];        	
        	$sig = $_COOKIE['app_mixi_sig'];
        	        	
        	if (!Bll_Secret::isTrueSecret($id,$sig)) {
        		$this->_redirect($this->_baseUrl . '/error/error');
        		return;

        	}      
            //$id=10541760;
        	$auth->getStorage()->write($id);
        }
        
        require_once 'Bll/User.php';
        $this->_user = Bll_User::getPerson($id);

        if (empty($this->_user)) {
        	$this->_redirect($this->_baseUrl . '/error/error');
        	return;
        }
        
        //get mixi_platform_api_url
        $this->view->mixi_platform_api_url = $_COOKIE['mixi_platform_api_url_' . $this->_request->getControllerName()];
        
        $this->getAppId();
    }
    
    function getAppId()
    {
        $topUrl = $_COOKIE['app_top_url_' . $this->_request->getControllerName()];
        preg_match('/id=(\d+)/', $topUrl, $matches);
        $this->_appId = $matches[1];
        $this->view->app_id = $this->_appId;
    }
    
    function checkTopAd()
    {
        /*
        require_once 'Dal/Ad/Ad.php';
        $dalAd = Dal_Ad_Ad::getDefaultInstance();
        $this->view->showTopAd = $dalAd->checkTopAd($this->_appId, $this->_user->getId());*/
    }
    
    /**
     * post-Initialize
     * called after parent::init method execution.
     * it can override
     * @return void
     */
    public function postInit()
    {
        
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
        $this->postInit();
    }

    /**
     * initialize view render data
     * @return void
     */
    protected function renderData()
    {
        $this->view->baseUrl = $this->_baseUrl;
        $this->view->staticUrl = Zend_Registry::get('static');
        $this->view->photoUrl = Zend_Registry::get('photo');
        $this->view->version = Zend_Registry::get('version');
        $this->view->hostUrl = Zend_Registry::get('host');
        $this->view->_user = $this->_user;        
        $this->checkTopAd();
        
        require_once 'MyLib/Browser.php';
        $browser = MyLib_Browser::getBrowser();
        $this->view->IE6 = 'Internet Explorer 6.0' == $browser ? true : false;
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

    /**
     * proxy for undefined methods
     * override
     * @param string $methodName
     * @param array $args
     */
    public function __call($methodName, $args)
    {
        $this->_forward('notfound', 'error', 'default');
        return;
    }
}