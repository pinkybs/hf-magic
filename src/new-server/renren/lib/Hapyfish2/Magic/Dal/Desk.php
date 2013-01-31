<?php

class Hapyfish2_Magic_Dal_Desk
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Magic_Dal_Desk
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
    	return 'magic_user_desk_' . $id;
    }

    public function getAllIds($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT id FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchCol($sql, array('uid' => $uid));
    }

    public function getInSceneIds($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT id FROM $tbname WHERE uid=:uid AND status=1";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchCol($sql, array('uid' => $uid));
    }

    public function getAll($uid)
    {
        $tbname = $this->getTableName($uid);
    	//$sql = "SELECT id,cid,x,y,z,mirro,item_type,status,student_id,magic_id,coin,end_time,stone_time FROM $tbname WHERE uid=:uid";
		$sql = "SELECT id,cid,x,y,z,mirro,item_type,status,0 AS student_id,0 AS magic_id,0 AS coin,0 AS end_time,0 AS stone_time FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchAll($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }

    public function getOne($uid, $id)
    {
        $tbname = $this->getTableName($uid);
    	//$sql = "SELECT id,cid,x,y,z,mirro,item_type,status,student_id,magic_id,coin,end_time,stone_time FROM $tbname WHERE uid=:uid AND id=:id";
		$sql = "SELECT id,cid,x,y,z,mirro,item_type,status,0 AS student_id,0 AS magic_id,0 AS coin,0 AS end_time,0 AS stone_time FROM $tbname WHERE uid=:uid AND id=:id";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchRow($sql, array('uid' => $uid, 'id' => $id), Zend_Db::FETCH_NUM);
    }

    public function getInScene($uid)
    {
        $tbname = $this->getTableName($uid);
    	//$sql = "SELECT id,cid,x,y,z,mirro,item_type,status,student_id,magic_id,coin,end_time,stone_time FROM $tbname WHERE uid=:uid AND status=1";
		$sql = "SELECT id,cid,x,y,z,mirro,item_type,status,0 AS student_id,0 AS magic_id,0 AS coin,0 AS end_time,0 AS stone_time FROM $tbname WHERE uid=:uid AND status=1";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchAll($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }

    public function getInBag($uid)
    {
        $tbname = $this->getTableName($uid);
    	//$sql = "SELECT id,cid,x,y,z,mirro,item_type,status,student_id,magic_id,coin,end_time,stone_time FROM $tbname WHERE uid=:uid AND status=0";
    	$sql = "SELECT id,cid,x,y,z,mirro,item_type,status,0 AS student_id,0 AS magic_id,0 AS coin,0 AS end_time,0 AS stone_time FROM $tbname WHERE uid=:uid AND status=0";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchAll($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }

    public function insert($uid, $building)
    {
        $tbname = $this->getTableName($uid);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	return $wdb->insert($tbname, $building);
    }

    public function update($uid, $id, $info)
    {
        $tbname = $this->getTableName($uid);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	$uid = $wdb->quote($uid);
        $id = $wdb->quote($id);
    	$where = "uid=$uid AND id=$id";

        return $wdb->update($tbname, $info, $where);
    }

    public function delete($uid, $id)
    {
        $tbname = $this->getTableName($uid);

        $sql = "DELETE FROM $tbname WHERE uid=:uid AND id=:id";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        return $wdb->query($sql, array('uid' => $uid, 'id' => $id));
    }

    public function init($uid)
    {
        $tbname = $this->getTableName($uid);

        $sql = "INSERT INTO $tbname(uid, id, cid, x, y, z, mirro, status, item_type)
            VALUES
            (:uid, 1, 191001, 4, 0, 6, 0, 1, 1),
            (:uid, 2, 191001, 4, 0, 3, 0, 1, 1)";

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

        return $wdb->query($sql, array('uid' => $uid));
    }

}