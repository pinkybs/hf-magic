<?php

class ConsumelogsController extends Zend_Controller_Action
{
    protected $uid;

    protected function vailid()
    {
    	$skey = $_COOKIE[PRODUCT_ID.'_skey'];
    	return Hapyfish2_Validate_UserCertify::checkKey($skey, APP_SECRET);
    }

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

    public function coinAction()
    {
		$uid = $this->uid;
		$user = Hapyfish2_Platform_Bll_User::getUser($uid);
		$user['face'] = $user['figureurl'];
		$user['gold'] = Hapyfish2_Island_HFC_User::getUserGold($uid);
		$time = time();
		$year = date('Y', $time);
		$month = (int)date('n', $time);

    	$logs = Hapyfish2_Island_Bll_ConsumeLog::getCoin($uid, $year, $month, 50);
    	if (!$logs) {
    		$count = 0;
    		$logs = '[]';
    	} else {
    		$count = count($logs);
    		$logs = json_encode($logs);
    	}
    	$pageSize = 25;
    	$this->view->user = $user;
    	$this->view->date = $year . '年' . $month . '月';
		$this->view->logs = $logs;
        $this->view->count = $count;
        $this->view->pageSize = 25;
        $this->view->pageNum = ceil($count/$pageSize);
        $this->render();
    }

    public function goldAction()
    {
		$uid = $this->uid;
		$user = Hapyfish2_Platform_Bll_User::getUser($uid);
		$user['face'] = $user['figureurl'];
		$user['gold'] = Hapyfish2_Island_HFC_User::getUserGold($uid);
		$time = time();
		$year = date('Y', $time);
		$month = (int)date('n', $time);

    	$logs = Hapyfish2_Island_Bll_ConsumeLog::getGold($uid, $year, $month, 50);
    	if (!$logs) {
    		$count = 0;
    		$logs = '[]';
    	} else {
    		$count = count($logs);
    		$logs = json_encode($logs);
    	}
    	$pageSize = 25;
    	$this->view->user = $user;
    	$this->view->date = $year . '年' . $month . '月';
		$this->view->logs = $logs;
        $this->view->count = $count;
        $this->view->pageSize = 25;
        $this->view->pageNum = ceil($count/$pageSize);
        $this->render();
    }

    function __call($methodName, $args)
    {
        echo '400';
        exit;
    }

}