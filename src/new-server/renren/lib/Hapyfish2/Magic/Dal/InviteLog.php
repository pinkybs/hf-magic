<?php


class Hapyfish2_Magic_Dal_InviteLog
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Magic_Dal_InviteLog
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getTableName($uid)
    {
    	$id = floor($uid/DATABASE_NODE_NUM) % 10;
    	return 'magic_user_invitelog_' . $id;
    }

    public function getAll($uid)
    {
    	$tbname = $this->getTableName($uid);
    	$sql = "SELECT uid,fid,`time` FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchAll($sql, array('uid' => $uid));
    }

    public function getAllByTime($uid, $time)
    {
    	$tbname = $this->getTableName($uid);
    	$sql = "SELECT fid FROM $tbname WHERE uid=:uid AND `time`>$time ORDER BY `time`";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchAll($sql, array('uid' => $uid));
    }

    public function getCount($uid)
    {
    	$tbname = $this->getTableName($uid);
    	$sql = "SELECT COUNT(uid) AS c FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchOne($sql, array('uid' => $uid));
    }

    public function getCountByTime($uid, $time)
    {
    	$tbname = $this->getTableName($uid);
    	$sql = "SELECT COUNT(uid) AS c FROM $tbname WHERE uid=:uid AND `time`>$time";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchOne($sql, array('uid' => $uid));
    }

    public function insert($uid, $info)
    {
    	$tbname = $this->getTableName($uid);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	return $wdb->insert($tbname, $info);
    }

    public function clear($uid)
    {
        $tbname = $this->getTableName($uid);

        $sql = "DELETE FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $wdb->query($sql, array('uid' => $uid));
    }

}