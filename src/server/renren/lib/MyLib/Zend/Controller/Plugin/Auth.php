<?php

/** @see Zend_Controller_Plugin_Abstract */
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * Implement the privilege controller.
 *
 * @package    MyLib_Controller
 * @subpackage MyLib_Controller_Plugin
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create     2009/05/14     Huch
 */
class MyLib_Zend_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
    /**
     * Track user privileges.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $module = $request->getModuleName();
        $front  = Zend_Controller_Front::getInstance();

        // if module name is null, set default
        if ($module == null) {
            //$module = 'default';
            $module = $front->getDefaultModule();
        }
                
    }
}