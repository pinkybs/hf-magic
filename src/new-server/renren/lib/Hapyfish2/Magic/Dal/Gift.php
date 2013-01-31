<?php

class Hapyfish2_Magic_Dal_Gift
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Magic_Dal_Gift
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getTableNameBag($uid)
    {
    	$id = floor($uid/DATABASE_NODE_NUM) % 10;
    	return 'magic_user_gift_bag_' . $id;
    }

    public function getTableNameWish($uid)
    {
    	$id = floor($uid/DATABASE_NODE_NUM) % 10;
    	return 'magic_user_gift_wish_' . $id;
    }

    public function getTableNameFriendWish($uid)
    {
    	$id = floor($uid/DATABASE_NODE_NUM) % 10;
    	return 'magic_user_gift_friend_wish_' . $id;
    }


    /* basic db table
   	update magic_gift g , magic_building b set g.name=b.name,g.class_name=b.class_name where g.gid=b.id and g.type=2;
	update magic_gift g , magic_item i set g.name=i.name,g.class_name=i.class_name where g.gid=i.id and g.type=1;
    */
    //
    public function getBasicGiftList()
    {
        $db = Hapyfish2_Db_Factory::getBasicDB('db_0');
        $rdb = $db['r'];
    	$sql = "SELECT * FROM magic_gift ORDER BY sort";
        return $rdb->fetchAssoc($sql);
    }
    /*
    basic db table end
    */

    public function getBagList($uid)
    {
        $expireTm = time() - 3600*24*7;
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        $tbname = $this->getTableNameBag($uid);
    	$sql = "SELECT * FROM $tbname WHERE uid=:uid AND (`status`=0 OR `status`=1) AND create_time>$expireTm ORDER BY create_time DESC LIMIT 0,200";
        return $rdb->fetchAll($sql, array('uid' => $uid));
    }

    public function getBagInfo($uid, $fromUid, $dt, $method)
    {
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        $tbname = $this->getTableNameBag($uid);
    	$sql = "SELECT * FROM $tbname WHERE uid=:uid AND from_uid=:from_uid AND `date`=:date AND method=:method";
        return $rdb->fetchRow($sql, array('uid'=>$uid, 'from_uid'=>$fromUid, 'date'=>$dt, 'method'=>$method));
    }

    public function insertBag($uid, $info)
    {
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $tbname = $this->getTableNameBag($uid);
    	return $wdb->insert($tbname, $info);
    }

    public function updateBag($uid, $fromUid, $dt, $method, $info)
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $tbname = $this->getTableNameBag($uid);
        $where = array(
            $wdb->quoteinto('uid = ?', $uid),
            $wdb->quoteinto('from_uid = ?', $fromUid),
            $wdb->quoteinto('date = ?', $dt),
            $wdb->quoteinto('method = ?', $method)
        );
        return $wdb->update($tbname, $info, $where);
    }

    public function updateBagStatus($uid, $fromUid, $dt, $method, $status)
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $tbname = $this->getTableNameBag($uid);
        $sql = "UPDATE $tbname SET `status`=:status WHERE uid=:uid AND from_uid=:from_uid AND `date`=:date AND method=:method";
        return $wdb->query($sql, array('status'=>$status, 'uid'=>$uid, 'from_uid'=>$fromUid, 'date'=>$dt, 'method'=>$method));
    }

    public function deleteBag($uid)
    {
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $tbname = $this->getTableNameBag($uid);
        $sql = "DELETE FROM $tbname WHERE uid=:uid";
        return $wdb->query($sql, array('uid' => $uid));
    }


    public function getWish($uid)
    {
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        $tbname = $this->getTableNameWish($uid);
    	$sql = "SELECT * FROM $tbname WHERE uid=:uid";
        return $rdb->fetchRow($sql, array('uid' => $uid));
    }

    public function insertWish($uid, $info)
    {
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $tbname = $this->getTableNameWish($uid);
    	return $wdb->insert($tbname, $info);
    }

    public function updateWish($uid, $info)
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $tbname = $this->getTableNameWish($uid);
        $where = $wdb->quoteinto('uid = ?', $uid);
        return $wdb->update($tbname, $info, $where);
    }

    public function deleteWish($uid)
    {
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $tbname = $this->getTableNameWish($uid);
        $sql = "DELETE FROM $tbname WHERE uid=:uid";
        return $wdb->query($sql, array('uid' => $uid));
    }

    public function getFriendWishList($uid)
    {
        $todayTm = strtotime(date('Ymd'));
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        $tbname = $this->getTableNameFriendWish($uid);
    	$sql = "SELECT * FROM $tbname WHERE uid=:uid AND create_time>=:tm ORDER BY create_time DESC LIMIT 0,200";
        return $rdb->fetchAll($sql, array('uid' => $uid, 'tm' => $todayTm));
    }

    public function getFriendWish($uid, $fromUid)
    {
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        $tbname = $this->getTableNameFriendWish($uid);
    	$sql = "SELECT * FROM $tbname WHERE uid=:uid AND from_uid=:from_uid";
        return $rdb->fetchRow($sql, array('uid' => $uid, 'from_uid' => $fromUid));
    }

    public function insertFriendWish($uid, $info)
    {
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $tbname = $this->getTableNameFriendWish($uid);
    	return $wdb->insert($tbname, $info);
    }

    public function updateFriendWish($uid, $fromUid, $info)
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $tbname = $this->getTableNameFriendWish($uid);
        $where = array(
            $wdb->quoteinto('uid = ?', $uid),
            $wdb->quoteinto('from_uid = ?', $fromUid)
        );
        return $wdb->update($tbname, $info, $where);
    }

    public function deleteFriendWish($uid)
    {
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $tbname = $this->getTableNameFriendWish($uid);
        $sql = "DELETE FROM $tbname WHERE uid=:uid";
        return $wdb->query($sql, array('uid' => $uid));
    }

}