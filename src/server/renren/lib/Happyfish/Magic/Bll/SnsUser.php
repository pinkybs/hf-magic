<?php

class Happyfish_Magic_Bll_SnsUser
{
	/*
    public static function getAppUser($uid)
    {
        return Happyfish_Magic_Bll_Cache_SnsUser::isAppUser($uid);
    }
*/

	/* sns platform user */
    public static function getPerson($uid)
    {
        return Happyfish_Magic_Bll_Cache_User::getPerson($uid);
    }

    public static function getPeople($ids)
    {
        $people = array();
        foreach ($ids as $id) {
            $people[$id] = self::getPerson($id);
        }

        return $people;
    }

    public static function different($old, $new)
    {
        $diff = array();
        foreach ($old as $k => $v) {
            if (isset($new[$k]) && $new[$k] != $v) {
                $diff[$k] = $new[$k];
            }
        }

        return $diff;
    }

    public static function updatePerson($person)
    {
        if ($person == null) {
			return;
		}

        $uid = $person['uid'];
        $oldPerson = self::getPerson($uid);

        if ($oldPerson == null) {
            $dalUser = Happyfish_Magic_Dal_Mongo_SnsUser::getDefaultInstance();
            try {
                $dalUser->addPerson($person);
            }
            catch (Exception $e) {
                err_log($e->getMessage());
            }

        } else {
            $diff = self::different($oldPerson, $person);
            if (!empty($diff)) {
                $dalUser = Happyfish_Magic_Dal_Mongo_SnsUser::getDefaultInstance();
                try {
                    $dalUser->updatePerson($uid, $diff);
                    Happyfish_Magic_Bll_Cache_User::clearPerson($uid);
                }
                catch (Exception $e) {
                    err_log($e->getMessage());
                }
            }
        }
    }

    public static function updatePeople($people)
    {
        foreach ($people as $person) {
            self::updatePerson($person);
        }
    }

    public static function appendPersonData(&$data, $person = null)
    {
        if ($person == null) {
            $data['name'] = '';
            $data['face'] = '';
            $data['smallFace'] = '';
        }
        else {
            $data['name'] = $person['name'];
            $data['face'] = $person['headurl'];
            $data['smallFace'] = $person['tinyurl'];
        }
    }

    public static function appendPerson(&$data, $idKey = 'uid')
    {
        $person = self::getPerson($data[$idKey]);

        self::appendPersonData($data, $person);
    }


    public static function appendPeople(&$datas, $idKey = 'uid')
    {
        if (empty($datas)) {
            return;
        }

        foreach ($datas as &$data) {
            $person = self::getPerson($data[$idKey]);
            self::appendPersonData($data, $person);
        }
    }
	/* sns platform user */
    
    
    
    /* sns platform friends */
    //return ids array
	public static function getFriends($uid)
    {        
        return Happyfish_Magic_Bll_Cache_User::getFriends($uid);
    }
    
    //return ids string 
	public static function getFriendIds($uid)
    {
        $fids = self::getFriends($uid);
        if (empty($fids)) {
            return '';
        }
        return implode(',', $fids);
    }
    
    //return ids array
	public static function getFriendsPage($uid, $page = 1, $step = 10)
    {
        $fids = self::getFriends($uid);
        if ($fids) {
            $start = ($page -1) * $step;
            $count = count($fids);
            if ($count > 0 && $start < $count) {
                return array_slice($fids, $start, $step);
            }
        }
        return null;
    }
    
	public static function isFriend($uid, $fid)
    {
        $fids = self::getFriends($uid);
        if (empty($fids)) {
            return false;
        }
        return in_array($fid, $fids);
    }
        
    public static function updateFriends($uid, $fids)
    {
        $dalFriend = Happyfish_Magic_Dal_Mongo_SnsFriend::getDefaultInstance();
        try {
            $dalFriend->insertFriend($uid, $fids);
            Happyfish_Magic_Bll_Cache_User::clearFriends($uid);
        }
        catch (Exception $e) {
            err_log($e->getMessage());
        }
    }
    /* sns platform friends */
    
    
    
    /*
    public static function appendPeople(&$datas, $idKey = 'uid')
    {
        if (empty($datas)) {
            return;
        }

        $ids = array();
        foreach ($datas as $data) {
        	$ids[] = $data[$idKey];
        }

        $people = self::getPeople2($ids);

        foreach ($datas as &$data) {
            self::appendPersonData($data, $people[$data[$idKey]]);
        }
    }

    public static function appendPeople2(&$datas, $idKey = 'uid')
    {
        if (empty($datas)) {
            return;
        }

        $ids = array();
        foreach ($datas as $data) {
        	$ids[] = $data[$idKey];
        }

        $people = self::getPeople2($ids);

        foreach ($datas as &$data) {
            self::appendPersonData($data, $people[$data[$idKey]]);
        }
    }

    public static function search($people, $uid)
    {
        foreach($people as $person) {
            if ($person['uid'] == $uid) {
                return $person;
            }
        }

        return null;
    }*/
}