<?php

class Hapyfish2_Magic_Event_Dal_Collection
{
    protected static $_instance;

    protected function getDB()
    {
    	$key = 'db_0';
    	return Hapyfish2_Db_Factory::getEventDB($key);
    }

    /**
     * Single Instance
     *
     * @return Hapyfish2_Magic_Event_Dal_Collection
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
    	$id = $uid % 10;
    	return 'magic_event_user_collection_' . $id;
    }

    public function getAllIds($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT id FROM $tbname WHERE uid=:uid";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchCol($sql, array('uid' => $uid));
    }

    public function insert($uid, $info)
    {
        $tbname = $this->getTableName($uid);

        $db = $this->getDB();
        $wdb = $db['w'];

    	return $wdb->insert($tbname, $info);
    }

    public function clear($uid)
    {
        $tbname = $this->getTableName($uid);

        $sql = "DELETE FROM $tbname WHERE uid=:uid";
        $db = $this->getDB();
        $wdb = $db['w'];

        return $wdb->query($sql, array('uid' => $uid));
    }

}