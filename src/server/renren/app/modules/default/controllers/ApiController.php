<?php

/**
 * api controller for flash
 *
 * @copyright  Copyright (c)
 * @create     2010/08/19    zhangxin
 */
class ApiController extends Zend_Controller_Action
{
    protected $uid;

    /**
     * initialize basic data
     * @return void
     */
    public function init()
    {
        $auth = Zend_Auth::getInstance();
        /********* test uid *********/
        /*if (!$auth->hasIdentity() && Zend_Registry::get('static') == 'http://island.liz.cn/static' ) {
            $auth->getStorage()->write('258027420');
        }*/
        
info_log('api:'.$auth->getIdentity(), 'aa');    
        if (!$auth->hasIdentity()) {
        	$auth->getStorage()->write('22112313');
        	//$rst = array('result' => array('status'=>-1, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0007')));
			//$this->_echoResult($rst);
        }
        
//$this->_validate();
        
        $controller = $this->getFrontController();
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);
        $this->view->staticUrl = Zend_Registry::get('static');

        $this->uid = $auth->getIdentity();
        $_SESSION['active_time_last'] = $_SERVER['REQUEST_TIME'];
    }

    
	/**
     * init swf
     *
     */
    public function initswfAction()
    {
        require CONFIG_DIR . '/swfconfig.php';
        echo Zend_Json::encode($swfResult);
    }
    
	/**
     * init game  - as loadstaticinit  ***done***
     *
     */
    public function initgameAction()
    {
    	$uid = $this->uid;
		$aryData = Happyfish_Magic_Bll_FormatVo::initGame();
        $this->_echoResult($aryData);
    }

   	/**
     * load user - as loaduser  ***done***
     *
     */ 
	public function loaduserAction()
	{
		$uid = $this->uid;
		$showUid = $this->_request->getParam('uid');
		if (empty($showUid)) {
			$showUid = $uid;
		}
		
		//show user info 
		$rowUser = Happyfish_Magic_Bll_User::getUserGameInfo($showUid);
		if (empty($rowUser)) {
			$rst = array('result' => array('status'=>-1, 'content'=>Happyfish_Magic_Bll_Language_Local::getText('ERR0011')));
			$this->_echoResult($rst);
		}
		$userVo = Happyfish_Magic_Bll_FormatVo::userVo($rowUser);
		$this->_echoResult($userVo);
	}
	
	/**
     * load user's friends - as loadfriends  ***done***
     *
     */ 
	public function loadfriendsAction()
	{
		$uid = $this->uid;
		$pageIndex = $this->_request->getParam('pageIndex', 1);
		$pageSize = $this->_request->getParam('pageSize', 10);
		
		$lstFriend = Happyfish_Magic_Bll_User::getRanking($uid, $pageIndex, $pageSize);
		$aryFriend = array();
		foreach ($lstFriend as $data) {		
			$rowUser = Happyfish_Magic_Bll_User::getUserGameInfo($data['uid']);
			$aryFriend[] = Happyfish_Magic_Bll_FormatVo::userVo($rowUser);
		}
		$aryRst = array('friends' => $aryFriend);
		$this->_echoResult($aryRst);
	}
	
	/**
     * load user scene - as loadinit  ***done***
     *
     */ 
	public function loadsceneAction()
	{		
		$mode = 1; //in room
		$uid = $this->uid;
		$ownerUid = $this->_request->getParam('uid', $uid);
		
		//recover mp check
		$newmp = Happyfish_Magic_Bll_Magician::recoverMp($uid);
		
		//user info 
		$rowUser = Happyfish_Magic_Bll_User::getUserGameInfo($ownerUid);
		if (empty($rowUser)) {
			$ownerUid = $uid;
		}
		//door info 
        $lstDoor = Happyfish_Magic_Bll_GuestService::getDoorInfo($ownerUid);
		//decor info (door info in it)
        $lstUserBuilding = Happyfish_Magic_Bll_Decoration::getDecoration($ownerUid, $mode);
        $aryData = Happyfish_Magic_Bll_FormatVo::decorVo($lstUserBuilding, $mode, $lstDoor);
        //student info
        $lstDesk = Happyfish_Magic_Bll_GuestService::getDeskInfo($ownerUid);
        $aryData['students'] = Happyfish_Magic_Bll_FormatVo::studentVo($lstDesk, $rowUser['mgInfo']['in_house_guest_ary'], $rowUser['major_magic']);
		//user
        $userVo = Happyfish_Magic_Bll_FormatVo::userVo($rowUser);
        $aryData['user'] = $userVo;
        
		//is self
		if ($ownerUid == $uid) {
			//learnt magic
	        $lstMagic = Happyfish_Magic_Bll_Cache_User::lstUserMagic($ownerUid);
	        $magicVo = Happyfish_Magic_Bll_FormatVo::magicVo($rowUser, $lstMagic);
	        
	        //own items in bag
	        $dalCard = Happyfish_Magic_Dal_Card::getDefaultInstance();
	        $lstCard = $dalCard->lstUserCard($ownerUid);
	        $dalItem = Happyfish_Magic_Dal_Item::getDefaultInstance();
	        $lstItem = $dalItem->lstUserItem($ownerUid);
	        $itemVo = Happyfish_Magic_Bll_FormatVo::itemVo($lstCard, $lstItem);
	        $aryRst = array('scene' => $aryData, 'userInfo' => $userVo, 'magics' => $magicVo, 'items' => $itemVo);
		}
        else {
        	$aryRst = array('scene' => $aryData);
        }
        
		$this->_echoResult($aryRst);
	}
 
	//get user diy building list // as decorbag
	public function loaddecorinbagAction()
	{		
		$uid = $this->uid;
		$mode = 0; //in bag
        $lstUserBuilding = Happyfish_Magic_Bll_Decoration::getDecoration($uid, $mode);
        $aryData = Happyfish_Magic_Bll_FormatVo::decorVo($lstUserBuilding, $mode);
		$this->_echoResult($aryData);
	}
	
	//save user diy building   ***done***
	public function savedecorAction()
	{
		$uid = $this->uid;
		//get post data
		$build1 = Zend_Json::decode($this->_request->getParam('decorChangeList',''));
		$build2 = Zend_Json::decode($this->_request->getParam('decorBagChangeList',''));
		$build3 = Zend_Json::decode($this->_request->getParam('decorSellChangeList',''));
		$floor1 = Zend_Json::decode($this->_request->getParam('floorChangeList',''));
		$floor2 = Zend_Json::decode($this->_request->getParam('floorBagChangeList',''));
		$floor3 = Zend_Json::decode($this->_request->getParam('floorSellChangeList',''));
		$wall1 = Zend_Json::decode($this->_request->getParam('wallChangeList',''));
		$wall2 = Zend_Json::decode($this->_request->getParam('wallBagChangeList',''));
		$wall3 = Zend_Json::decode($this->_request->getParam('wallSellBagChangeList',''));
		$build1 = empty($build1) ? array() : $build1;
		$build2 = empty($build2) ? array() : $build2;
		$build3 = empty($build3) ? array() : $build3;
		$floor1 = empty($floor1) ? array() : $floor1;
		$floor2 = empty($floor2) ? array() : $floor2;
		$floor3 = empty($floor3) ? array() : $floor3;
		$wall1 = empty($wall1) ? array() : $wall1;
		$wall2 = empty($wall2) ? array() : $wall2;
		$wall3 = empty($wall3) ? array() : $wall3;
		/*for test $build1 = $build2 = array();
		$build1[] = array('id'=>22,'d_id'=>191001,'x'=>0,'y'=>0,'z'=>4,'mirror'=>1,'bag_type'=>1);
		$build2[] = array('id'=>20,'d_id'=>197001,'x'=>0,'y'=>0,'z'=>0,'mirror'=>1,'bag_type'=>0);
		$build3[] = array('id'=>21,'d_id'=>195019,'x'=>0,'y'=>0,'z'=>0,'mirror'=>1,'bag_type'=>0);
		$floor1[] = array('id'=>0,'d_id'=>193002,'x'=>3,'y'=>0,'z'=>1,'mirror'=>1,'bag_type'=>1);
		$floor1[] = array('id'=>0,'d_id'=>193001,'x'=>2,'y'=>0,'z'=>2,'mirror'=>1,'bag_type'=>1);
		$floor3[] = array('id'=>0,'d_id'=>193001,'x'=>0,'y'=>0,'z'=>0,'mirror'=>1,'bag_type'=>1,'num'=>1);

		$wall1[] = array('id'=>0,'d_id'=>194003,'x'=>1,'y'=>0,'z'=>0,'mirror'=>1,'bag_type'=>1);
		$wall1[] = array('id'=>0,'d_id'=>194002,'x'=>0,'y'=>0,'z'=>1,'mirror'=>1,'bag_type'=>1);
		$wall3[] = array('id'=>0,'d_id'=>194001,'x'=>0,'y'=>0,'z'=>0,'mirror'=>1,'bag_type'=>1,'num'=>2);
		*/

		//building info
		$aryBuild = array_merge($build1, $build2);
		$aryPostBuild = array();
		foreach ($aryBuild as $key=>$vdata) {
			$aryPostBuild[$key]['id'] = $vdata['id'];
			$aryPostBuild[$key]['building_id'] = $vdata['d_id'];
			$aryPostBuild[$key]['pos_x'] = $vdata['x'];
			$aryPostBuild[$key]['pos_y'] = $vdata['y'];
			$aryPostBuild[$key]['pos_z'] = $vdata['z'];
			$aryPostBuild[$key]['mirror'] = $vdata['mirror'];
			$aryPostBuild[$key]['status'] = $vdata['bag_type'];
		}
		foreach ($build3 as $sdata) {
			$aryPostBuild[] = array('sell'=>1, 'id'=>$sdata['id']);
		}
		
		//floor info
		$aryFloor = $floor1;
		$aryShowFloor = $arySellFloor = array();
		foreach ($aryFloor as $key=>$vdata) {
			$aryShowFloor[$key]['floor_id'] = $vdata['d_id'];
			$aryShowFloor[$key]['pos_x'] = $vdata['x'] - 1;
			$aryShowFloor[$key]['pos_z'] = $vdata['z'] - 1;
		}
		foreach ($floor3 as $sdata) {
			$arySellFloor[] = array('sell'=>1, 'floor_id'=>$sdata['d_id'], 'quantity'=>(empty($sdata['num'])?1:$sdata['num']));
		}
		$aryPostFloor = array('aryShowFloor' => $aryShowFloor, 'arySellFloor' => $arySellFloor);
		
		//wall info
		$aryWall = $wall1;
		$aryShowWall = $arySellWall = array();
		foreach ($aryWall as $key=>$vdata) {
			$aryShowWall[$key]['wall_id'] = $vdata['d_id'];
			//x-wall
			if (0 == $vdata['z']) {
				$aryShowWall[$key]['pos_x'] = 0;
				$aryShowWall[$key]['pos_z'] = $vdata['x'] - 1;
			}
			//y-wall
			else if (0 == $vdata['x']) {
				$aryShowWall[$key]['pos_x'] = 1;
				$aryShowWall[$key]['pos_z'] = $vdata['z'] - 1;
			}
		}
		foreach ($wall3 as $sdata) {
			$arySellWall[] = array('sell'=>1, 'wall_id'=>$sdata['d_id'], 'quantity'=>(empty($sdata['num'])?1:$sdata['num']));
		}
		$aryPostWall = array('aryShowWall' => $aryShowWall, 'arySellWall' => $arySellWall);	
        $rst = Happyfish_Magic_Bll_Decoration::changeDecoration($uid, $aryPostBuild, $aryPostFloor, $aryPostWall);
        
        //refresh get
        $resultVo = array();
        $resultVo['status'] = -1;
        $resultVo['content'] = '';
        $resultVo['levelUp'] = false;
        $resultVo['red'] = 0;
        $resultVo['blue'] = 0;
        $resultVo['green'] = 0;
        $resultVo['exp'] = 0;
        $aryData = array();
        if ($rst) {
        	$resultVo['status'] = 1;
        	$resultVo['red'] = $rst['red'];
        	$resultVo['blue'] = $rst['blue'];
        	$resultVo['green'] = $rst['green'];
        	$mode = 0; //in bag
	        $lstUserBuilding = Happyfish_Magic_Bll_Decoration::getDecoration($uid, $mode);
	        $aryData = Happyfish_Magic_Bll_FormatVo::decorVo($lstUserBuilding, $mode);
        }
        
        $aryRst = array('result' => $resultVo, 'scene' => $aryData);
        $this->_echoResult($aryRst);
	}


	
	//get door info
	public function getdoorAction()
	{
		$uid = $this->uid;
		$aryDoor = Happyfish_Magic_Bll_GuestService::getDoorInfo($uid);
		
		$this->_echoResult($aryData);
	}
	
	//open door 
	public function opendoorAction()
	{
		$uid = $this->uid;
		$doorId = $this->_request->getParam('decor_id');
		$aryData = Happyfish_Magic_Bll_GuestService::openDoor($uid, $doorId);
		
		$this->_echoResult($aryData);
	}

	public function serveguestAction()
    {
    	$uid = $this->uid;
    	$deskid = $this->_request->getParam('decor_id');
    	$rst = Happyfish_Magic_Bll_GuestService::serveGuest($uid, $deskid);
    	$this->_echoResult($rst);
    }
    
	public function stealcrystalAction()
    {
    	$uid = $this->uid;
    	$deskIds = Zend_Json::decode($this->_request->getParam('decor_ids'));
    	$rst = Happyfish_Magic_Bll_GuestService::stealcrystal($uid, $uid, $deskIds);
    	unset($rst['messages']);
    	$this->_echoResult($rst);
    }
    
	public function rescueguestAction()
    {
    	$uid = $this->uid;
    	$target = $this->_request->getParam('uid');
    	$target = empty($target) ? $uid : $target;
    	$deskIds = Zend_Json::decode($this->_request->getParam('decor_ids'));
    	$rst = Happyfish_Magic_Bll_GuestService::rescueGuest($uid, $target, $deskIds);
    	
    	$this->_echoResult($rst);
    }
    
	
	protected function _echoResult($aryResult)
    {
    	/*$result = array();
    	$result['errNo'] = $errNo;
    	if ($errMsg) {
    		$result['errMsg'] = $errMsg;
    	}
    	$result['result'] = $aryResult;
		*/
    	echo Zend_Json::encode($aryResult);
    	exit;
    }

	/**
     * magic function
     *   if call the function is undefined,then echo undefined
     *
     * @param string $methodName
     * @param array $args
     * @return void
     */
    function __call($methodName, $args)
    {
        echo 'undefined method name: ' . $methodName;
        exit;
    }
    
}