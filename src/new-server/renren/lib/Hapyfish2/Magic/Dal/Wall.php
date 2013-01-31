<?php


class Hapyfish2_Magic_Dal_Wall
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Magic_Dal_Wall
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
    	return 'magic_user_wall_' . $id;
    }

    public function get($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT data FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchOne($sql, array('uid' => $uid));
    }

    public function insert($uid, $data)
    {
    	$tbname = $this->getTableName($uid);

        $sql = "INSERT INTO $tbname(uid, data) VALUES(:uid, :data)";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        return $wdb->query($sql, array('uid' => $uid, 'data' => $data));
    }

    public function update($uid, $data)
    {
        $tbname = $this->getTableName($uid);

        $sql = "UPDATE $tbname SET data=:data WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $wdb->query($sql, array('uid' => $uid, 'data' => $data));
    }

    public function init($uid, $wallId, $size)
    {
        $tbname = $this->getTableName($uid);
        $tmp = array();
       	for($j = 0; $j < $size; $j++) {
       		$tmp[] = $wallId;
       	}

        $data = json_encode(array($tmp, $tmp));

        $sql = "INSERT INTO $tbname(uid, data) VALUES(:uid, :data)";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        return $wdb->query($sql, array('uid' => $uid, 'data' => $data));
    }

    public function clear($uid)
    {
        $tbname = $this->getTableName($uid);

        $sql = "UPDATE $tbname SET data='[]' WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $wdb->query($sql, array('uid' => $uid));
    }

}