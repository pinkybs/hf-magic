<?php

/**
 * logic's Operation
 *
 * @package    Happyfish_Magic_Bll
 * @copyright  Copyright (c)
 * @create      2010/08/19    zhangxin
 */
class Happyfish_Magic_Bll_Message
{

	/**
	 * get user message
	 *
	 * @param integer $uid
	 * @return array
	 */
	public static function getUserMessage($uid)
	{
		$dalMgMsg = Happyfish_Magic_Dal_Mongo_UserMessage::getDefaultInstance();
		$lstData = $dalMgMsg->lstMessage($uid, 1, 100);
		$lstMsg = array();
		foreach ($lstData as $key=>$vdata) {
			$lstMsg[$key]['uid'] = $vdata['uid'];
			//get message template
			$rowNb = Happyfish_Magic_Bll_Cache_NbBasicInfo::getNbMessage($vdata['template']);
			$lstMsg[$key]['mood'] = $rowNb['mood'];// 好情绪 /开心
			$lstMsg[$key]['message'] = $rowNb['template'];
			$keys = array();
			$values = array();
			//combine message parameters
			foreach ($vdata['properties'] as $k => $v) {
                $keys[] = '{*' . $k . '*}';
                $values[] = $v;
            }
            $lstMsg[$key]['message'] = str_replace($keys, $values, $lstMsg[$key]['message']);
		}
		return $lstMsg;
	}

	/**
	 * add user message
	 *
	 * @param integer $actorUid 
	 * @param array $aryInfo
	 * @return boolean
	 */
	public static function addUserMessage($aryInfo)
	{
		if ( empty($aryInfo) || 0 == count($aryInfo) ) {
			return false;
		}
		try {
			$dalMgMsg = Happyfish_Magic_Dal_Mongo_UserMessage::getDefaultInstance();
			if (1 == count($aryInfo)) {
	    		$dalMgMsg->insert($aryInfo[0]);
			}
			else {
				$dalMgMsg->batchInsert($aryInfo);
			}
		}
		catch (Exception $e) {
            info_log($uid.'[Happyfish_Magic_Bll_Message]-[addUserMessage]:'.$e->getMessage(), 'err-Message-catched');
            info_log($uid.'[Happyfish_Magic_Bll_Message]-[addUserMessage]:'.$e->getTraceAsString(), 'err-Message-catched');
            return false;
		}
		return true;
	}

}