<?php

/** @see Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

/**
 * Admin Ajax Base Controller
 * admin user must login, identity not empty
 *
 * @package    MyLib_Zend_Controller
 * @subpackage Action
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/02/23     zhangxin
 */
class MyLib_Zend_Controller_Action_AdminAjax extends Zend_Controller_Action
{

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
        $controller = $this->getFrontController();
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);

        require_once 'Admin/Bll/Auth.php';
        $auth = Admin_Bll_Auth::getAuthInstance();
        if (!$auth->hasIdentity()) {
    		$this->_request->setDispatched(true);
            echo 'Authority Denied! Please Login First!';
            exit();
        }

        //get user info
        $this->_user = Admin_Bll_Auth::getIdentity();

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

/*
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
            if (2 != $isAllow) {
                $this->_request->setDispatched(true);
                echo 'Authority Denied!';
                exit();
            }
        }
*/

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