<?php


class Hapyfish2_Magic_Dal_TaskDaily
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Magic_Dal_TaskDaily
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
    	return 'magic_user_task_daily_' . $id;
    }

    public function get($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT today,tids FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchRow($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }

    public function init($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "INSERT INTO $tbname(uid, today) VALUES(:uid, 0)";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        return $wdb->query($sql, array('uid' => $uid));
    }

    public function insert($uid, $tids, $today)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "INSERT INTO $tbname(uid, today, tids) VALUES(:uid, :today, :tids) ON DUPLICATE KEY UPDATE today=:today, tids=:tids";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        return $wdb->query($sql, array('uid' => $uid, 'today' => $today, 'tids' => $tids));
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