<?php

class Hapyfish2_Magic_Bll_Monster
{
	public static function getInScene($uid, $sceneId)
	{
		//在HOME场景，不显示怪
		if ($sceneId == HOME_SCENE_ID) {
			return array();
		}

		$data = Hapyfish2_Magic_Cache_Monster::getUser($uid, $sceneId);

		$list = array();
		if ($data) {
			foreach ($data as $m) {
				if ($m[2] == 0) {
					$list[] = array(
						'enemyId' => $m[0],
						'enemyCid' => $m[1]
					);
				}
			}
		}

    	return $list;
	}

	public static function kill($uid, $enemyId)
	{
		$userSceneInfo = Hapyfish2_Magic_HFC_User::getUserScene($uid);
		$sceneId = $userSceneInfo['cur_scene_id'];
		if ($sceneId == HOME_SCENE_ID) {
			return Hapyfish2_Magic_Bll_UserResult::Error('scene_id_error');
		}

		$data = Hapyfish2_Magic_Cache_Monster::getUser($uid, $sceneId);
		if (!$data) {
			return Hapyfish2_Magic_Bll_UserResult::Error('no_monster');
		}

		if (!isset($data[$enemyId])) {
			return Hapyfish2_Magic_Bll_UserResult::Error('monster_id_error');
		}

		$monter = $data[$enemyId];
		if ($monter[2] == 1) {
			return Hapyfish2_Magic_Bll_UserResult::Error('monster_died');
		}

		$monterInfo = Hapyfish2_Magic_Cache_BasicInfo::getMonsterInfo($monter[1]);
		if (!$monterInfo) {
			return Hapyfish2_Magic_Bll_UserResult::Error('monster_info_error');
		}

		$userMpInfo = Hapyfish2_Magic_HFC_User::getUserMp($uid);
		if ($userMpInfo['mp'] < $monterInfo['mp']) {
			return Hapyfish2_Magic_Bll_UserResult::Error('mp_not_enough');
		}

		$monter[2] = 1;
		$monter[3] = time();
		$data[$enemyId] = $monter;
		Hapyfish2_Magic_Cache_Monster::updateUser($uid, $sceneId, $data);

		//减少MP
		Hapyfish2_Magic_HFC_User::decUserMp($uid, $monterInfo['mp']);

		//获得奖励
		Hapyfish2_Magic_HFC_User::incUserExpAndCoin($uid, $monterInfo['exp'], $monterInfo['coin']);

		//概率掉落物品(item)
		$items = json_decode($monterInfo['drop_items'], true);
		$addItemList = array();
		foreach ($items as $item) {
			if (1 == mt_rand(1, $item[1])) {
				$addItemList[] = array($item[0], 1);
			}
		}

		if (!empty($addItemList)) {
			$awardRot = new Hapyfish2_Magic_Bll_Award();
			$awardRot->setItemList($addItemList);
			$awardRot->sendOne($uid);
		}

		return Hapyfish2_Magic_Bll_UserResult::all();
	}
}