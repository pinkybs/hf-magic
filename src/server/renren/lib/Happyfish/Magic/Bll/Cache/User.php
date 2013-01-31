<?php

/**
 * Cache For User
 *
 * @package    Happyfish/Magic/Bll/Cache
 * @copyright  Copyright (c) 2008 Community Factory Inc. (http://communityfactory.com)
 * @create      2010/01/20    Hulj
*/
class Happyfish_Magic_Bll_Cache_User
{
    const PREFIX_NAME = '_Happyfish_Magic_Bll_Cache_User_';
    
	/**
     * clear cache info
     *
     * @param string $name
     * @param string $param
     */
    private static function _clear($key)
    {
        $cache = Happyfish_Cache_Memcached::getInstance();
        $cache->delete($key);
    }
    
	public static function getPerson($uid)
    {
    	$key = SNS_PLATFORM . self::PREFIX_NAME . 'getPerson_' . $uid;
        $cache = Happyfish_Cache_Memcached::getInstance();
        if (!$result = $cache->get($key)) {
            $dalUser = Happyfish_Magic_Dal_Mongo_SnsUser::getDefaultInstance();
            $result = $dalUser->getPerson($uid);
			$cache->add($key, $result, Happyfish_Cache_Memcached::LIFE_TIME_ONE_MONTH);
        }
        return $result;
    }
    
    public static function clearPerson($uid) 
    {
    	$key = SNS_PLATFORM . self::PREFIX_NAME . 'getPerson_' . $uid;
    	self::_clear($key);
    }
    
    public static function getFriends($uid)
    {
        $key = SNS_PLATFORM . self::PREFIX_NAME . 'getFriends_' . $uid;
        $cache = Happyfish_Cache_Memcached::getInstance();
        if (!$result = $cache->get($key)) {
            $dalFriend = Happyfish_Magic_Dal_Mongo_SnsFriend::getDefaultInstance();
            $result = $dalFriend->getFriends($uid);
			$cache->add($key, $result, Happyfish_Cache_Memcached::LIFE_TIME_ONE_MONTH);
        }

        return $result;
    }
    public static function clearFriends($uid) 
    {
    	$key = SNS_PLATFORM . self::PREFIX_NAME . 'getFriends_' . $uid;
    	self::_clear($key);
    }
    
	public static function getAppUser($uid)
    {
        $key = SNS_PLATFORM . self::PREFIX_NAME . 'getAppUser_' . $uid;
        $cache = Happyfish_Cache_Memcached::getInstance();
        if (!$result = $cache->get($key)) {
            $dalUser = Happyfish_Magic_Dal_User::getDefaultInstance();
            $result = $dalUser->getUser($uid);
			$cache->add($key, $result, Happyfish_Cache_Memcached::LIFE_TIME_ONE_WEEK);
        }
        return $result;
    }

    public static function clearAppUser($uid) 
    {
    	$key = SNS_PLATFORM . self::PREFIX_NAME . 'getAppUser_' . $uid;
    	self::_clear($key);
    }
    
	public static function lstUserMagic($uid)
    {
        $key = SNS_PLATFORM . self::PREFIX_NAME . 'lstUserMagic_' . $uid;
        $cache = Happyfish_Cache_Memcached::getInstance();
        if (!$result = $cache->get($key)) {
            $dalMagic = Happyfish_Magic_Dal_Magic::getDefaultInstance();
            $lstMag = $dalMagic->lstUserMagic($uid);
            $result = array();
            foreach ($lstMag as $magData) {
            	$magKey = $magData['uid'] . '_' . $magData['magic_id'];
            	$result[$magKey] = $magData;
            }
			$cache->add($key, $result, Happyfish_Cache_Memcached::LIFE_TIME_ONE_MONTH);
        }
        return $result;
    }

    public static function clearUserMagic($uid) 
    {
    	$key = SNS_PLATFORM . self::PREFIX_NAME . 'lstUserMagic_' . $uid;
    	self::_clear($key);
    }
    
	public static function lstUserCard($uid)
    {
        $key = SNS_PLATFORM . self::PREFIX_NAME . 'lstUserCard_' . $uid;
        $cache = Happyfish_Cache_Memcached::getInstance();
        if (!$result = $cache->get($key)) {
            $dalCard = Happyfish_Magic_Dal_Card::getDefaultInstance();
            $lstCard = $dalCard->lstUserCard($uid);
            $result = array();
            foreach ($lstCard as $data) {
            	$cardKey = $data['uid'] . '_' . $data['cid'];
            	$result[$cardKey] = $data;
            }
			$cache->add($key, $result, Happyfish_Cache_Memcached::LIFE_TIME_ONE_MONTH);
        }
        return $result;
    }

    public static function clearUserCard($uid) 
    {
    	$key = SNS_PLATFORM . self::PREFIX_NAME . 'lstUserCard_' . $uid;
    	self::_clear($key);
    }
    
    //ranking
	public static function getRanking($uid)
    {
        $key = SNS_PLATFORM . self::PREFIX_NAME . 'lstRanking_' . $uid;
        $cache = Happyfish_Cache_Memcached::getInstance();
        return $cache->get($key);
    }
    
	public static function setRanking($uid, $aryRank)
    {
        $key = SNS_PLATFORM . self::PREFIX_NAME . 'lstRanking_' . $uid;
        $cache = Happyfish_Cache_Memcached::getInstance();
        $cache->delete($key);
        return $cache->add($key, $aryRank, Happyfish_Cache_Memcached::LIFE_TIME_ONE_HOUR);
    }
    
