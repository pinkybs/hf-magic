<?php

class Hapyfish2_Magic_Bll_Act
{
    public static $actEvent = array(
                '201112Xmas' =>array('201112Xmas', '2011圣诞节活动', '20111221', '20120116', '[{"type":"1","id":"8325","num":"1","per":"20"}]')
    );

    public static function get($uid = 0, $todayLoginCnt = 1)
	{
		$actState = array();
        $idxNum = 1;
		$userHelp = Hapyfish2_Magic_Cache_UserHelp::getHelpInfo($uid);
        $comCount = $userHelp['completeCount'];
        if ($comCount >= 7) {
    	    //has gained today's awards
        	$mckey = Hapyfish2_Magic_Bll_DailyAward::$_mcKeyPrex . $uid;
            $cache = Hapyfish2_Cache_Factory::getMC($uid);
            $dailyAward = $cache->get($mckey);
    		if ($dailyAward && $dailyAward['award'] && $dailyAward['dt'] == date('Ymd') && !$dailyAward['gained']) {
    			//连续登录
        		$dlyAwardAct = array(
        			"actName" => "signAct",
                    "initIndex" => $idxNum++,
                    "state" => 2,
                    "backModuleUrl" => STATIC_HOST . '/swf/SignAwardAct.swf?v=20111221v1'
                );
                $actState[] = $dlyAwardAct;
    		}
        }
        /*//新手引导
        else {
            $guideAct = array(
    			'actName' => 'guides',
    			'initIndex' => 1,
    			'state' => 2,
    			'backModuleUrl' => STATIC_HOST . '/swf/guides.swf'
		    );
		    $actState[] = $guideAct;
        }*/

		//礼物
		$mkey = 'm:u:gift:newrececnt:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $newReceCnt = (int)$cache->get($mkey);
		$giftAct = array(
			'actName' => 'giftact',
			'initIndex' => $idxNum++,
			'state' => 2,
			'backModuleUrl' => STATIC_HOST . '/swf/GiftGetAct.swf?v=20111221v1',
		    'moduleData' => array('giftNum' => $newReceCnt)
		);
		$actState[] = $giftAct;

		//dm
        $dmAct = array (
            'actName' => 'HappyMagicDM',
            'moduleUrl' => STATIC_HOST . '/swf/HappyMagicDM.swf',
            'initIndex' => $idxNum++,
            'state' => 2,
            'menuType' => 1,
            'menuClass' => 'HappyMagicDMBtn',
            'menuUrl' => STATIC_HOST . '/swf/HappyMagicDMIcon.swf',
            'moduleData' => array('dmUrl' => STATIC_HOST . '/swf/HappyMagicDMSWC.swf')
		);
		if ($todayLoginCnt == 1) {
		    $dmAct['backModuleUrl'] = STATIC_HOST . '/swf/HappyMagicDM.swf';
		}
        $actState[] = $dmAct;

        //活动模块
        self::eventAct($uid, $actState, $idxNum);

        //diy重叠模块
	    $diyRpAct = array (
            'actName' => 'overlapFix',
            'initIndex' => $idxNum++,
            'state' => 2,
            'backModuleUrl' => STATIC_HOST . '/swf/overlapFix.swf'
        );
        $actState[] = $diyRpAct;

		return $actState;
	}

	private static function eventAct($uid, &$actState, &$idxNum)
	{
	    //201112Xmas
	    $event = self::$actEvent['201112Xmas'];
        $eCode = $event[0];
        $eName = $event[1];
        $eStart = strtotime($event[2]);
        $eEnd = strtotime($event[3]);

	    $evtCollection = new Hapyfish2_Magic_Event_EvtCollect($eCode, $eName, $eStart, $eEnd);
        if ($evtCollection->checkAvailLife()) {
            $list = Hapyfish2_Magic_Event_Cache_Basic::getCollectionList($eCode);
            if ($list) {
                $collectIds = Hapyfish2_Magic_Event_Bll_Collection::getUserCollect($uid);
                if ($collectIds) {
                    $aryCollectIds = explode(',', $collectIds);
                }
                else {
                    $aryCollectIds = array();
                }
                $moduleData = array();
                $aryAward = array();
                foreach ($list as $id=>$data) {
                    $needCon = json_decode($data['need_condition'], true);
                    $moduleData['priceItemCid'] = $needCon[0]['id'];
                    $state = 0;
                    if (in_array($id, $aryCollectIds)) {
                        $state = 1;
                    }
                    $aryAward[] = array('id'=>$id, 'price'=>$needCon[0]['num'], 'state'=>$state);
                }
                $moduleData['awards'] = $aryAward;
                $moduleData['request'] = array('prizesAwardGet' => 'event/collect');
        	    $collAct = array (
                    'actName' => 'christmasAct',
        	    	'moduleUrl'=> STATIC_HOST . '/swf/christmasAct.swf',
                    'initIndex' => $idxNum++,
                    'state' => 1,
        	    	'menuType' => 1,
            		'menuClass' => 'christmasIcon',
            		'menuUrl' => STATIC_HOST . '/swf/christmasIcon.swf',
                    'moduleData' => $moduleData
        		);
                $actState[] = $collAct;
            }
        }

	}

}