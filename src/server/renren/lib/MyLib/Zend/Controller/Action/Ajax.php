<?php

/** @see Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

/**
 * Ajax Base Controller
 * user must login, identity not empty
 *
 * @package    MyLib_Zend_Controller
 * @subpackage Action
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2008/07/18     Hulj
 */
class MyLib_Zend_Controller_Action_Ajax extends Zend_Controller_Action
{
   
	/**
     * base url of website
     * @var string
     */
    protected $_baseUrl;
    
    protected $_photoBasePath;
    
    /**
     * current user
     * contain id,displayName,thumbnailUrl,profileUrl
     *
     * @var array
     */
    protected $_user;
    
    /**
     * initialize basic data
     * @return void
     */
    public function initData()
    {
        $controller = $this->getFrontController();
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);
                
        $this->_baseUrl = $this->_request->getBaseUrl();
        $this->_photoBasePath = Zend_Registry::get('photoBasePath');
        
        
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
        	$id = $auth->getIdentity();
        }
        else {
            /*
        	$id = $_COOKIE['app_mixi_uid'];        	
        	$sig = $_COOKIE['app_mixi_sig'];
        	
        	if (!Bll_Secret::isTrueSecret($id,$sig)) {
        		echo 'false';
        		exit();
        	}*/
        	
        	$id = 14;
        	$auth->getStorage()->write($id);
        }
		//$id='mixi.jp:10541760b2804dfd26efc45fadb1f5b9f2f096e1a0412982';
        require_once 'Bll/User.php';
        $this->_user = Bll_User::getPerson($id);
        
        if (empty($this->_user)) {
            echo 'false';
            exit();
        }
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
     * default action
     */
    public function indexAction()
    {
        echo 'Ajax Index';
    }
    
    /**
     * proxy for undefined methods
     * override
     * @param string $methodName
     * @param array $args
     */
    public function __call($methodName, $args)
    {
        echo 'No This Method';
    }
}