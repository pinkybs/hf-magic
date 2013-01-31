<?php

class Hapyfish2_Magic_Event_Bll_Collection extends Hapyfish2_Magic_Event_Abstract
{

    public static $_mcKeyPrexColl = 'm:u:e:collect:';

    public static function getUserCollect($uid)
    {
        $mcKey = self::$_mcKeyPrexColl . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $collectIds = $cache->get($mcKey);

        if ($collectIds === false) {
        	try {
	            $dal = Hapyfish2_Magic_Event_Dal_Collection::getDefaultInstance();
	            $ids = $dal->getAllIds($uid);
	            if ($ids) {
	                $collectIds = implode(',', $ids);
	            	$cache->add($mcKey, $collectIds);
	            } else {
	            	return null;
	            }
        	} catch (Exception $e) {
        		return null;
        	}
        }
        return $collectIds;
    }

    public static function saveUserCollect($uid, $id)
    {
        $mcKey = self::$_mcKeyPrexColl . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $collectIds = $cache->get($mcKey);

        try {
            $info = array();
            $info['uid'] = $uid;
            $info['id'] = $id;
            $info['create_time'] = time();
            $dal = Hapyfish2_Magic_Event_Dal_Collection::getDefaultInstance();
            $dal->insert($uid, $info);

            if ($collectIds) {
                $collectIds = ',' . $id;
            }
            else {
                $collectIds = $id;
            }
            $cache->set($mcKey, $collectIds);
        }
        catch (Exception $e) {
            info_log('saveUserCollect:'.$uid.':'.$id.':'.$e->getMessage(), 'err_Event_Bll_Collection');
            return false;
        }
        return true;
    }

    public static function reloadUserCollect($uid)
    {
        $mcKey = self::$_mcKeyPrexColl . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);

    	try {
    	    $cache->delete($mcKey);
            $dal = Hapyfish2_Magic_Event_Dal_Collection::getDefaultInstance();
            $ids = $dal->getAllIds($uid);
            if ($ids) {
                $collectIds = implode(',', $ids);
            	$cache->set($mcKey, $collectIds);
            } else {
            	return null;
            }
    	} catch (Exception $e) {
    		return null;
    	}

        return $collectIds;
    }

    public static function exchange($eCode, $uid, $id)
	{
	    //eventCode='2011Xmas'
        $event = Hapyfish2_Magic_Bll_Act::$actEvent[$eCode];
        $eCode = $event[0];
        $eName = $event[1];
        $eStart = strtotime($event[2]);
        $eEnd = strtotime($event[3]);

	    $evtCollection = new Hapyfish2_Magic_Event_EvtCollect($eCode, $eName, $eStart, $eEnd);
        if (!$evtCollection->checkAvailLife()) {
            return Hapyfish2_Magic_Bll_UserResult::Error('event_overtime');
        }

        $rowExchange = Hapyfish2_Magic_Event_Cache_Basic::getCollectionInfo($id, $eCode);
        if (!$rowExchange) {
            return Hapyfish2_Magic_Bll_UserResult::Error('event_exchange_notfound');
        }

	    $collectIds = self::getUserCollect($uid);
        if ($collectIds) {
            $aryCollectIds = explode(',', $collectIds);
            if (in_array($id, $aryCollectIds)) {
                return Hapyfish2_Magic_Bll_UserResult::Error('event_exchange_gained');
            }
        }

        //event collection
        $exgCon = array();
        $exgCon['id'] = $rowExchange['id'];
        $exgCon['need'] = json_decode($rowExchange['need_condition'], true);
        $exgCon['for'] = json_decode($rowExchange['for_condition'], true);
        $evtCollection->setExchangeCondition($exgCon);
        $rst = $evtCollection->exchange($uid, $id);
        if ($rst != 1) {
            info_log('exchange:'.$uid.':'.$id.':failed1:'.$rst, 'err_Event_Bll_Collection');
            if (-2 == $rst) {
                return Hapyfish2_Magic_Bll_UserResult::Error('item_not_enough');
            }
            else {
                return Hapyfish2_Magic_Bll_UserResult::Error('event_exchange_failed');
            }
        }

	    //update status
	    $ok = self::saveUserCollect($uid, $id);
	    if (!$ok) {
            self::reloadUserCollect($uid);
            return Hapyfish2_Magic_Bll_UserResult::Error('event_exchange_gained');
	    }

        return Hapyfish2_Magic_Bll_UserResult::all();
	}

	public static function rndDrop($eCode, $uid)
	{
	    //eventCode='2011Xmas'
        $event = Hapyfish2_Magic_Bll_Act::$actEvent[$eCode];
        $eCode = $event[0];
        $eName = $event[1];
        $eStart = strtotime($event[2]);
        $eEnd = strtotime($event[3]);
        $rndItem = isset($event[4]) ? $event[4] : '[]';

	    $evtCollection = new Hapyfish2_Magic_Event_EvtCollect($eCode, $eName, $eStart, $eEnd);
        if (!$evtCollection->checkAvailLife()) {
            return false;
        }

        if (!$rndItem) {
            return false;
        }

        $aryCon = json_decode($rndItem, true);
        $rst = 0;
        if ($aryCon) {
            $rst = $evtCollection->randDrop($uid, $aryCon);
        }
	    if ($rst != 1) {
            info_log('rndDrop:'.$uid.':'.$rst, 'err_Event_Bll_Collection');
            return false;
        }
        return true;
	}

}