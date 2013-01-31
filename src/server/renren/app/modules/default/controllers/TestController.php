<?php

/**
 * island index controller
 *
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create      2010/01/20    Hulj
 */
class TestController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = Zend_Registry::get('static');
        $this->view->version = Zend_Registry::get('version');
        $this->view->hostUrl = Zend_Registry::get('host');
    }

    /**
     * index Action
     *
     */
    public function indexAction()
    {
    	echo 'hello magic test!! <br />';
    	
        $this->render();
    }
    
 	public function clearcAction()
    {
    	$controller = $this->getFrontController();
        $controller->setParam('noViewRenderer', true);
    	Happyfish_Magic_Bll_Cache_NbBasicInfo::clearAll();
    	echo 'clear cache done';
    }
    
	public function adddecorAction()
	{
		try {
			$uid = $this->_request->getParam('uid');
			$bid = $this->_request->getParam('bid');
			$dalb = Happyfish_Magic_Dal_Building::getDefaultInstance();
			$dalf = Happyfish_Magic_Dal_Floors::getDefaultInstance();
			$dalw = Happyfish_Magic_Dal_Walls::getDefaultInstance();
			$lstBu = Happyfish_Magic_Bll_Cache_NbBasicInfo::listNbBuilding();
			$aryBid = explode(',', $bid);
			foreach ($aryBid as $bid) {
				$data = $lstBu[$bid];
				if ($data['type'] != 3 && $data['type'] != 4) {
					$aryB = array();
					$aryB['uid'] = $uid;
					$aryB['building_id'] = $data['bid'];
					$aryB['building_type'] = $data['type'];
					$aryB['effect_mp'] = $data['effect_mp'];
					$aryB['create_time'] = time();
					$dalb->insert($aryB);
				}
				else if ($data['type'] == 3 ) {
					$aryF = array();
					$aryF['uid'] = $uid;
					$aryF['floor_id'] = $data['bid'];
					$aryF['quantity'] = 1;
					$row = $dalf->getUserFloorInBag($uid, $data['bid']);
					if (empty($row)) {
						$dalf->insertUserFloorInBag($aryF);
					}
					else {
						$dalf->updateUserFloorInBagByField($uid, $data['bid'], 'quantity', 1);
					}
					
				}
				else if ($data['type'] == 4 ) {
					$aryW = array();
					$aryW['uid'] = $uid;
					$aryW['wall_id'] = $data['bid'];
					$aryW['quantity'] = 1;
					$row = $dalw->getUserWallInBag($uid, $data['bid']);
					if (empty($row)) {
						$dalw->insertUserWallInBag($aryW);
					}
					else {
						$dalw->updateUserWallInBagByField($uid, $data['bid'], 'quantity', 1);
					}
				}
			}
			
			echo 'done!';
		}
		catch (Exception $e) {
			info_log($e->getMessage(), 'aa');
			echo $e->getMessage();
		}
		
		exit;
	}
    
    
	
	public function adduserAction()
    {
    	



/*
    	$newUid = 101;
    	$name = '姜浩';
    	$img = 'http://testmagic.hf.com:8111/static/apps/magic/images/liwu_013.jpg';
    	$dalMongo = Happyfish_Magic_Dal_Mongo_SnsUser::getDefaultInstance();
    	$dalMongo->addPerson(array('uid'=>$newUid, 'name' =>$name, 'sex' => 1, 'headurl' =>$img, 'tinyurl'=>$img));
    	$rst = Happyfish_Magic_Bll_User::join($newUid);
    	Happyfish_Magic_Bll_Cache_User::clearPerson($newUid);
    	Happyfish_Magic_Bll_Cache_User::clearAppUser($newUid);
    	
    	$newUid = 102;
    	$name = '姜浩2';
    	$img = 'http://testmagic.hf.com:8111/static/apps/magic/images/liwu_013.jpg';
    	$dalMongo = Happyfish_Magic_Dal_Mongo_SnsUser::getDefaultInstance();
    	$dalMongo->addPerson(array('uid'=>$newUid, 'name' =>$name, 'sex' => 1, 'headurl' =>$img, 'tinyurl'=>$img));
    	$rst = Happyfish_Magic_Bll_User::join($newUid);
    	Happyfish_Magic_Bll_Cache_User::clearPerson($newUid);
    	Happyfish_Magic_Bll_Cache_User::clearAppUser($newUid);
    	
    	$newUid = 103;
    	$name = '徐鹏';
    	$img = 'http://testmagic.hf.com:8111/static/apps/magic/images/liwu_014.jpg';
    	$dalMongo = Happyfish_Magic_Dal_Mongo_SnsUser::getDefaultInstance();
    	$dalMongo->addPerson(array('uid'=>$newUid, 'name' =>$name, 'sex' => 1, 'headurl' =>$img, 'tinyurl'=>$img));
    	$rst = Happyfish_Magic_Bll_User::join($newUid);
    	Happyfish_Magic_Bll_Cache_User::clearPerson($newUid);
    	Happyfish_Magic_Bll_Cache_User::clearAppUser($newUid);
    	
    	$newUid = 104;
    	$name = '徐鹏2';
    	$img = 'http://testmagic.hf.com:8111/static/apps/magic/images/liwu_014.jpg';
    	$dalMongo = Happyfish_Magic_Dal_Mongo_SnsUser::getDefaultInstance();
    	$dalMongo->addPerson(array('uid'=>$newUid, 'name' =>$name, 'sex' => 1, 'headurl' =>$img, 'tinyurl'=>$img));
    	$rst = Happyfish_Magic_Bll_User::join($newUid);
    	Happyfish_Magic_Bll_Cache_User::clearPerson($newUid);
    	Happyfish_Magic_Bll_Cache_User::clearAppUser($newUid);
    	
    	$newUid = 105;
    	$name = '赵炯';
    	$img = 'http://testmagic.hf.com:8111/static/apps/magic/images/liwu_015.jpg';
    	$dalMongo = Happyfish_Magic_Dal_Mongo_SnsUser::getDefaultInstance();
    	$dalMongo->addPerson(array('uid'=>$newUid, 'name' =>$name, 'sex' => 1, 'headurl' =>$img, 'tinyurl'=>$img));
    	$rst = Happyfish_Magic_Bll_User::join($newUid);
    	Happyfish_Magic_Bll_Cache_User::clearPerson($newUid);
    	Happyfish_Magic_Bll_Cache_User::clearAppUser($newUid);
    	
    	$newUid = 106;
    	$name = '赵炯2';
    	$img = 'http://testmagic.hf.com:8111/static/apps/magic/images/liwu_015.jpg';
    	$dalMongo = Happyfish_Magic_Dal_Mongo_SnsUser::getDefaultInstance();
    	$dalMongo->addPerson(array('uid'=>$newUid, 'name' =>$name, 'sex' => 1, 'headurl' =>$img, 'tinyurl'=>$img));
    	$rst = Happyfish_Magic_Bll_User::join($newUid);
    	Happyfish_Magic_Bll_Cache_User::clearPerson($newUid);
    	Happyfish_Magic_Bll_Cache_User::clearAppUser($newUid);
    	
    	$newUid = 107;
    	$name = 'eric';
    	$img = 'http://testmagic.hf.com:8111/static/apps/magic/images/liwu_016.jpg';
    	$dalMongo = Happyfish_Magic_Dal_Mongo_SnsUser::getDefaultInstance();
    	$dalMongo->addPerson(array('uid'=>$newUid, 'name' =>$name, 'sex' => 1, 'headurl' =>$img, 'tinyurl'=>$img));
    	$rst = Happyfish_Magic_Bll_User::join($newUid);
    	Happyfish_Magic_Bll_Cache_User::clearPerson($newUid);
    	Happyfish_Magic_Bll_Cache_User::clearAppUser($newUid);
    	
    	$newUid = 108;
    	$name = 'eric2';
    	$img = 'http://testmagic.hf.com:8111/static/apps/magic/images/liwu_016.jpg';
    	$dalMongo = Happyfish_Magic_Dal_Mongo_SnsUser::getDefaultInstance();
    	$dalMongo->addPerson(array('uid'=>$newUid, 'name' =>$name, 'sex' => 1, 'headurl' =>$img, 'tinyurl'=>$img));
    	$rst = Happyfish_Magic_Bll_User::join($newUid);
    	Happyfish_Magic_Bll_Cache_User::clearPerson($newUid);
    	Happyfish_Magic_Bll_Cache_User::clearAppUser($newUid);
    	
    	
    	$newUid = 109;
    	$name = '张琦';
    	$img = 'http://testmagic.hf.com:8111/static/apps/magic/images/liwu_017.jpg';
    	$dalMongo = Happyfish_Magic_Dal_Mongo_SnsUser::getDefaultInstance();
    	$dalMongo->addPerson(array('uid'=>$newUid, 'name' =>$name, 'sex' => 1, 'headurl' =>$img, 'tinyurl'=>$img));
    	$rst = Happyfish_Magic_Bll_User::join($newUid);
    	Happyfish_Magic_Bll_Cache_User::clearPerson($newUid);
    	Happyfish_Magic_Bll_Cache_User::clearAppUser($newUid);
    	
    	$newUid = 110;
    	$name = '张琦2';
    	$img = 'http://testmagic.hf.com:8111/static/apps/magic/images/liwu_017.jpg';
    	$dalMongo = Happyfish_Magic_Dal_Mongo_SnsUser::getDefaultInstance();
    	$dalMongo->addPerson(array('uid'=>$newUid, 'name' =>$name, 'sex' => 1, 'headurl' =>$img, 'tinyurl'=>$img));
    	$rst = Happyfish_Magic_Bll_User::join($newUid);
    	Happyfish_Magic_Bll_Cache_User::clearPerson($newUid);
    	Happyfish_Magic_Bll_Cache_User::clearAppUser($newUid);
    
    	
    	$newUid = 112;
    	$name = '陈辰一2';
    	$img = 'http://testmagic.hf.com:8111/static/apps/magic/images/liwu_018.jpg';
    	$dalMongo = Happyfish_Magic_Dal_Mongo_SnsUser::getDefaultInstance();
    	$dalMongo->addPerson(array('uid'=>$newUid, 'name' =>$name, 'sex' => 1, 'headurl' =>$img, 'tinyurl'=>$img));
    	$rst = Happyfish_Magic_Bll_User::join($newUid);
    	Happyfish_Magic_Bll_Cache_User::clearPerson($newUid);
    	Happyfish_Magic_Bll_Cache_User::clearAppUser($newUid);
*/    	
    	echo "<br> $rst <br>";
    	exit;
    }
    
    public function listusermagicAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$aa = Happyfish_Magic_Bll_Cache_User::lstUserMagic($uid);
    	echo Zend_Json::encode($aa);
    	exit;
    }
    
    public function addfriendAction() 
    {
    	$uid = $this->_request->getParam('uid');
    	$fid = $this->_request->getParam('fid');
    	if (Happyfish_Magic_Bll_SnsUser::isFriend($uid, $fid) || $uid == $fid) {
    		echo "Err:already friends!";
    		exit;
    	}
    	$fids = Happyfish_Magic_Bll_SnsUser::getFriends($uid);
    	$fids[] = $fid;
    	Happyfish_Magic_Bll_SnsUser::updateFriends($uid, $fids);
    	echo '1 Done!';
    	
    	if (Happyfish_Magic_Bll_SnsUser::isFriend($fid, $uid)) {
    		echo "Err:already friends!";
    		exit;
    	}
    	$fids = Happyfish_Magic_Bll_SnsUser::getFriends($fid);
    	$fids[] = $uid;
    	Happyfish_Magic_Bll_SnsUser::updateFriends($fid, $fids);
    	echo '2 Done!';
    	exit;
    }
    
    public function recalcbuildingmpAction()
    {
    	$uid = $this->_request->getParam('uid');
		$rst = Happyfish_Magic_Bll_Decoration::reCalcBuildingAdditionMp($uid);
		echo 'user building addition mp recalculated:' . $uid . " mp addition:$rst[0]|$rst[1]|$rst[2]";
    	exit;
    }
    
    public function clearusercAction()
    {
    	$uid = $this->_request->getParam('uid');
    	Happyfish_Magic_Bll_Cache_User::clearPerson($uid);
    	Happyfish_Magic_Bll_Cache_User::clearAppUser($uid);
    	Happyfish_Magic_Bll_Cache_User::clearFriends($uid);
    	Happyfish_Magic_Bll_Cache_User::clearRanking($uid);
    	Happyfish_Magic_Bll_Cache_User::clearUserMagic($uid);
    	Happyfish_Magic_Bll_Cache_User::clearUserCard($uid);
    	echo 'clear user cache done:' . $uid;
    	exit;
    }
    
    
	public function removeAction()
    {
    	$controller = $this->getFrontController();
        $controller->setParam('noViewRenderer', true);
    	//$rst = Happyfish_Magic_Bll_User::remove(22112313);
    	echo "<br> $rst <br>";
    }
    
	public function learnmagicAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$magicId = $this->_request->getParam('magicId');
    	$rst = Happyfish_Magic_Bll_Magician::learnMagic($uid, $magicId);
    	echo Zend_Json::encode($rst);
    	exit;
    }
    
    public function getdoorAction()
    {
    	$rst = Happyfish_Magic_Bll_GuestService::getDoorInfo(22112313);
    	echo Zend_Json::encode($rst);
    	exit;
    }
    
	public function opendoorAction()
    {
    	$rst = Happyfish_Magic_Bll_GuestService::openDoor(22112313,64);
    	echo Zend_Json::encode($rst);
    	exit;
    }
    
	public function serveguestAction()
    {
    	$rst = Happyfish_Magic_Bll_GuestService::serveGuest(22112313,63);
    	echo Zend_Json::encode($rst);
    	exit;
    }
	
	public function rescueguestAction()
    {
    	$rst = Happyfish_Magic_Bll_GuestService::rescueGuest(22112313,22112313,array(62,63));
    	echo Zend_Json::encode($rst);
    	exit;
    }
    
	public function stealcrystalAction()
    {
    	$rst = Happyfish_Magic_Bll_GuestService::stealcrystal(999999,22112313,array(62,63));
    	echo Zend_Json::encode($rst);
    	exit;
    }
    
	public function recovermpAction()
    {
    	$rst = Happyfish_Magic_Bll_Magician::recoverMp(22112313);
    	echo Zend_Json::encode($rst);
    	exit;
    }
    
	public function transshapeAction()
    {
    	$rst = Happyfish_Magic_Bll_Magician::transShape(22112313, 999999, 8003);
    	echo Zend_Json::encode($rst);
    	exit;
    }
    
	public function combinebuildAction()
    {
    	//Happyfish_Magic_Bll_Cache_User::clearAppUser(22112313);
    	$rst = Happyfish_Magic_Bll_Magician::combineBuild(22112313, 191010);
    	echo Zend_Json::encode($rst);
    	exit;
    }
    
    
	public function fillinmarketAction()
    {
    	$cnt = $this->_request->getParam('fillCnt', 0);
    	$rst = Happyfish_Magic_Bll_Market::fillInMarket(22112313, $cnt);
    	echo Zend_Json::encode($rst);
    	exit;
    }
    
	public function takebackmarketAction()
    {
    	$cnt = $this->_request->getParam('backCnt', 0);
    	$rst = Happyfish_Magic_Bll_Market::takeBackMarket(22112313, $cnt);
    	echo Zend_Json::encode($rst);
    	exit;
    }
    
    
	public function enlargemarketAction()
    {
    	$rst = Happyfish_Magic_Bll_Market::enlargeMarket(22112313);
    	echo Zend_Json::encode($rst);
    	exit;
    }

	public function checkmarketstatusAction()
    {
    	$rst = Happyfish_Magic_Bll_Market::checkMarketStatus(22112313);
    	echo Zend_Json::encode($rst);
    	exit;
    }
    
	public function exchangeAction()
    {
    	$rst = Happyfish_Magic_Bll_Market::exchange(22112313, 999999, 2);
    	echo Zend_Json::encode($rst);
    	exit;
    }
    
	public function gainmarketcrystalAction()
    {
    	$rst = Happyfish_Magic_Bll_Market::gainMarketCrystal(22112313);
    	echo Zend_Json::encode($rst);
    	exit;
    }
    
	public function buycardAction()
    {
    	$cid = $this->_request->getParam('cid');
    	$rst = Happyfish_Magic_Bll_Shop::buyCard(22112313,$cid, 1);
    	echo Zend_Json::encode($rst);
    	exit;
    }
    
	public function usecardAction()
    {
    	$cid = $this->_request->getParam('cid');
    	$rst = Happyfish_Magic_Bll_Shop::useCard(22112313,$cid);
    	echo Zend_Json::encode($rst);
    	exit;
    }
    
	public function lstmsgAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$rst = Happyfish_Magic_Bll_Message::getUserMessage($uid);
    	echo Zend_Json::encode($rst);
    	exit;
    }
    
	public function lstrankingAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$rst = Happyfish_Magic_Bll_User::getRanking($uid, 1, 10);
    	echo Zend_Json::encode($rst);
    	exit;
    }
    
	public function gettodaydailytaskAction()
    {
    	$uid = $this->_request->getParam('uid', 22112313);
    	$task = Happyfish_Magic_Bll_TaskDaily::getTodayTask($uid);
    	echo Zend_Json::encode($task);
    	exit;
    }
    
	public function cleanroomAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$lstDesk = Happyfish_Magic_Bll_GuestService::getDeskInfo($uid);
    	$dalMgDesk = Happyfish_Magic_Dal_Mongo_UserDesk::getDefaultInstance();
    	foreach ($lstDesk as $desk) {
    		$dalMgDesk->update($uid, $desk['desk_id'], 
									   array('status'=>0, 'guest_id'=>0, 'magic_id'=>0,
									         'red'=>0, 'blue'=>0, 'green'=>0,
									   		 'start_time'=>0, 'break_time'=>0, 'rescue_time'=>0,'spend_time'=>0,
									         'help_uid'=>0,'steal_uid_ary'=>''));
    	}
    	$dalMgUser = Happyfish_Magic_Dal_Mongo_UserLoginInfo::getDefaultInstance();
    	$dalMgUser->update($uid, array('in_house_guest_ary' => array()));
    	echo 'Ok';
    	exit;
    }
    
    
    public function fullmpAction()
    {
    	$uid = $this->_request->getParam('uid', 22112313);
    	$rowUser = Happyfish_Magic_Bll_User::getUserGameInfo($uid);
    	$maxMp = $rowUser['nbLevInfo']['max_mp'] + $rowUser['mp_addition'];
    	$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
    	$dalUser->updateUser(array('mp'=>$maxMp), $uid);
    	Happyfish_Magic_Bll_Cache_User::clearAppUser($uid);
    	echo 'Ok';
    	exit;
    }
    
    
	public function addcrystalAction() 
    {
    	$uid = $this->_request->getParam('uid');
    	$red = (int)$this->_request->getParam('red');
    	$blue = (int)$this->_request->getParam('blue');
    	$green = (int)$this->_request->getParam('green');
    	$money = (int)$this->_request->getParam('money');
    	if (empty($uid)) {
    		echo 'false';
    		exit;
    	}
    	$dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
    	if (!$dalUser->getUser($uid)) {
    		echo 'no such user';
    		exit;
    	}
    	$aryParam = array();
    	if ($red) {
    		$aryParam['red'] = $red;
    	}
    	if ($blue) {
    		$aryParam['blue'] = $blue;
    	}
    	if ($green) {
    		$aryParam['green'] = $green;
    	}
    	if ($money) {
    		$aryParam['money'] = $money;
    		$dalLog = Happyfish_Magic_Dal_MoneyLog::getDefaultInstance();
			$dalLog->insert(array('uid'=>$uid, 'money'=>$money, 'order_id'=>-99, 'create_time'=>time()));
    	}
    	$dalUser->updateUserByMultipleField($uid, $aryParam);
    	Happyfish_Magic_Bll_Cache_User::clearAppUser($uid);
    	echo 'add done:' . $uid;
    	exit;
    }
    
    
}