	public static function clearRanking($uid) 
    {
    	$key = SNS_PLATFORM . self::PREFIX_NAME . 'lstRanking_' . $uid;
    	self::_clear($key);
    }
    
    
    
   /* 
	public static function getCacheKey($salt, $params = null)
    {
    	$key = SNS_PLATFORM . '_' . self::PREFIX_NAME . '_' . $salt . '_';
        if ($params != null) {
            if (is_array($params)) {
                $key .= implode('_', $params);
            }
            else {
                $key .= $params;
            }
        }
        return $key;
    }

    
    
    public static function getPeople($uids)
    {
        $keys = array();
        $idCache = array();
        foreach ($uids as $uid) {
            $key = Happyfish_Magic_Bll_Cache_User::getCacheKey('Person', $uid);
        	$keys[] = $key;
        	$idCache[$key] = $uid;
        }

        $data = Happyfish_Magic_Bll_Cache::getMutilple($keys);
        $result = array();
        if ($data) {
            foreach ($data as $k => $v) {
                $result[$idCache[$k]] = $v;
            }
        }

        if (count($result) < count($uids)) {
            $dalUser = Happyfish_Magic_Dal_Mongo_SnsUser::getDefaultInstance();
            foreach ($uids as $uid) {
                if(!isset($result[$uid])) {
                    $d = $dalUser->getPerson($uid);
                    $result[$uid] = $d;
                    $key = Happyfish_Magic_Bll_Cache_User::getCacheKey('Person', $uid);
                    Happyfish_Magic_Bll_Cache::set($key, $d, Happyfish_Magic_Bll_Cache::LIFE_TIME_ONE_MONTH);
                }
            }
        }

        return $result;
    }

    public static function updatePerson($uid, $user)
    {
        $key = self::getCacheKey('Person', $uid);
        Happyfish_Magic_Bll_Cache::set($key, $user, Happyfish_Magic_Bll_Cache::LIFE_TIME_ONE_MONTH);
    }


    public static function updateFriends($uid, $fids)
    {
        $key = self::getCacheKey('Friends', $uid);
        Happyfish_Magic_Bll_Cache::set($key, $fids, Happyfish_Magic_Bll_Cache::LIFE_TIME_ONE_DAY);
    }

    public static function isUpdated($uid)
    {
        $key = self::getCacheKey('isUpdated', $uid);

        if (!Happyfish_Magic_Bll_Cache::get($key)) {
            return false;
        }

        return true;
    }

    public static function setUpdated($uid)
    {
        $key = self::getCacheKey('isUpdated', $uid);

        Happyfish_Magic_Bll_Cache::set($key, '1', Happyfish_Magic_Bll_Cache::LIFE_TIME_ONE_HOUR * 3);
    }

    public static function isFriendsUpdated($uid)
    {
        $key = self::getCacheKey('isFriendsUpdated', $uid);

        if (!Happyfish_Magic_Bll_Cache::get($key)) {
            return false;
        }

        return true;
    }

    public static function setFriendsUpdated($uid)
    {
        $key = self::getCacheKey('isFriendsUpdated', $uid);

        Happyfish_Magic_Bll_Cache::set($key, '1', Happyfish_Magic_Bll_Cache::LIFE_TIME_ONE_MINUTE * 15);
    }

    public static function getRenrenFriends($uid, $session)
    {
        $key = self::getCacheKey('getRenrenFriends', $uid);

        if (!$result = Happyfish_Magic_Bll_Cache::get($key)) {
            $renren = Xiaonei_Renren::getInstance();
            $renren->setUser($uid, $session);
            $result = $renren->getFriends();

            if ($result) {
                Happyfish_Magic_Bll_Cache::set($key, $result, Happyfish_Magic_Bll_Cache::LIFE_TIME_ONE_MINUTE * 15);
            }
        }

        return $result;
    }

    public static function getRenrenNotJoinFriends($uid, $session)
    {
        $renrenfriends = self::getRenrenFriends($uid, $session);

        if ($renrenfriends) {
            $dalRank = Happyfish_Magic_Dal_Island_Rank::getDefaultInstance();
            $allfids = array_keys($renrenfriends);
            $result = $dalRank->getUserJoinFriends($allfids);
            if ($result) {
                $fids = array();
                foreach($result as $row) {
                    $fids[] = $row['uid'];
                }
                $notfids = array_diff($allfids, $fids);
                if ($notfids) {
                    $notjoinfriends = array();
                    foreach ($notfids as $id) {
                        $notjoinfriends[] = $renrenfriends[$id];
                    }

                    return $notjoinfriends;
                } else {
                    return array();
                }
            } else {
                return array_values($renrenfriends);
            }
        }

        return array();
    }

    public static function cleanPerson($uid)
    {
        Happyfish_Magic_Bll_Cache::delete(self::getCacheKey('Person', $uid));
    }

    public static function cleanPeople($ids)
    {
        if (count($ids) > 0) {
            foreach ($ids as $id) {
                self::cleanPerson($id);
            }
        }
    }

    public static function cleanFriends($uid)
    {
        Happyfish_Magic_Bll_Cache::delete(self::getCacheKey('Friends', $uid));
    }

    public static function cleanMultiFriends($ids)
    {
        foreach ($ids as $id) {
            self::cleanFriends($id);
        }
    }
*/
}