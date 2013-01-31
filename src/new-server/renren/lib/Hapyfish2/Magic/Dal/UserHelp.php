<?php

class Hapyfish2_Magic_Dal_UserHelp
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Magic_Dal_UserHelp
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
    	return 'magic_user_info_' . $id;
    }

    public function get($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT help FROM $tbname WHERE uid=:uid";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchRow($sql, array('uid' => $uid));
    }

    public function update($uid, $info)
    {
        $tbname = $this->getTableName($uid);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	$where = $wdb->quoteinto('uid = ?', $uid);

        return $wdb->update($tbname, $info, $where);
    }

}