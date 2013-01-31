<?php

/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';


class Hapyfish2_Application_Abstract
{
    /**
     * $_actionController - ActionController reference
     *
     * @var Zend_Controller_Action
     */
    protected $_actionController;
    
    /**
     * application id
     * @var string
     */
    protected $_appId;
    
    /**
     * application name
     * @var string
     */
    protected $_appName;
    
    /**
     * application owner id
     * @var string
     */
    protected $_userId;
    
    
    /**
     * Singleton instance
     *
     * Marked only as protected to allow extension of the class. To extend,
     * simply override {@link getInstance()}.
     *
     * @var Bll_Application
     */
    protected static $_instance = null;
        
    
    /**
     * __construct() -
     *
     * @param Zend_Controller_Action $actionController
     * @return void
     */
    public function __construct(Zend_Controller_Action $actionController)
    {
        $this->_actionController = $actionController;
        $this->_init();
    }
        
    /**
     * get singleton instance
     *
     * @return Bll_Application
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            throw new Exception('Application instance has not been created! Please use "newInstance" to create one.');
        }

        return self::$_instance;
    }
    
    /**
     * Get request object
     *
     * @return Zend_Controller_Request_Abstract $request
     */
    public function getRequest()
    {
        return $this->_actionController->getRequest();
    }
    
    /**
     * get application id
     *
     * @return string
     */
    public function getAppId()
    {
        return $this->_appId;
    }
    
    public function getUserId()
    {
        return $this->_userId;
    }
    
    protected function _init()
    {
        
    }
       
    /**
     * run() - main mothed
     * 
     * @return void
     */
    public function run()
    {      

    }
    
    /**
     * Redirect to another URL
     *
     * Proxies to {@link Zend_Controller_Action_Helper_Redirector::gotoUrl()}.
     *
     * @param string $url
     * @param array $options Options to be used when redirecting
     * @return void
     */
    public function redirect($url, array $options = array())
    {
        $redirector = $this->_actionController->getHelper('redirector');
        $redirector->gotoUrl($url, $options);
    }
    
    /**
     * Redirect to "/error/notfound"
     * 
     * @return void
     */
    public function redirect404()
    {
        $this->redirect('/error/notfound');
        exit;
    }

}