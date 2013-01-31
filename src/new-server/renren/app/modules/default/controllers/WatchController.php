<?php

class WatchController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
    }

	function check()
	{
		$uid = $this->_request->getParam('uid');
		if (empty($uid)) {
			echo '1001';
			exit;
		}

		/*
		$t = $this->_request->getParam('t');
		if (empty($t)) {
			echo '1001';
			exit;
		}

		$sig = $this->_request->getParam('sig');
		if (empty($t)) {
			echo '1001';
			exit;
		}

		$validSig = md5($uid . $t . APP_KEY);
		if ($sig != $validSig) {
			echo '1002';
			exit;
		}

		$now = time();
		if (abs($now - $t) > 1800) {
			echo '1003';
			exit;
		}*/

		$isAppUser = Hapyfish2_Magic_Cache_User::isAppUser($uid);
		if (!$isAppUser) {
			echo 'uid error, not app user';
			exit;
		}

		return $uid;
	}

    /**
     * index Action
     *
     */
    public function indexAction()
    {
    	$uid = $this->check();
    	$user = Hapyfish2_Platform_Bll_User::getUser($uid);
        $puid = $user['puid'];
        $t = time();
        $rnd = mt_rand(1, ECODE_NUM);
        //simulate
        $session_key = md5($t);

        //$sig = md5($uid . $puid . $session_key . $t . $rnd . APP_SECRET);
        //$skey = $uid . '.' . $puid . '.' . base64_encode($session_key) . '.' . $t . '.' . $rnd . '.' . $sig;

        $skey = Hapyfish2_Validate_UserCertify::generateKey($uid, $puid, $session_key, $t, $rnd, APP_SECRET);
        setcookie(PRODUCT_ID.'_skey', $skey , 0, '/', str_replace('http://', '.', HOST));

        $avatarInfo = Hapyfish2_Magic_HFC_User::getUserAvatar($uid);
        if ($avatarInfo['avatar_edit'] == 0) {
        	$this->view->piantou = STATIC_HOST . '/swf/piantou.swf?v=2011090101';
        	$this->view->createUrl = HOST . '/api/initavatar';
        	$this->view->createModule = STATIC_HOST . '/swf/createPlayer.swf?v=2011090101';
        } else {
        	$this->view->piantou = '';
        	$this->view->createUrl = '';
        	$this->view->createModule = '';
        }

        $this->view->uid = $uid;
        $this->view->puid = $puid;
        $this->view->newuser = 0;
        $this->view->tipsStr = '变化咒可以对你和好友使用,但对学生无效||在魔法小屋中多摆放一些家具,会增加魔法值上限||魔法上限越高,回复的点数也越多||多和村民们聊聊,可以获得很多有用的信息||水晶是合成魔法的基础材料，可以在商店中购买获得||在使用合成魔法时，如果发现某个材料不足，直接点击他就可购买了||小屋是通过添置家具增加魔法值上限来升级的||长时间不收取学费，学费就会变成石头哦||使用食品和饮料可以恢复魔法值||在魔法书中可以学到更多的魔法，越高级的魔法收入就越多||小屋每升一级都会吸引新的学生来你的教室学习魔法||一个好汉三个帮，拥有越多的好友，就能得到越多的帮助哦||使用不同的变化咒可以获得不同的特殊材料，所以......多多变化吧！||魔法师乐乐是伟大的大魔导师奥兹巴的爱徒，她为什么主动来帮助我们呢？||全屏游戏可以看到更多的景色||完成任务可以获得更多的经验和奖励||迎接新学生钱，请先把空位上的学费收走';
        $this->render();
    }
 }

