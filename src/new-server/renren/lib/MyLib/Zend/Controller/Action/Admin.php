<?php

/** @see Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

/**
 * Admin Base Controller
 * admin user must login, identity not empty
 *
 * @package    MyLib_Zend_Controller
 * @subpackage Action
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/02/19     zhangxin
 */
class MyLib_Zend_Controller_Action_Admin extends Zend_Controller_Action
{
    /**
     * base url of website
     * @var string
     */
    protected $_baseUrl;

    /**
     * flag is current forward setted
     * @var boolean
     */
    protected $_isSetForward = false;

    /**
     * current user
     * contain uid,uuid,name,email
     *
     * @var array
     */
    protected $_user;

    /**
     * super user flag
     * if user is super user
     *
     * @var boolean
     */
    protected $_isSuperUser;

    /**
     * editor user flag
     * if user is editor user
     *
     * @var boolean
     */
    protected $_isEditor;

    /**
     * viewer user flag
     * if user is viewer user
     *
     * @var boolean
     */
    protected $_isViewer;

    /**
     * watcher user flag
     * if user is watcher user
     *
     * @var boolean
     */
    protected $_isWatcher;

    /**
     * initialize basic data
     * @return void
     */
    public function initData()
    {
        $this->_baseUrl = $this->_request->getBaseUrl();

        require_once 'Admin/Bll/Auth.php';
        $auth = Admin_Bll_Auth::getAuthInstance();
        if (!$auth->hasIdentity()) {
    		$this->_redirect($this->_baseUrl . '/auth/login');
    		return;
        }

        //get user
        $this->_user = Admin_Bll_Auth::getIdentity();
        if (empty($this->_user)) {
            $this->_isSetForward = true;
        	$this->_forward('notfound', 'Error', 'admin');
        	return;
        }

        //get user roles
        $this->_isSuperUser = false;
        $this->_isViewer = false;
        $this->_isWatcher = false;
        $userRoles = Admin_Bll_Auth::getRoles();
        foreach ($userRoles as $role) {
            if ('superUser' == $role['role_name']) {
                $this->_isSuperUser = true;
                break;
            } else if ('viewer' == $role['role_name']) {
                $this->_isViewer = true;
                break;
            } else if ('watcher' == $role['role_name']) {
                $this->_isWatcher = true;
                break;
            } else if ('editor' == $role['role_name']) {
                $this->_isEditor = true;
            }
        }

        //is not SupperUser
        if (!$this->_isSuperUser) {
            //check user roles and allowed resource
            require_once 'Admin/Bll/AccessRoleChecker.php';
            require_once 'Admin/Bll/Role.php';
            $sysRoles = Admin_Bll_Role::getSysRole();
            $sysResource = Admin_Bll_Role::getSysResource();
            $sysRel = Admin_Bll_Role::getSysRoleResource();
            $roleChecker = new Admin_Bll_AccessRoleChecker($sysRoles, $sysResource, $sysRel);

            //user roles is allowed
            $isAllow = false;
            foreach ($userRoles as $role) {
                $visitedRes = '/' . $this->_request->getControllerName() . '/' . $this->_request->getActionName();
                $isAllow = $roleChecker->checkAllowed($role['role_name'], strtolower($visitedRes));
                if ($isAllow == 2) {
                    break;
                }
            }

            //not allow
            if (1 == $isAllow) {
                $this->_isSetForward = true;
                return $this->_forward('noauthority', 'error', 'admin', array('message' => 'Sorry,You Have No Permission To Visit This Page!!'));
            }
            //not found
            else if (0 == $isAllow) {
                return $this->_forward('notfound', 'error', 'admin');
            }
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
    final function init()
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
        $this->view->version = Zend_Registry::get('version');
        $this->view->hostUrl = Zend_Registry::get('host');
        $this->view->photoUrl = Zend_Registry::get('photo');
        $this->view->adminUser = $this->_user;

        $this->view->isSuperUser = $this->_isSuperUser;
        $this->view->isViewer = $this->_isViewer;
        $this->view->isWatcher = $this->_isWatcher;
        $this->view->isEditor = $this->_isEditor;
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
     */
    public function __call($methodName, $args)
    {
        if ($this->_isSetForward) {
            return $this->_forward('noauthority', 'error', 'admin');
        }
        else {
            return $this->_forward('notfound', 'error', 'admin');
        }
    }
}