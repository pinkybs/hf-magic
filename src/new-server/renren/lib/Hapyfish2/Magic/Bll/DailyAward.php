<?php

class Hapyfish2_Magic_Bll_DailyAward
{

	public static $_mcKeyPrex = 'm:u:dlyaward:';
	public static $_mcGainAwardAll = 'magic:dlyaward:cntall';

	public static function getDailyAwardsVoData()
    {
        $list = Hapyfish2_Magic_Cache_BasicInfo::getDailyAwardList();
        $rstVo = array();
        foreach ($list as $data) {
            $activeDay = $data['id'];
            $awards = array();
            $fansaward = array();
            $aryAward = json_decode($data['base_award'], true);
            $aryFansAward = json_decode($data['fans_award'], true);
            // award
            if (isset($aryAward['coin'])) {
                $awards[] = array('type' => '3', 'id' => 'coin', 'num' => $aryAward['coin']);
            }
            if (isset($aryAward['gem'])) {
                $awards[] = array('type' => '3', 'id' => 'gmoney', 'num' => $aryAward['gem']);
            }
            if (isset($aryAward['exp'])) {
                $awards[] = array('type' => '3', 'id' => 'exp', 'num' => $aryAward['exp']);
            }
            if (isset($aryAward['item'])) {
                foreach ($aryAward['item'] as $item) {
                    $awards[] = array('type' => '1', 'id' => $item[0], 'num' => $item[1]);
                }
            }
            if (isset($aryAward['decor'])) {
                foreach ($aryAward['decor'] as $decor) {
                    $awards[] = array('type' => '2', 'id' => $decor[0], 'num' => $decor[1]);
                }
            }
            // fan plus award
            if (isset($aryFansAward['coin'])) {
                $fansaward[] = array('type' => '3', 'id' => 'coin', 'num' => $aryFansAward['coin']);
            }
            if (isset($aryFansAward['gem'])) {
                $fansaward[] = array('type' => '3', 'id' => 'gmoney', 'num' => $aryFansAward['gem']);
            }
            if (isset($aryFansAward['exp'])) {
                $fansaward[] = array('type' => '3', 'id' => 'exp', 'num' => $aryFansAward['exp']);
            }
            if (isset($aryFansAward['item'])) {
                foreach ($aryFansAward['item'] as $item) {
                    $fansaward[] = array('type' => '1', 'id' => $item[0], 'num' => $item[1]);
                }
            }
            if (isset($aryFansAward['decor'])) {
                foreach ($aryFansAward['decor'] as $decor) {
                    $fansaward[] = array('type' => '2', 'id' => $decor[0], 'num' => $decor[1]);
                }
            }
            $rstVo[] = array('day' => $activeDay, 'awards' => $awards, 'fansaward' => $fansaward);
        }

        $result = array('signAwardClass' => $rstVo);
        return json_encode($result);
    }

    /**
     * check user task info
     *
     * @param integer $uid
     * @param integer $activeDays
     * @return array
     */
    public static function getAwards($uid, $activeDays)
    {

    	$result = array();
    	$result['signAwardNumber'] = rand(5000, 9999);
    	$result['signDay'] = -1;//[int] -1 表示领完了 0 是无奖励  1是连续一天登陆
    	$result['isfans'] = false;
    	$today = date('Ymd');

    	//is app fans
    	$context = Hapyfish2_Util_Context::getDefaultInstance();
		$puid = $context->get('puid');
		$session_key = $context->get('session_key');
		$rest = Renren_Client::getInstance();
		$rest->setUser($puid, $session_key);
		$isFan = $rest->isFan();
		$result['isfans'] = $isFan ? true : false;

    	//today gained user count
    	$basCache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
    	$rowGainAll = $basCache->get(self::$_mcGainAwardAll);
    	if (!$rowGainAll) {
    	    $rowGainAll = array('dt' => $today, 'cnt' => 0);
            $basCache->set(self::$_mcGainAwardAll, $rowGainAll);
    	}
    	if ($rowGainAll['dt'] != $today) {
    	    info_log($rowGainAll['dt'].'|'.$rowGainAll['cnt'], 'dailyAwardGainCount');
    	    $rowGainAll = array('dt' => $today, 'cnt' => 0);
    	    $basCache->set(self::$_mcGainAwardAll, $rowGainAll, 3600*24);
    	}
    	$result['signAwardNumber'] = $rowGainAll['cnt'];


    	//has gained today's awards
    	$mckey = self::$_mcKeyPrex . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $dailyAward = $cache->get($mckey);//$dailyAward['date'], $dailyAward['award'], $dailyAward['gained']
		if ($dailyAward && $dailyAward['award'] && $dailyAward['dt'] == $today && $dailyAward['gained']) {
			return $result;
		}

		if ($dailyAward && $dailyAward['award'] && $dailyAward['dt'] == $today) {
            $result['signDay'] = $activeDays;
            if (0 == $activeDays) {
                $result['signDay'] = -1;
                $dailyAward['gained'] = 1;
                $cache->set($mckey, $dailyAward, 3600*24);
            }
		    return $result;
		}

		//generate today's award items
		$aryAward = array();
		if ($activeDays > 0) {
		    $awardId = $activeDays > 5 ? 5 : $activeDays;
		    $awardInfo = Hapyfish2_Magic_Cache_BasicInfo::getDailyAwardInfo($awardId);
		    $aryAward = array('base_award' => $awardInfo['base_award']);
		    if ($result['isfans']) {
		        $aryAward['fans_award'] = $awardInfo['fans_award'];
		    }
		}

		$dailyAward = array('dt' => $today, 'award' => $aryAward, 'gained' => 0);
        //0 $activeDays user has viewed the page
        if (0 == $activeDays) {
            $result['signDay'] = -1;
            $dailyAward['gained'] = 1;
        }
        else {
            $result['signDay'] = $activeDays;
        }
        $cache->set($mckey, $dailyAward, 3600*24);

    	return $result;
    }

