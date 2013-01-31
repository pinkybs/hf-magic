<?php

class Hapyfish2_Magic_Bll_Character
{

    public static function listCharacterVo()
	{
	    $info = Hapyfish2_Magic_Bll_BasicInfo::getCharacterList();
        $result = array('rehandlingVo'=>$info);
        return json_encode($result);
	}

	public static function listUserCharacter($uid)
	{
	    $lstChar = Hapyfish2_Magic_Cache_BasicInfo::getCharacterList();
	    try {
    	    $dalChar = Hapyfish2_Magic_Dal_Character::getDefaultInstance();
    	    $lstUserChar = $dalChar->getList($uid);
	    }
	    catch (Exception $e) {
	        $lstUserChar = null;
	        info_log('listUserCharacter:'.$e->getMessage(), 'Bll_Character_Err');
	        return Hapyfish2_Magic_Bll_UserResult::Error('fatal_error');
	    }
	    if (!$lstUserChar) {
	        $lstUserChar = array();
	        //init user character
	        $curAvatar = Hapyfish2_Magic_HFC_User::getUserAvatar($uid);
            $rowCha = array();
            $rowCha['uid'] = $uid;
            $rowCha['id'] = $curAvatar['avatar_id'];
            $rowCha['create_time'] = time();
            try {
                $dalChar->insert($uid, $rowCha);
            }
    	    catch (Exception $e) {
    	        info_log('listUserCharacter:initchar:'.$e->getMessage(), 'Bll_Character_Err');
    	        return Hapyfish2_Magic_Bll_UserResult::Error('fatal_error');
    	    }
            $lstUserChar[] = $rowCha;
	    }

	    $info = array();
	    foreach ($lstChar as $data) {
	        $charId = $data['id'];
	        $lock = 1;
	        if ($lstUserChar) {
    	        foreach ($lstUserChar as $userChar) {
                    if ($charId == $userChar['id']) {
                        $lock = 0;
                        break;
                    }
    	        }
	        }
            $info[] = array(
				'avatarId' => $charId,
				'lock' => $lock
			);
	    }

        return array('rehandlingStateVo'=>$info);
	}

    public static function changeCharacter($uid, $id)
	{

	    //get current character list
	    try {
    	    $dalChar = Hapyfish2_Magic_Dal_Character::getDefaultInstance();
    	    $lstUserChar = $dalChar->getList($uid);
	    }
	    catch (Exception $e) {
	        $lstUserChar = null;
	        info_log('listUserCharacter:'.$e->getMessage(), 'Bll_Character_Err');
	    }

	    $isLock = 1;
        foreach ($lstUserChar as $userChar) {
            if ($id == $userChar['id']) {
                $isLock = 0;
                break;
            }
        }

        if ($isLock) {
            $rowChar = Hapyfish2_Magic_Cache_BasicInfo::getCharacterInfo($id);
            //cost gold
            if ($rowChar['price_type']==2) {
                $goldNeed = $rowChar['price'];
    			$userGold = Hapyfish2_Magic_HFC_User::getUserGold($uid);
    			if ($userGold < $goldNeed) {
    				return Hapyfish2_Magic_Bll_UserResult::Error('glod_not_enough');
    			}

    			$goldInfo = array(
            		'uid' => $uid,
            		'cost' => $goldNeed,
            		'summary' => '解锁换装-' . $rowChar['name']
                );
    			$rst = Hapyfish2_Magic_Bll_Gold::consume($uid, $goldInfo);
    			if (!$rst) {
    			    info_log('changeCharacter:usegoldfailed', 'Bll_Character_Err');
    			    return Hapyfish2_Magic_Bll_UserResult::Error('fatal_error');
    			}
            }
            //cost coin
            else {
                $coinNeed = $rowChar['price'];
    			$userCoin = Hapyfish2_Magic_HFC_User::getUserCoin($uid);
    			if ($userCoin < $coinNeed) {
    				return Hapyfish2_Magic_Bll_UserResult::Error('coin_not_enough');
    			}
    			$rst = Hapyfish2_Magic_HFC_User::decUserCoin($uid, $coinNeed);
                if (!$rst) {
    			    info_log('changeCharacter:usecoinfailed', 'Bll_Character_Err');
    			    return Hapyfish2_Magic_Bll_UserResult::Error('fatal_error');
    			}
            }
            $tm = time();
            $info = array();
            $info['uid'] = $uid;
            $info['id'] = $id;
            $info['create_time'] = $tm;
            try {
                $dalChar->insert($uid, $info);
            }
    	    catch (Exception $e) {
    	        info_log('changeCharacter:insert:'.$e->getMessage(), 'Bll_Character_Err');
    	        return Hapyfish2_Magic_Bll_UserResult::Error('fatal_error');
    	    }
        }

        //change char
        $curAvatar = Hapyfish2_Magic_HFC_User::getUserAvatar($uid);
        if ($curAvatar['avatar_id'] != $id) {
            $curAvatar['avatar_id'] = $id;
            Hapyfish2_Magic_HFC_User::updateUserAvatar($uid, $curAvatar, true);
        }

        $changeUser = array('avatar'=>$id, 'uid'=>$uid);
        Hapyfish2_Magic_Bll_UserResult::addField($uid, 'changeUsers', array($changeUser));
        return Hapyfish2_Magic_Bll_UserResult::all();
	}
}