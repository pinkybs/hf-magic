<?php

class ApiController extends Zend_Controller_Action
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

    /**
     * init swf
     *
     */
    public function initswfAction()
    {
        //$uid = $this->uid;
        $apiBasicVer = Hapyfish2_Magic_Cache_BasicInfo::getBasicVersion();
        include_once(CONFIG_DIR . '/swfconfig.php');
        $this->echoResult($swfConfig);
    }

    public function initavatarAction()
    {
        $uid = $this->uid;
        $avatarId = $this->_request->getParam('avatarId');
        $result = Hapyfish2_Magic_Bll_User::initAvatar($uid, $avatarId);
        $this->echoResult($result);
    }

    public function initbasicAction()
    {
        header("Cache-Control: max-age=2592000");
        //header("Cache-Control: no-store, no-cache, must-revalidate");
        echo Hapyfish2_Magic_Bll_BasicInfo::getInitVoData();
        exit();
    }

    public function initAction()
    {
        $uid = $this->uid;
        $sceneType = 3;
        $scene = Hapyfish2_Magic_Bll_Scene::getData($uid, $sceneType);

        //连续登录信息 login time upd
        $loginInfo = Hapyfish2_Magic_Bll_User::updateLoginTime($uid);
        //sign daily award
        $arySignAward = Hapyfish2_Magic_Bll_DailyAward::getAwards($uid, $loginInfo['active_login_count']);
        //每天send Activity 次数
        $sendActivityCnt = Hapyfish2_Magic_Bll_Activity::getCount($uid);
        $scene['user']['signAwardNumber'] = $arySignAward['signAwardNumber'];
        $scene['user']['signDay'] = $arySignAward['signDay'];
        $scene['user']['isfans'] = $arySignAward['isfans'];
        $scene['user']['feedNum'] = $sendActivityCnt;

        //user magic list
        $userMagic = Hapyfish2_Magic_Cache_Magic::getList($uid, true);
        //user item list
        $userItem = Hapyfish2_Magic_HFC_Item::getUserItem($uid);
        $items = array();
        foreach ($userItem as $cid => $v) {
            if ($v['count'] > 0) {
                $items[] = array($cid, $v['count'], $cid);
            }
        }
        //user guide tutorial
        $guides = array();
        $helpInfo = Hapyfish2_Magic_Cache_UserHelp::getHelpInfo($uid);
        if ($helpInfo['completeCount'] < 7) {
            foreach ($helpInfo['helpList'] as $k => $v) {
                $guides[] = array('gid' => $k, 'state' => ($v == 0) ? 1 : 2);
            }
        }

        //user tasks
        if (2 == $sceneType) {
            $pMapId = substr($scene['sceneId'], 0, 1);
            $tasks = Hapyfish2_Magic_Bll_Task::getUserMapTask($uid, $pMapId);
        }
        else {
            $tutorialTasks = Hapyfish2_Magic_Bll_Task::getUserTutorialTask($uid, $helpInfo['helpList']);
            $dailyTasks = Hapyfish2_Magic_Bll_Task::getDailyTask($uid);
            $trunkTasks = Hapyfish2_Magic_Bll_Task::getUserTrunkTask($uid);
            $branchTasks = Hapyfish2_Magic_Bll_Task::getBranchTask($uid);
            $tasks = array_merge($tutorialTasks, $dailyTasks, $trunkTasks, $branchTasks);
        }

        //user bag
        $userBag = Hapyfish2_Magic_Bll_Bag::getData($uid);
        //user diary
        $diarys = Hapyfish2_Magic_Bll_Feed::getFeed($uid);
        //user scene list
        $sceneState = Hapyfish2_Magic_Bll_Scene::getState($uid);
        //event act
        $acts = Hapyfish2_Magic_Bll_Act::get($uid, $loginInfo['today_login_count']);
        $result = array('scene' => $scene, //'userInfo' => $userVo,
                		'magics' => $userMagic['study_ids'],
                        'transMagics' => $userMagic['trans_ids'], 'items' => $items, 'tasks' => $tasks,
                        'decorBagList' => $userBag, 'diarys' => $diarys, 'guides' => $guides,
                        'sceneState' => $sceneState, 'acts' => $acts);
        $this->echoResult($result);
    }

    public function friendlistAction()
    {
        $uid = $this->uid;
        $pageIndex = $this->_request->getParam('pageIndex', 1);
        $pageSize = $this->_request->getParam('pageSize', 1000);
        $rankResult = Hapyfish2_Magic_Bll_Friend::getRankList($uid, $pageIndex, $pageSize);
        $this->echoResult($rankResult);
    }

    public function homesceneAction()
    {
        $uid = $this->uid;
        $fid = $this->_request->getParam('uid');
        if ($fid == GM_UID_LELE) {
            header("Cache-Control: no-store, no-cache, must-revalidate");
            echo $result = Hapyfish2_Magic_Bll_DumpUser::restore($fid);
            exit();
        }
        else {
            $scene = Hapyfish2_Magic_Bll_Scene::getHomeData($uid, $fid, true);
            $result = Hapyfish2_Magic_Bll_UserResult::all();
            $result['scene'] = $scene;
            $helpInfo = Hapyfish2_Magic_Cache_UserHelp::getHelpInfo($uid);
            $tutorialTasks = Hapyfish2_Magic_Bll_Task::getUserTutorialTask($uid, $helpInfo['helpList']);
            $dailyTasks = Hapyfish2_Magic_Bll_Task::getDailyTask($uid);
            $trunkTasks = Hapyfish2_Magic_Bll_Task::getUserTrunkTask($uid);
            $branchTasks = Hapyfish2_Magic_Bll_Task::getBranchTask($uid);
            $tasks = array_merge($tutorialTasks, $dailyTasks, $trunkTasks, $branchTasks);
            $result['tasks'] = $tasks;
            $this->echoResult($result);
        }
    }

    public function tutorialAction()
    {
        $uid = $this->uid;
        $gid = (int) $this->_request->getParam('gid');
        if (empty($gid) || $gid <= 0 || $gid > 7) {
            $this->echoResult(array('result' => array('status' => - 1)));
        }
        $result = Hapyfish2_Magic_Bll_User::changehelp($uid, $gid);
        $this->echoResult($result);
    }

    public function finishtaskAction()
    {
        $uid = $this->uid;
        $taskId = (int) $this->_request->getParam('t_id');
        if (empty($taskId) || $taskId <= 0) {
            $this->echoResult(array('result' => array('status' => - 1)));
        }
        $key = 'm:u:lock:task:' . $uid . ':' . $taskId;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
        //get lock
        $ok = $lock->lock($key);
        if (! $ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('click_too_fast'));
        }
        $result = Hapyfish2_Magic_Bll_Task::finishTask($uid, $taskId);
        //release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

    ///////////////////////
    public function opendoorAction()
    {
        $uid = $this->uid;
        //获取门的id
        $doorId = $this->_request->getParam('decor_id');
        $key = 'm:u:lock:opendoor:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
        //get lock
        $ok = $lock->lock($key);
        if (! $ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('click_too_fast'));
        }

        $result = Hapyfish2_Magic_Bll_Door::open($uid, $doorId);

        //release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

    public function studentstudyAction()
    {
        $uid = $this->uid;
        //获取学生id
        $sid = $this->_request->getParam('student_id');
        if (empty($sid)) {
            $deskId = $this->_request->getParam('decor_id');
            $desk = Hapyfish2_Magic_HFC_Desk::getOne($uid, $deskId);
            $sid = $desk['student_id'];
        }
        $result = Hapyfish2_Magic_Bll_Student::studyMagic($uid, $sid);
        $this->echoResult($result);
    }

    public function pickupAction()
    {
        $uid = $this->uid;
        $fid = (int) $this->_request->getParam('uid');
        if (empty($fid)) {
            $fid = $uid;
        }
        $decor_ids = $this->_request->getParam('decor_ids');
        $deskIds = json_decode($decor_ids, true);

        $key = 'm:u:lock:pickup:' . $fid;
        $lock = Hapyfish2_Cache_Factory::getLock($fid);
        //get lock
        $ok = $lock->lock($key);
        if (! $ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('lock_failed'));
        }

        $result = Hapyfish2_Magic_Bll_Student::pickup($uid, $fid, $deskIds);
        if ($result['results'][0]['status'] == -1) {
            //$changeStudents = Hapyfish2_Magic_Bll_Student::getInScene($fid);
            //Hapyfish2_Magic_Bll_UserResult::addField($uid, 'changeStudents', $changeStudents);
            $scene = Hapyfish2_Magic_Bll_Scene::getHomeData($uid, $fid, true);
            $result['refreshScene'] = $scene;
        }

        //release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

    public function studentawardAction()
    {
        $uid = $this->uid;
        //学生id
        $sid = $this->_request->getParam('sid');
        $key = 'm:u:lock:stuaward:' . $uid . ':' . $sid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
        //get lock
        $ok = $lock->lock($key);
        if (! $ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('click_too_fast'));
        }

        $result = Hapyfish2_Magic_Bll_Student::award($uid, $sid);
        //release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

    public function studenthelpAction()
    {
        $uid = $this->uid;
        $fid = (int) $this->_request->getParam('uid');
        if (empty($fid)) {
            $fid = $uid;
        }
        $decor_ids = $this->_request->getParam('decor_ids');
        $deskIds = json_decode($decor_ids);

        $key = 'm:u:lock:studenthelp:' . $fid;
        $lock = Hapyfish2_Cache_Factory::getLock($fid);
        //get lock
        $ok = $lock->lock($key);
        if (! $ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('lock_failed'));
        }

        $result = Hapyfish2_Magic_Bll_Student::help($uid, $fid, $deskIds);
        if ($result['results'][0]['status'] == -1) {
            //$changeStudents = Hapyfish2_Magic_Bll_Student::getInScene($fid);
            //Hapyfish2_Magic_Bll_UserResult::addField($uid, 'changeStudents', $changeStudents);
            $scene = Hapyfish2_Magic_Bll_Scene::getHomeData($uid, $fid, true);
            $result['refreshScene'] = $scene;
        }

        //release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

    //////////////////
    public function mixAction()
    {
        $uid = $this->uid;
        $mid = $this->_request->getParam('mix_mid');
        $num = $this->_request->getParam('nums');

        $key = 'm:u:lock:mix:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
        //get lock
        $ok = $lock->lock($key);
        if (! $ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('click_too_fast'));
        }
        $result = Hapyfish2_Magic_Bll_Magic::mix($uid, $mid, $num);
        //release lock
        $lock->unlock($key);

        $this->echoResult($result);
    }

    public function transAction()
    {
        $uid = $this->uid;
        $fid = $this->_request->getParam('uid');
        $mid = $this->_request->getParam('trans_mid');

        $key = 'm:u:lock:trans:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
        //get lock
        $ok = $lock->lock($key);
        if (! $ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('click_too_fast'));
        }

        $result = Hapyfish2_Magic_Bll_Magic::trans($uid, $fid, $mid);
        //release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

    public function studytransAction()
    {
        $uid = $this->uid;
        $mid = $this->_request->getParam('trans_mid');

        $key = 'm:u:lock:studytrans:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
        //get lock
        $ok = $lock->lock($key);
        if (! $ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('click_too_fast'));
        }

        $result = Hapyfish2_Magic_Bll_Magic::studyTrans($uid, $mid);

        //release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

    public function studyteachAction()
    {
        $uid = $this->uid;
        $mid = $this->_request->getParam('magic_id');
        $result = Hapyfish2_Magic_Bll_Magic::studyTeach($uid, $mid);

        $key = 'm:u:lock:studyteach:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
        //get lock
        $ok = $lock->lock($key);
        if (! $ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('click_too_fast'));
        }

        //release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

    ///////////////////
    public function diyAction()
    {
        $uid = $this->uid;
        $decorChangeList = $this->_request->getParam('decorChangeList');
        $decorBagChangeList = $this->_request->getParam('decorBagChangeList');
        $floorChangeList = $this->_request->getParam('floorChangeList');
        $wallChangeList = $this->_request->getParam('wallChangeList');
        $building1 = empty($decorChangeList) ? array() : json_decode($decorChangeList, true);
        $building2 = empty($decorBagChangeList) ? array() : json_decode($decorBagChangeList, true);
        $floor = empty($floorChangeList) ? array() : json_decode($floorChangeList, true);
        $wall = empty($wallChangeList) ? array() : json_decode($wallChangeList, true);
        $data = array('building1' => $building1, 'building2' => $building2, 'floor' => $floor,
        'wall' => $wall);

        $key = 'm:u:lock:diy:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
        //get lock
        $ok = $lock->lock($key);
        if (! $ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('click_too_fast'));
        }
        $result = Hapyfish2_Magic_Bll_Scene::diy($uid, $data);

        //release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

    ///////////////////
    public function useitemAction()
    {
        $uid = $this->uid;
        $itemId = $this->_request->getParam('id');

        $key = 'm:u:lock:useitem:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
        //get lock
        $ok = $lock->lock($key);
        if (! $ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('click_too_fast'));
        }

        $result = Hapyfish2_Magic_Bll_Item::useItem($uid, $itemId);
        //release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

    public function buyitemAction()
    {
        $uid = $this->uid;
        $itemId = $this->_request->getParam('i_id');
        $num = $this->_request->getParam('num');

        $key = 'm:u:lock:buyitem:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
        //get lock
        $ok = $lock->lock($key);
        if (! $ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('click_too_fast'));
        }

        $result = Hapyfish2_Magic_Bll_Item::buyItem($uid, $itemId, $num);
        //release lock
        $lock->unlock($key);

        $this->echoResult($result);
    }

    public function loadbagAction()
    {
        $uid = $this->uid;
        //user bag
        $userBag = Hapyfish2_Magic_Bll_Bag::getData($uid);
        $result = array('decorList' => $userBag);
        $this->echoResult($result);
    }

    //////////////////////
    public function expandhouseAction()
    {
        $uid = $this->uid;
        $id = (int) $this->_request->getParam('id');
        $type = (int) $this->_request->getParam('type');
        if (empty($id) || empty($type)) {
            $this->echoResult(array('result' => array('status' => - 1)));
        }

        $key = 'm:u:lock:expandhouse:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
        //get lock
        $ok = $lock->lock($key);
        if (! $ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('click_too_fast'));
        }

        $result = Hapyfish2_Magic_Bll_Scene::expandhouse($uid, $id, $type);
        //release lock
        $lock->unlock($key);

        $this->echoResult($result);
    }

    ///////////////////////
    public function unlocksceneAction()
    {
        $uid = $this->uid;
        $sceneId = (int) $this->_request->getParam('sceneId');
        $type = (int) $this->_request->getParam('type');
        if (empty($sceneId) || empty($type)) {
            $this->echoResult(array('result' => array('status' => - 1)));
        }
        $result = Hapyfish2_Magic_Bll_Scene::unlock($uid, $sceneId, $type);
        $this->echoResult($result);
    }

    public function changesceneAction()
    {
        $uid = $this->uid;
        $sceneId = (int) $this->_request->getParam('sceneId');
        if (empty($sceneId)) {
            $this->echoResult(array('result' => array('status' => -1)));
        }
        $result = Hapyfish2_Magic_Bll_Scene::change($uid, $sceneId);
        if ($sceneId != HOME_SCENE_ID) {
            $scene = Hapyfish2_Magic_Bll_Scene::getOtherData($uid);
            $result['scene'] = $scene;
        }
        $this->echoResult($result);
    }

    //////////////////////
    public function killmonsterAction()
    {
        $uid = $this->uid;
        $enemyId = (int) $this->_request->getParam('enemyId');
        $key = 'm:u:lock:killmonster:' . $uid . ':' . $enemyId;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
        //get lock
        $ok = $lock->lock($key);
        if (! $ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('click_too_fast'));
        }
        $result = Hapyfish2_Magic_Bll_Monster::kill($uid, $enemyId);
        //release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

    public function initdailyawardAction()
    {
        header("Cache-Control: max-age=2592000");
        echo Hapyfish2_Magic_Bll_DailyAward::getDailyAwardsVoData();
        exit;
    }

    public function gaindailyawardAction()
    {
        $uid = $this->uid;
        $key = 'm:u:lock:gaindailyaward:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
        //get lock
        $ok = $lock->lock($key);
        if (! $ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('click_too_fast'));
        }
        $result = Hapyfish2_Magic_Bll_DailyAward::gainAwards($uid);
        //release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

    public function activityawardAction()
    {
        $aid = (int) $this->_request->getParam('id');
        $uid = $this->uid;
        $key = 'm:u:lock:activityaward:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
        //get lock
        $ok = $lock->lock($key);
        if (! $ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('click_too_fast'));
        }
        $result = Hapyfish2_Magic_Bll_Activity::gainAward($uid, $aid);
        //release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

    public function initcharacterAction()
    {
        header("Cache-Control: max-age=2592000");
        echo Hapyfish2_Magic_Bll_Character::listCharacterVo();
        exit;
    }

    public function listcharacterAction()
    {
        $uid = $this->uid;
        $result = Hapyfish2_Magic_Bll_Character::listUserCharacter($uid);
        $this->echoResult($result);
    }

    public function changecharacterAction()
    {
        $uid = $this->uid;
        $id = (int)$this->_request->getParam('avatarId');
        $key = 'm:u:lock:changecharacter:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
        //get lock
        $ok = $lock->lock($key);
        if (! $ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('click_too_fast'));
        }

        $result = Hapyfish2_Magic_Bll_Character::changeCharacter($uid, $id);

        //release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

    public function entermapAction()
    {
        $uid = $this->uid;
        $mapId = (int)$this->_request->getParam('sceneId');
        $portalId = (int)$this->_request->getParam('portalId');

        $key = 'm:u:lock:entermap:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
        //get lock
        $ok = $lock->lock($key);
        if (!$ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('click_too_fast'));
        }

        if (empty($mapId)) {
            $this->echoResult(array('result' => array('status' => -1)));
        }

        $result = Hapyfish2_Magic_Bll_Scene::changeScene($uid, $mapId, $portalId);

        if (!isset($result['tasks'])) {
            $helpInfo = Hapyfish2_Magic_Cache_UserHelp::getHelpInfo($uid);
            $tutorialTasks = Hapyfish2_Magic_Bll_Task::getUserTutorialTask($uid, $helpInfo['helpList']);
            $dailyTasks = Hapyfish2_Magic_Bll_Task::getDailyTask($uid);
            $trunkTasks = Hapyfish2_Magic_Bll_Task::getUserTrunkTask($uid);
            $branchTasks = Hapyfish2_Magic_Bll_Task::getBranchTask($uid);
            $tasks = array_merge($tutorialTasks, $dailyTasks, $trunkTasks, $branchTasks);
            $result['tasks'] = $tasks;
        }

        //release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

    public function hitmonsterAction()
    {
        $uid = $this->uid;
        $id = (int)$this->_request->getParam('id');
        $key = 'm:u:lock:hitmonster:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
        //get lock
        $ok = $lock->lock($key);
        if (! $ok) {
            $this->echoResult(Hapyfish2_Magic_Bll_UserResult::Error('click_too_fast'));
        }

        $result = Hapyfish2_Magic_Bll_MapCopy::hitMonster($uid, $id);

        //release lock
        $lock->unlock($key);
        $this->echoResult($result);
    }

}
