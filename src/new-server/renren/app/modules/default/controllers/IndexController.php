<?php

/**
 * island index controller
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2010/10    lijun.hu
 */
class IndexController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
        $this->view->appId = APP_ID;
        $this->view->appKey = APP_KEY;
    }

    protected function getClientIP()
    {
    	$ip = false;
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode (', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip) {
				array_unshift($ips, $ip);
				$ip = false;
			}
			for ($i = 0, $n = count($ips); $i < $n; $i++) {
				if (!eregi ("^(10|172\.16|192\.168)\.", $ips[$i])) {
					$ip = $ips[$i];
					break;
				}
			}
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
    }

    public function indexAction()
    {
        //info_log(json_encode($_REQUEST), 'from_renren');
    	if (APP_STATUS == 0) {
    		$ip = $this->getClientIP();
    		if ($ip != '116.232.78.124' && $ip != '183.15.146.221') {
    			header('Location: ' . STATIC_HOST . '/maintance/index.html?v=2011010601');
    			exit;
    		}
    	}

    	try {
    		$application = Hapyfish2_Application_Renren::newInstance($this);
        	$application->run();
    	} catch (Exception $e) {
    		err_log($e->getMessage());
    		//echo '加载数据出错，请重新进入。';
    		echo '<div style="text-align:center;margin-top:30px;"><img src="' . STATIC_HOST . '/maintance/images/problem1.gif" alt="加载数据出错，请重新进入" /></div>';
    		exit;
    	}

        $uid = $application->getUserId();
        $isnew = $application->isNewUser();
        $platformUid = $application->getPlatformUid();

        if ($isnew) {
        	$ok = Hapyfish2_Magic_Bll_User::joinUser($uid);
        	if (!$ok) {
    			echo '创建初始化数据出错，请重新进入。';
    			exit;
        	}

        	//is invited by sb
        	$hf_params = $this->_request->getParam('hf_params');
            $params = array();
            parse_str(base64_decode($hf_params), $params);
            $hfInvitesig = $params['hf_invitesig'];
            //$rrSender = $this->_request->getParam('sender');
	        if ($hfInvitesig) {
	        	$hfInvitesig = base64_decode($hfInvitesig);
	            $time = time();
	            $inviteTm = substr($hfInvitesig, 0, 10);
				$inviterUid = substr($hfInvitesig, 10);
				//time valid 7 days
				if ($time>=$inviteTm && $time-$inviteTm<3600*24*7) {
                    if ((int)$inviterUid) {
                        $rowUser = Hapyfish2_Platform_Bll_User::getUser($uid);
                        //if ($rowUser['puid'] == $rrSender) {
                        if ($rowUser) {
                            Hapyfish2_Magic_Bll_Invite::add($inviterUid, $uid);
                            info_log($inviterUid.' invite '.$uid.' done ', 'invite_done');
                        }
                    }
				}
	        }
        } else {
        	$isAppUser = Hapyfish2_Magic_Cache_User::isAppUser($uid);
        	if (!$isAppUser) {
        		$ok = Hapyfish2_Magic_Bll_User::joinUser($uid);
        	    if (!$ok) {
    				echo '创建初始化数据出错，请重新进入。';
    				exit;
        		}
        	} else {
        		$status = Hapyfish2_Platform_Cache_User::getStatus($uid);
        		if ($status > 0) {
        			if ($status == 1) {
        				$msg = '该帐号(魔法教室号:' . $uid . ')因使用外挂或违规已被封禁，有问题请联系管理员QQ:1471558464';
        			} else if ($status == 2) {
        				$msg = '该帐号(魔法教室号:' . $uid . ')因数据出现异常被暂停使用，有问题请联系管理员QQ:1471558464';
        			} else if ($status == 3)  {
        				$msg = '该帐号(魔法教室号:' . $uid . ')因利用bug被暂停使用[待处理后恢复]，有问题请联系管理员QQ:1471558464';
        			} else {
        				$msg = '该帐号(魔法教室号:' . $uid . ')暂时不能访问，有问题请联系管理员QQ:1471558464';
        			}

        			echo $msg;
        			exit;
        		}
        	}
        }

        $avatarInfo = Hapyfish2_Magic_HFC_User::getUserAvatar($uid);
        if ($avatarInfo['avatar_edit'] == 0) {
        	$this->view->piantou = STATIC_HOST . '/swf/piantou.swf?v=2011090101';
        	$this->view->createUrl = HOST . '/api/initavatar';
        	$this->view->createModule = STATIC_HOST . '/swf/createPlayer.swf?v=2011122301';
        } else {
        	$this->view->piantou = '';
        	$this->view->createUrl = '';
        	$this->view->createModule = '';
        }

        $this->view->uid = $uid;
        $this->view->platformUid = $platformUid;
        $this->view->showpay = true;
        $this->view->newuser = $isnew ? 1 : 0;
        $this->view->inviteSig = base64_encode(time().$uid);
        $this->render();
    }


    public function testAction()
    {
        echo 'hello magic-renren';
        exit;
    }
}