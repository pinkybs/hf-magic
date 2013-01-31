<?php

class Hapyfish2_Magic_Bll_Item
{
	public static function useItem($uid, $itemId)
	{
		$itemBasicInfo = Hapyfish2_Magic_Cache_BasicInfo::getItemInfo($itemId);
		if (!$itemBasicInfo) {
			return Hapyfish2_Magic_Bll_UserResult::Error('item_use_error');
		}

		$itemType = $itemBasicInfo['type'];
		if ($itemType != 1 && $itemType != 2) {
			return Hapyfish2_Magic_Bll_UserResult::Error('item_use_error');
		}

		$userItem = Hapyfish2_Magic_HFC_Item::getUserItem($uid);
		if (!isset($userItem[$itemId]) || $userItem[$itemId]['count'] < 1) {
			return Hapyfish2_Magic_Bll_UserResult::Error('item_not_enough');
		}

		/*
		//check today use count
		$todayUnixTime = strtotime(date('Y-m-d'), time());
		if ( $userItem['last_use_time'] >= $todayUnixTime ) {
			if ( $userItem['today_use_count'] >= $itemBasic['limit_time'] ) {
				$result['content'] = 'item_limit_time';
				return $result;
			}
			$newTodayUseCount = $userItem['today_use_count'] + 1;
		}
		else {
			$newTodayUseCount = 1;
		}*/

		//start use item
		$ok = Hapyfish2_Magic_HFC_Item::useUserItem($uid, $itemId);
		$mpChange = (int)$itemBasicInfo['add_mp'];
		if ($ok) {
			Hapyfish2_Magic_HFC_User::incUserMp($uid, $mpChange);
		}

		return Hapyfish2_Magic_Bll_UserResult::all();
	}

	public static function buyItem($uid, $itemId, $num = 1)
	{
		$itemBasicInfo = Hapyfish2_Magic_Cache_BasicInfo::getItemInfo($itemId);
		if (!$itemBasicInfo) {
			return Hapyfish2_Magic_Bll_UserResult::Error('item_id_error');
		}

		if ($itemBasicInfo['canbuy'] != 1) {
			return Hapyfish2_Magic_Bll_UserResult::Error('item_not_can_buy');
		}

		$coinNeed = 0;
		if ($itemBasicInfo['coin'] > 0) {
			$coinNeed = $itemBasicInfo['coin']*$num;
			$userCoin = Hapyfish2_Magic_HFC_User::getUserCoin($uid);
			if ($userCoin < $coinNeed) {
				return Hapyfish2_Magic_Bll_UserResult::Error('coin_not_enough');
			}
		}

		$goldNeed = 0;
		if ($itemBasicInfo['gold'] > 0) {
			$goldNeed = $itemBasicInfo['gold']*$num;
			$userGold = Hapyfish2_Magic_HFC_User::getUserGold($uid);
			if ($userGold < $goldNeed) {
				return Hapyfish2_Magic_Bll_UserResult::Error('glod_not_enough');
			}
		}

		if ($goldNeed > 0) {
        	$goldInfo = array(
        		'uid' => $uid,
        		'cost' => $goldNeed,
        		'summary' => '购买' . $num . '个' . $itemBasicInfo['name'],
        		'cid' => $itemId,
        		'num' => $num
        	);
			$ok = Hapyfish2_Magic_Bll_Gold::consume($uid, $goldInfo);
			if ($ok) {
				$ok2 = Hapyfish2_Magic_HFC_Item::addUserItem($uid, $itemId, $num);
				if ($ok2) {

				}
			}
			else {
			    return Hapyfish2_Magic_Bll_UserResult::Error('buy_failed');
			}
		} else if ($coinNeed > 0) {
			$ok = Hapyfish2_Magic_HFC_User::decUserCoin($uid, $coinNeed, true);
			if ($ok) {
				$ok2 = Hapyfish2_Magic_HFC_Item::addUserItem($uid, $itemId, $num);
				if ($ok2) {

				}
			}
		    else {
			    return Hapyfish2_Magic_Bll_UserResult::Error('buy_failed');
			}
		}

		return Hapyfish2_Magic_Bll_UserResult::all();
	}
}