<?php

/**
 * error controller
 * init each error page
 *
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create    2008/08/01    HCH
 */
class ErrorController extends Zend_Controller_Action
{
    /**
     * record user is login
     *
     * @var boolean
     */
    private $_login = false;

    /**
     * user info
     * @var object (stdClass)
     */
    protected $_user;

    /**
     * init
     *  init the data
     */
    function init()
    {
        $this->view->staticUrl = Zend_Registry::get('static');
        $this->view->version = Zend_Registry::get('version');

    }


    /**
     * notfound Action
     *
     */
    public function notfoundAction()
    {
        $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
        $this->view->title = '404 Not Found｜LinNo ( リンノ )';
        $this->render();
    }

    /**
     * error Action
     *
     */
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER :
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION :
                // 404 error -- controller or action not found
                return $this->_forward('notfound');
                break;
            default :
                $exception = $errors->exception;
                if ($exception) {
                    $content = $exception->getMessage() . "\n" . $exception->getTraceAsString();

                    //write the error message to log
                    err_log($content);
                }
                
                break;
        }

        // Clear previous content
        $this->getResponse()->clearBody();
        $this->view->title = 'Error';
        $this->render();
    }

    /**
     * app paring error
     *
     */
    public function parkingAction()
    {
    	$this->render();
    }
    
    /**
     * magic function
     *   if call the function is undefined,then forward to not found
     *
     * @param string $methodName
     * @param array $args
     * @return void
     */
    function __call($methodName, $args)
    {
        return $this->_forward('notfound');
    }

}
