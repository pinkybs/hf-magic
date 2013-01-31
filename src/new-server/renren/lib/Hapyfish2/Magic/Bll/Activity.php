<?php

class Hapyfish2_Magic_Bll_Activity
{
    public static $_mcKeyPrex = 'm:u:dlyactivity:';
    public static $_dailyMax = 5;

	public static function getCount($uid)
	{
	    $rst = 0;
	    $today = date('Ymd');
        $mckey = self::$_mcKeyPrex . $uid;
        try {
            $cache = Hapyfish2_Cache_Factory::getMC($uid);
            $dailyActivity = $cache->get($mckey);
            if ($dailyActivity && $dailyActivity['dt'] == $today && $dailyActivity['cnt']) {
                $rst = $dailyActivity['cnt'];
            }
        }
	    catch (Exception $e) {
            $errMsg = join(' ', array('Err-getCount:', $uid, $e->getMessage()));
            info_log($errMsg, 'Hapyfish2_Magic_Bll_Activity');
	    }

		return $rst;
	}

    public static function gainAward($uid, $aid)
	{
	    $rst = array();
	    $today = date('Ymd');
        $mckey = self::$_mcKeyPrex . $uid;
        try {
            $cache = Hapyfish2_Cache_Factory::getMC($uid);
            $dailyActivity = $cache->get($mckey);
            if ($dailyActivity && $dailyActivity['dt'] == $today && $dailyActivity['cnt'] && $dailyActivity['cnt']>=self::$_dailyMax) {
                return Hapyfish2_Magic_Bll_UserResult::Error('no_more_award');
            }

            if ($dailyActivity && $dailyActivity['dt'] == $today && $dailyActivity['cnt']) {
                $dailyActivity['cnt'] += 1;
            }
            else {
                $dailyActivity = array();
                $dailyActivity['dt'] = $today;
                $dailyActivity['cnt'] = 1;
            }
            $cache->set($mckey, $dailyActivity, 3600*24);

            //send award to user
            $rowActivity = Hapyfish2_Magic_Cache_BasicInfo::getActivityInfo($aid);
            if (!$rowActivity) {
                return Hapyfish2_Magic_Bll_UserResult::Error('no_award');
            }

            $isSend = false;
    		$coin = $gem = 0;
    		$aryItem = array();
    		$aryDecor = array();
    		if (isset($rowActivity['awards'])) {
                $baseAward = json_decode($rowActivity['awards'], true);
    		    if (isset($baseAward['coin'])) {
                    $coin += $baseAward['coin'];
                }
                if (isset($baseAward['gem'])) {
                    $gem += $baseAward['gem'];
                }
    		    if (isset($baseAward['item'])) {
    		        foreach ($baseAward['item'] as $item) {
                        $aryItem[] = array($item[0], $item[1]);
                    }
                }
    		    if (isset($baseAward['decor'])) {
    		        foreach ($baseAward['decor'] as $decor) {
                        $aryDecor[] = array($decor[0], $decor[1]);
                    }
                }
    		}

            $sendAward = new Hapyfish2_Magic_Bll_Award();
            if ($coin) {
                $sendAward->setCoin($coin);
                $isSend = true;
            }
            if ($gem) {
                $sendAward->setGold($gem, 3);
                $isSend = true;
            }
            if ($aryItem && count($aryItem) > 0) {
                $sendAward->setItemList($aryItem);
                $isSend = true;
            }
            if ($aryDecor && count($aryDecor) > 0) {
                $sendAward->setDecorList($aryDecor);
                $isSend = true;
            }
            if ($isSend) {
                $rst = $sendAward->sendOne($uid);
            }

        }
	    catch (Exception $e) {
            $errMsg = join(' ', array('Err-gainAward:', $uid, $e->getMessage()));
            info_log($errMsg, 'Hapyfish2_Magic_Bll_Activity');
	    }

		return Hapyfish2_Magic_Bll_UserResult::all();
	}
}