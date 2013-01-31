<?php

class GiftController extends Zend_Controller_Action
{
    protected $uid;

    public function init()
    {
    	$info = $this->vailid();
        if (!$info) {
        	$result = array('status' => '-1', 'content' => 'serverWord_101');
			$this->echoResult($result);
        }

        $this->info = $info;
        $this->uid = $info['uid'];
        $data = array('uid' => $info['uid'], 'puid' => $info['puid'], 'session_key' => $info['session_key']);
        $context = Hapyfish2_Util_Context::getDefaultInstance();
        $context->setData($data);
        Hapyfish2_Magic_Bll_UserResult::setUser($info['uid']);

    	$controller = $this->getFrontController();
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);
    }

    protected function vailid()
    {
    	$skey = $_COOKIE[PRODUCT_ID.'_skey'];
    	return Hapyfish2_Validate_UserCertify::checkKey($skey, APP_SECRET);
    }

    protected function echoResult($data)
    {
    	header("Cache-Control: no-store, no-cache, must-revalidate");
    	echo json_encode($data);
    	exit;
    }

	public function listAction()
	{
		header("Cache-Control: max-age=2592000");
    	echo Hapyfish2_Magic_Bll_Gift::getGiftVoData();
		exit;
	}

	public function userAction()
	{
		$uid = $this->uid;
		$newReceCnt = 0;
		$receiveList = Hapyfish2_Magic_Bll_Gift::getReceiveList($uid, $newReceCnt);
		$requestList = Hapyfish2_Magic_Bll_Gift::getRequestList($uid);

		//can send wish today
		$canWish = true;
	    $today = date('Ymd');
		//read today wish cache
        //get my wish
		$wishCache = Hapyfish2_Magic_Bll_Gift::getMywish($uid);
	    if ( $wishCache && isset($wishCache['create_time']) && date('Ymd', $wishCache['create_time']) == $today ) {
            $canWish = false;
        }
        $giftMyWish = array();
        for ($i=0; $i<3; $i++) {
            $giftMyWish[] = array('id' => 0, 'type' => 0);
        }
        if ($wishCache) {
            if ($wishCache['gid_1']) {
                $giftInfo = Hapyfish2_Magic_Cache_Gift::getBasicGiftInfo($wishCache['gid_1']);
                $giftMyWish[0] = array('id' => $giftInfo['gid'], 'type' => $giftInfo['type']);
            }
            if ($wishCache['gid_2']) {
                $giftInfo = Hapyfish2_Magic_Cache_Gift::getBasicGiftInfo($wishCache['gid_2']);
                $giftMyWish[1] = array('id' => $giftInfo['gid'], 'type' => $giftInfo['type']);
            }
            if ($wishCache['gid_3']) {
                $giftInfo = Hapyfish2_Magic_Cache_Gift::getBasicGiftInfo($wishCache['gid_3']);
                $giftMyWish[2] = array('id' => $giftInfo['gid'], 'type' => $giftInfo['type']);
            }
        }

        $hasNewGift = $newReceCnt ? true : false;

		$giftUser = array('giftNum' => $newReceCnt, 'giftRequestNum' => count($requestList),
						  'isReleaseWish' => $canWish, 'isNewGift' => $hasNewGift);

		$rankResult = Hapyfish2_Magic_Bll_Friend::getFriendList($uid, 1, 1000);
		$friendList = $rankResult['friends'];
		$mkey2 = 'm:u:gift:sent:g:uids:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
        $sentCache = $cache->get($mkey2);
		foreach ($friendList as $key=>$data) {
		    $friendList[$key]['giftAble'] = true;
		    if ($sentCache && isset($sentCache['dt']) && $sentCache['dt'] == $today && isset($sentCache['ids'])) {
    		    if (in_array($data['uid'], $sentCache['ids'])) {
    		        $friendList[$key]['giftAble'] = false;
    		    }
		    }

		    $friendList[$key]['giftRequestAble'] = true;
		}

		$result = array('giftDiarys' => $receiveList, 'giftRequests' => $requestList,
						'giftUser' => $giftUser, 'giftFriendUser' => $friendList, 'giftMyWish' => $giftMyWish);

		$this->echoResult($result);
	}

	public function friendrequestAction()
	{
        $uid = $this->uid;
		$id = $this->_request->getParam('giftRequestId');
		$giftId = $this->_request->getParam('giftId');
	    if (empty($id) || empty($giftId)) {
            $result = array('result' => array('status'=>-1,'content'=>'invalid data'));
            $this->echoResult($result);
        }


	    $aryId = explode('|', base64_decode(urldecode($id)));
		if ( !(isset($aryId[0]) && isset($aryId[1])) ) {
		    $result['status'] = -1;
            $result['content'] = 'id invalid';
            return array('result' => $result);
		}

        $key = 'checkfriendrequest:' . $aryId[1];
        $lock = Hapyfish2_Cache_Factory::getLock($aryId[1]);
    	//get lock
		$ok = $lock->lock($key, 2);
	    if (!$ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('lock_failed'));
		}
   		$result = Hapyfish2_Magic_Bll_Gift::sendWish($id, $giftId);

        //release lock
        $lock->unlock($key);

		$this->echoResult($result);
	}

	public function ignoregiftAction()
	{
        $uid = $this->uid;
        $id = $this->_request->getParam('giftDiaryId');
        $ids = array($id);

        $key = 'checkreceivegift:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
    	//get lock
		$ok = $lock->lock($key, 2);
	    if (!$ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('lock_failed'));
		}
		$result = Hapyfish2_Magic_Bll_Gift::ignore($uid, $ids);

        //release lock
        $lock->unlock($key);

		$this->echoResult($result);
	}

	public function receivegiftAction()
	{
        $uid = $this->uid;
        $ids = $this->_request->getParam('giftDiaryId');

	    $key = 'checkreceivegift:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
    	//get lock
		$ok = $lock->lock($key, 2);
		if (!$ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('lock_failed'));
		}

		$ids = explode('-', $ids);
    	$result = Hapyfish2_Magic_Bll_Gift::accept($uid, $ids);

    	//release lock
        $lock->unlock($key);

		$this->echoResult($result);
	}

	public function sendAction()
	{
		$uid = $this->uid;
		$giftId = $this->_request->getParam('giftId');
		$fids = $this->_request->getParam('friendId');
	    if (empty($giftId) || empty($fids)) {
            $result = array('result' => array('status'=>-1,'content'=>'invalid data'));
            $this->echoResult($result);
        }

        $fids = explode('-', $fids);

        $key = 'giftsend:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
    	//get lock
		$ok = $lock->lock($key, 2);
		if (!$ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('lock_failed'));
		}
        $result = Hapyfish2_Magic_Bll_Gift::send($uid, $giftId, $fids);

        //release lock
        $lock->unlock($key);

		$this->echoResult($result);
	}

	public function mywishAction()
	{
		$uid = $this->uid;
		$gids = $this->_request->getParam('giftId');

        if (empty($gids)) {
            $result = array('result' => array('status'=>-1,'content'=>'invalid data'));
            $this->echoResult($result);
        }

	    $gids = explode('-', $gids);

		$result = Hapyfish2_Magic_Bll_Gift::mywish($uid, $gids);
		$this->echoResult($result);
	}

    public function hadreadAction()
	{
	    $uid = $this->uid;
	    $rst = Hapyfish2_Magic_Bll_Gift::readReceive($uid);
        $result = array('status'=>$rst);
        $this->echoResult($result);
	}

}