	/**
     * gain awards
     *
     * @param integer $uid
     * @return array
     */
    public static function gainAwards($uid)
    {
    	$resultVo = array();

		$today = date('Ymd');
    	$mckey = self::$_mcKeyPrex . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $dailyAward = $cache->get($mckey);//$dailyAward['date'], $dailyAward['award'], $dailyAward['gained']
        if (empty($dailyAward)) {
            return Hapyfish2_Magic_Bll_UserResult::Error('daily_award_none');
        }
        if ($dailyAward && $dailyAward['award'] && $dailyAward['dt'] != $today) {
			return Hapyfish2_Magic_Bll_UserResult::Error('daily_award_error');
		}
        if ($dailyAward && $dailyAward['award'] && $dailyAward['dt'] == $today && $dailyAward['gained']) {
			return Hapyfish2_Magic_Bll_UserResult::Error('daily_award_has_gained');
		}

		$isSend = false;
		$coin = $gem = 0;
		$aryItem = array();
		$aryDecor = array();
		if (isset($dailyAward['award']['base_award'])) {
            $baseAward = json_decode($dailyAward['award']['base_award'], true);
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
        if (isset($dailyAward['award']['fans_award'])) {
            $fansAward = json_decode($dailyAward['award']['fans_award'], true);
		    if (isset($fansAward['coin'])) {
                $coin += $fansAward['coin'];
            }
            if (isset($fansAward['gem'])) {
                $gem += $fansAward['gem'];
            }
		    if (isset($fansAward['item'])) {
		        foreach ($fansAward['item'] as $item) {
                    $aryItem[] = array($item[0], $item[1]);
                }
            }
		    if (isset($fansAward['decor'])) {
		        foreach ($fansAward['decor'] as $decor) {
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
            $sendAward->setGold($gem, 1);
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

        $dailyAward['gained'] = 1;
        $cache->set($mckey, $dailyAward, 3600*24);

        //today gained user count ++
    	$basCache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
    	$rowGainAll = $basCache->get(self::$_mcGainAwardAll);
    	if (!$rowGainAll || $rowGainAll['dt'] != $today) {
    	    $rowGainAll = array('dt' => $today, 'cnt' => 0);
    	}
    	$rowGainAll['cnt'] += 1;
    	$basCache->set(self::$_mcGainAwardAll, $rowGainAll);

        return Hapyfish2_Magic_Bll_UserResult::all();
    }

	/**
	 * generate random by key=>odds
	 *
	 * @param array $aryKeys
	 * @return integer
	 */
	private static function _randomKeyForOdds($aryKeys)
	{
		$tot = 0;
		$aryTmp = array();
		foreach ($aryKeys as $key => $odd) {
			$tot += $odd;
			$aryTmp[$key] = $tot;
		}
		$rnd = mt_rand(1,$tot);

		foreach ($aryTmp as $key=>$value) {
			if ($rnd <= $value) {
				return $key;
			}
		}
	}

}