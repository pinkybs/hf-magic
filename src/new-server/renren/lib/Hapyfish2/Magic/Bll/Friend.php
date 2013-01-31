<?php

class Hapyfish2_Magic_Bll_Friend
{
	public static function getRankList($uid, $pageIndex = 1, $pageSize = 50)
	{
		$friendList = array();
		$friendList[] = array(
			'uid' => GM_UID_LELE,
			'name' => GM_NAME_LELE,
			'face' => STATIC_HOST . '/img/magic/' . GM_FACE_LELE,
			'exp' => 999999999,
			'level' => 99
		);

		$fids = Hapyfish2_Platform_Bll_Friend::getFriendIds($uid);
		if ($fids) {
			foreach ($fids as $fid) {
				$userInfo = Hapyfish2_Magic_HFC_User::getUser($fid, array('exp' => 1, 'level' => 1));
				if ($userInfo) {
					$info = Hapyfish2_Platform_Bll_User::getUser($fid);
					$friendList[] = array(
						'uid' => $fid,
						'name' => $info['name'],
						'face' => $info['figureurl'],
						'exp' => $userInfo['exp'],
						'level' => $userInfo['level']
					);
				}
			}
		}

		return array('friends' => $friendList, 'maxPage' => 1);
	}


    public static function getFriendList($uid, $pageIndex = 1, $pageSize = 50)
	{
		$friendList = array();

		$fids = Hapyfish2_Platform_Bll_Friend::getFriendIds($uid);
        if ($fids) {
    		foreach ($fids as $fid) {
    			$userInfo = Hapyfish2_Magic_HFC_User::getUser($fid, array('exp' => 1, 'level' => 1));
    			if ($userInfo) {
    				$info = Hapyfish2_Platform_Bll_User::getUser($fid);
    				$friendList[] = array(
    					'uid' => $fid,
    					'name' => $info['name'],
    					'face' => $info['figureurl'],
    					'exp' => $userInfo['exp'],
    					'level' => $userInfo['level']
    				);
    			}
    		}
        }

		return array('friends' => $friendList, 'maxPage' => 1);
	}
}