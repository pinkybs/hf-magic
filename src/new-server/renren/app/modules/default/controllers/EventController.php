<?php

class EventController extends Zend_Controller_Action
{
    protected $uid;
    protected $info;

    /**
     * initialize basic data
     * @return void
     */
    public function init()
    {
        if (APP_STATUS == 0) {
            $ip = $this->getClientIP();
            if ($ip != '27.115.48.202' && $ip != '122.147.63.223') {
                $result = array('status' => '-1', 'content' => '停机维护中');
                $this->echoResult($result);
            }
        }
        $info = $this->vailid();
        if (! $info) {
            $result = array('status' => '-1', 'content' => 'serverWord_101');
            $this->echoResult($result);
        }
        $this->info = $info;
        $this->uid = $info['uid'];
        $data = array('uid' => $info['uid'], 'puid' => $info['puid'],
        'session_key' => $info['session_key']);
        $context = Hapyfish2_Util_Context::getDefaultInstance();
        $context->setData($data);
        Hapyfish2_Magic_Bll_UserResult::setUser($info['uid']);
        $controller = $this->getFrontController();
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);
    }

    protected function getClientIP()
    {
        $ip = false;
        if (! empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        else if (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip) {
                array_unshift($ips, $ip);
                $ip = false;
            }
            for ($i = 0, $n = count($ips); $i < $n; $i ++) {
                if (! eregi("^(10|172\.16|192\.168)\.", $ips[$i])) {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    protected function vailid()
    {
        $skey = $_COOKIE[PRODUCT_ID . '_skey'];
        return Hapyfish2_Validate_UserCertify::checkKey($skey, APP_SECRET);
    }

    protected function checkEcode($params = array())
    {
        if ($this->info['rnd'] > 0) {
            $rnd = $this->info['rnd'];
            $uid = $this->uid;
            $ts = $this->_request->getParam('tss');
            $authid = $this->_request->getParam('authid');
            $ok = true;
            if (empty($authid) || empty($ts)) {
                $ok = false;
            }
            if ($ok) {
                $ok = Hapyfish2_Magic_Bll_Ecode::check($rnd, $uid, $ts, $authid, $params);
            }
            if (! $ok) {
                //Hapyfish2_Magic_Bll_Block::add($uid, 1, 2);
                info_log($uid, 'ecode-err');
                $result = array('status' => '-1', 'content' => 'serverWord_101');
                setcookie(PRODUCT_ID . '_skey', '', 0, '/', str_replace('http://', '.', HOST));
                $this->echoResult($result);
            }
        }
    }

    protected function echoResult($data)
    {
        header("Cache-Control: no-store, no-cache, must-revalidate");
        echo json_encode($data);
        exit();
    }

    protected function echoResultAndLog($data, $logInfo)
    {
        header("Cache-Control: no-store, no-cache, must-revalidate");
        echo json_encode($data);
        /*
    	if ($logInfo != null) {
			//report log
			$logInfo['openid'] = $this->info['openid'];
			$logger = Qzone_Log::getInstance();
			//$logger->setLogFile(LOG_DIR . '/report.log');
			$logger->report($this->uid, $logInfo);
    	}
		*/
        exit();
    }

    //收集活动
    public function collectAction()
    {
        $uid = $this->uid;
        $exchangeId = (int)$this->_request->getParam('id');

        //get lock
        $lockkey = 'm:u:e:lock:collect:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
        $ok = $lock->lock($lockkey);
        if (!$ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('click_too_fast'));
        }

        $result = Hapyfish2_Magic_Event_Bll_Collection::exchange('201112Xmas', $uid, $exchangeId);

        //release lock
        $lock->unlock($lockkey);
        $this->echoResult($result);
    }

	public function getsystimeAction()
	{
		$result = array ('systime' => time());
		$this->echoResult($result);
	}

 }