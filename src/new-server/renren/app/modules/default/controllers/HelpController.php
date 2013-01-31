<?php

/**
 * island help controller
 *
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create      2010/10/12    Liz
 */
class HelpController extends Zend_Controller_Action
{
    protected $uid;

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

        $notice = Hapyfish2_Island_Cache_BasicInfo::getNoticeList();
        if (empty($notice)) {
        	$this->view->showNotice = false;
        } else {
        	$this->view->showNotice = true;
			$this->view->mainNotice = $notice['main'];
			$this->view->subNotice = $notice['sub'];
			$this->view->picNotice = $notice['pic'];
        }
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

    public function help2Action()
    {
        $this->render();
    }
    public function help3Action()
    {
        $this->render();
    }
    public function help4Action()
    {
        $this->render();
    }
    public function help5Action()
    {
        $this->render();
    }
    public function help6Action()
    {
        $this->render();
    }
    public function help7Action()
    {
        $this->render();
    }
    public function help8Action()
    {
        $this->render();
    }
    public function help9Action()
    {
        $this->render();
    }
 }
