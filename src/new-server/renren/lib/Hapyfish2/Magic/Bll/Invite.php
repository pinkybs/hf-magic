<?php

class Hapyfish2_Magic_Bll_Invite
{
	public static function add($inviteUid, $newUid, $time = null)
	{
		if (!$time) {
			$time = time();
		}

		$ok = Hapyfish2_Magic_Bll_InviteLog::add($inviteUid, $newUid, $time);

		if ($ok) {
		    //achievement update
    		Hapyfish2_Magic_HFC_Achievement::updateUserAchievementByField($inviteUid, 'num_1', 1);

    		//send invite complete award
            if (!self::_sendInviteAward($inviteUid)) {
                info_log('invite sent award failed:' . $inviteUid . '->' . $newUid, 'Hapyfish2_Magic_Bll_Invite');
            }

    		//insert minifeed
            $rowUser = Hapyfish2_Platform_Bll_User::getUser($newUid);
            if ($rowUser) {
        		$feed = array(
        			'uid' => $inviteUid,
        			'template_id' => 5,
        			'actor' => $newUid,
        			'target' => $inviteUid,
        			'type' => 1,//1好友 2系统
        			'icon' => 1,//1笑脸 2哭脸
        			'title' => array('actor' => $rowUser['name'], 'item_name' => '1000魔币'),
        			'create_time' => time()
        		);
        		Hapyfish2_Magic_Bll_Feed::insertMiniFeed($feed);
            }
		}
		else {
		    info_log('invite log insert failed:' . $inviteUid . '->' . $newUid, 'Hapyfish2_Magic_Bll_Invite');
		}

		return true;
	}

	private static function _sendInviteAward($uid)
	{
	    //1000金币
	    $awardRot = new Hapyfish2_Magic_Bll_Award();
	    $awardRot->setCoin(1000);
	    $awardRot->sendOne($uid);

	    return true;
	}

}