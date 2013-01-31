<?php

class UnittestController extends Zend_Controller_Action
{
    protected function vailid()
    {
    	$skey = $_COOKIE[PRODUCT_ID.'_skey'];
    	return Hapyfish2_Validate_UserCertify::checkKey($skey, APP_SECRET);
    }


	public function inituserAction()
	{
		$puid = $this->_request->getParam('puid');
		$uidInfo = Hapyfish2_Platform_Cache_UidMap::getUser($puid);
		if (!$uidInfo) {
    		$uidInfo = Hapyfish2_Platform_Cache_UidMap::newUser($puid);
    		if (!$uidInfo) {
    			echo 'inituser error: 1';
    			exit;
    		}
		}
		$uid = $uidInfo['uid'];
        $user = array();
        $user['uid'] = $uid;
        $user['puid'] = $puid;
        $user['name'] = '测试' . $puid;
        $user['figureurl'] = 'http://hdn.xnimg.cn/photos/hdn521/20091210/1355/tiny_E7Io_11729b019116.jpg';
        $user['gender'] = rand(0,1);

		Hapyfish2_Platform_Bll_User::addUser($user);

		Hapyfish2_Magic_Bll_User::joinUser($uid);

		echo 'OK: ' . $uid;
		exit;
	}

	public function updateavartarAction()
	{
		$uid = $this->_request->getParam('uid');
		$aid = $this->_request->getParam('aid');
		$result = Hapyfish2_Magic_Bll_User::initAvatar($uid, $aid);
		echo $result ? 'ok' : 'false';
		exit;
	}

	public function tmemAction()
	{
		$uid = 10018;
		$key = 't:' . '1';
		$data = 't1';
		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$cache->add($key, $data);
		$data1 = $cache->get($key);

		echo $data . '<br/>' . $data1;
		exit;
	}

	public static function randofArray(&$arr)
	{
		$key = array_rand($arr);
		return $arr[$key];
	}

	public function getitemAction()
	{
		$uid = $this->_request->getParam('uid');
		$item = Hapyfish2_Magic_HFC_Item::getUserItem($uid);
		print_r($item);

		exit;
	}

	public function t1Action()
	{
		$uid = 10018;
		//$userMagic = Hapyfish2_Magic_Cache_Magic::getList($uid, false);
		//print_r($userMagic);
		$deskId = '107';
		$sid = '1';
       	$t = $this->_request->getParam('t');
		$stepX = 1;
		$size = 9;

		$desk = Hapyfish2_Magic_HFC_Desk::getOne($uid, $deskId);

		print_r($desk);

		exit;
	}

	public function addmpAction()
	{
		$uid = 10018;
		Hapyfish2_Magic_HFC_User::incUserMp($uid, 100);
		exit;
	}

	public function deccoinAction()
	{
		$uid = 10018;
		$coin = '100';
		$c1 = Hapyfish2_Magic_HFC_User::getUserCoin($uid);
		Hapyfish2_Magic_HFC_User::decUserCoin($uid, $coin);
		$c2 = Hapyfish2_Magic_HFC_User::getUserCoin($uid);
		echo $c1 . ' -> ' . $c2;
		exit;
	}

	public function getfeedAction()
	{
		$uid = $this->_request->getParam('uid');
		$feeds = Hapyfish2_Magic_Bll_Feed::getFeedData($uid);
		print_r($feeds);
		exit;
	}

	public function getfloorbagAction()
	{
		$uid = $this->_request->getParam('uid');
		$floors = Hapyfish2_Magic_HFC_FloorBag::getUserFloor($uid);
		print_r($floors);
		exit;

	}

	public function getcoinAction()
	{
		$uid = $this->_request->getParam('uid');
		$coin = Hapyfish2_Magic_HFC_User::getUserCoin($uid);
		echo $coin;
		exit;
	}

	public function inithouseAction()
	{
		$uid = $this->_request->getParam('uid');
		$floorData = '[[193005,193005,193005,193005,193005,193005,193005,193005],[193005,193005,193005,193005,193005,193005,193005,193005],[193005,193005,193005,193005,193005,193005,193005,193005],[193005,193005,193005,193005,193005,193005,193005,193005],[193005,193005,193005,193005,193005,193005,193005,193005],[193005,193005,193005,193005,193005,193005,193005,193005],[193005,193005,193005,193005,193005,193005,193005,193005],[193005,193005,193005,193005,193005,193005,193005,193005]]';
		$wallData = '[[194001,194001,194001,194001,194001,194001,194001,194001],[194001,194001,194001,194001,194001,194001,194001,194001]]';
		$x = 8;
		$userSceneInfo = Hapyfish2_Magic_HFC_User::getUserScene($uid);
		$userSceneInfo['tile_x_length'] = $x;
		$userSceneInfo['tile_z_length'] = $x;
		Hapyfish2_Magic_HFC_User::updateUserScene($uid, $userSceneInfo, true);
		Hapyfish2_Magic_Cache_Floor::updateInScene($uid, $floorData);
		Hapyfish2_Magic_Cache_Wall::updateInScene($uid, $wallData);
		echo 'ok';
		exit;
	}

	public function testgetuserAction()
	{
	    $info = $puid = $this->vailid();
        $rest = Renren_Client::getInstance();
        $rest->setUser($info['puid'], $info['session_key']);
        $aa = $rest->getUser();
        echo json_encode($aa);
        exit;
	}

	public function testgetfriendAction()
	{
        $info = $puid = $this->vailid();
        $rest = Renren_Client::getInstance();
        $rest->setUser($info['puid'], $info['session_key']);
        $aa = $rest->getFriendIds();
        echo json_encode($aa);
        exit;
	}

	public function testisfanAction()
	{
        $info = $puid = $this->vailid();
        $rest = Renren_Client::getInstance();
        $rest->setUser($info['puid'], $info['session_key']);
        $aa = $rest->isFan();
        echo json_encode($aa);
        exit;
	}

    public function testqueryorderAction()
	{
	    $order_numbers = $this->_request->getParam('orderids');
        $info = $puid = $this->vailid();
        $rest = Renren_Client::getInstance();
        $rest->setUser($info['puid'], $info['session_key']);
        $aa = $rest->queryOrders($order_numbers);
        echo json_encode($aa);
        exit;
	}

}