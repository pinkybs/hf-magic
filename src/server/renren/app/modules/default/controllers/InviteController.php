<?php

/**
 * island index controller
 *
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create      2010/01/20    Hulj
 */
class InviteController extends Zend_Controller_Action
{    
    public function init()
    {
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = Zend_Registry::get('static');
        $this->view->version = Zend_Registry::get('version');
        $this->view->hostUrl = Zend_Registry::get('host');
    }
    
    public function topAction()
    {
        $application = Bll_Application_Renren::getInstance();
        $this->view->app_id = $application->getAppId();
        $this->view->domain = $application->xn_params['domain'];
        $this->render();
    }
    
    public function friendsAction()
    {
        $application = Bll_Application_Renren::getInstance();
        $this->view->app_id = $application->getAppId();
        $this->view->domain = $application->xn_params['domain'];

        $actor = $application->getUserId();        
        $st = floor(microtime(true)*1000);
        $invite_param= 'hf_invite=true&hf_inviter=' . $actor . '&hf_st=' . $st;
        $sg = md5($invite_param . APP_KEY . APP_SECRET);

        $this->view->params = $invite_param . '&hf_sg=' . $sg;
        $this->view->st = $st;
        $this->view->sg = $sg;
        $this->render();
    }
    
    public function sendAction()
    {
        $application = Bll_Application_Renren::getInstance();
        $this->view->app_id = $application->getAppId();
        $this->view->domain = $application->xn_params['domain'];

        $st = $this->_request->getPost('hf_st');
        $sg = $this->_request->getPost('hf_sg');
        $ids = $this->_request->getPost('ids');
        
        $actor = $application->getUserId();
        
        if ($st && $sg && $ids) {
        	$newSg = $sg;
            foreach($ids as $id) {
                $sg .= $id;
                Bll_Island_Log::addInvite($actor, $id, $st, $sg);
                $sg = $newSg;
            }
        }
        
        $home = 'http://apps.' . $application->xn_params['domain'] . '/rrisland';
        echo '<xn:redirect url="' . $home . '" />';
        exit;
    }

    function preDispatch()
    {
        $application = Bll_Application_Renren::newInstance($this);
        
        $application->run();
    }
    

 }
