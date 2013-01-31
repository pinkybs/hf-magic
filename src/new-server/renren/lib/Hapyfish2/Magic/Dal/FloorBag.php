<?php


class Hapyfish2_Magic_Dal_FloorBag
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Magic_Dal_FloorBag
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
    	$id = floor($uid/DATABASE_NODE_NUM) % 20;
    	return 'magic_user_floor_inbag_' . $id;
    }

    public function get($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT floor_id,num FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchPairs($sql, array('uid' => $uid));
    }

    public function update($uid, $floorId, $num)
    {
        $tbname = $this->getTableName($uid);
        $sql = "INSERT INTO $tbname (uid, floor_id, num) VALUES($uid, $floorId, $num) ON DUPLICATE KEY UPDATE num=$num";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $wdb->query($sql);
    }

    public function init($uid, $list)
    {
        $tbname = $this->getTableName($uid);

        $tmp = array();
        foreach ($list as $k => $v) {
        	$tmp[] = "(:uid, $k, $v)";
        }
        $sql = "INSERT INTO $tbname(uid, floor_id, num) VALUES " . join(',', $tmp);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $wdb->query($sql, array('uid' => $uid));
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