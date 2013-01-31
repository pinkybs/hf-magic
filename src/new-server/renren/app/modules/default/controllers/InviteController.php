<?php

class InviteController extends Zend_Controller_Action
{
    public function init()
    {
        $info = $this->vailid();
        if (!$info) {
            echo '<html><body><script type="text/javascript">window.top.location="http://apps.renren.com/'.APP_NAME.'/";</script></body></html>';
            exit;
        }

        $this->info = $info;
        $this->uid = $info['uid'];

        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
        $this->view->appId = APP_ID;
        $this->view->appKey = APP_KEY;
        $this->view->uid = $info['uid'];
        $this->view->platformUid = $info['puid'];
    }

    protected function vailid()
    {
    	$skey = $_COOKIE[PRODUCT_ID.'_skey'];
    	return Hapyfish2_Validate_UserCertify::checkKey($skey, APP_SECRET);
    }

    public function topAction()
    {
    	$this->render();
    }

	public function friendsAction()
    {
        $st = time();

        $onwerUid = Hapyfish2_Platform_Cache_User::getUser($this->uid);

        $invite_param= 'hf_invite=true&hf_inviter=' . $onwerUid['puid'] . '&hf_st=' . $st;
        $sg = md5($invite_param . APP_KEY . APP_SECRET);

        $this->view->params = $invite_param . '&hf_sg=' . $sg;
        $this->view->st = $st;
        $this->view->sg = $sg;
        $this->render();
    }

    public function sendAction()
    {
		$st = $this->_request->getParam('hf_st');
        $sg = $this->_request->getParam('hf_sg');
        $ids = $this->_request->getParam('ids');

        $onwerUid = Hapyfish2_Platform_Cache_User::getUser($this->uid);

        if ($st && $sg && $ids) {
            foreach($ids as $id) {
                Hapyfish2_Island_Bll_InviteLog::addInvite($onwerUid['puid'], $id, $st, $sg);
            }
        }

		$this->_redirect('/invite/top');
        exit;
    }

 }
