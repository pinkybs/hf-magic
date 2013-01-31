<?php

class Hapyfish2_Magic_Dal_MapCopyMonster
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Magic_Dal_MapCopyMonster
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
    	return 'magic_user_map_copy_monster_' . $id;
    }

    public function getAllIds($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT map_id FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchCol($sql, array('uid' => $uid));
    }


    public function getAll($uid)
    {
        $tbname = $this->getTableName($uid);
    	//$sql = "SELECT id, cid, max_hp, cur_hp FROM $tbname WHERE uid=:uid";
		$sql = "SELECT map_id,`data` FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchAll($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }

    public function getOne($uid, $mapId)
    {
        $tbname = $this->getTableName($uid);
    	//$sql = "SELECT id,cid,max_hp,cur_hp FROM $tbname WHERE uid=:uid AND id=:id";
		$sql = "SELECT map_id,`data` FROM $tbname WHERE uid=:uid AND map_id=:map_id";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchRow($sql, array('uid' => $uid, 'map_id' => $mapId), Zend_Db::FETCH_NUM);
    }

    public function insert($uid, $info)
    {
        $tbname = $this->getTableName($uid);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	return $wdb->insert($tbname, $info);
    }

    public function update($uid, $mapId, $info)
    {
        $tbname = $this->getTableName($uid);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	$uid = $wdb->quote($uid);
    	$mapId = $wdb->quote($mapId);
    	$where = "uid=$uid AND map_id=$mapId";

        return $wdb->update($tbname, $info, $where);
    }

    public function delete($uid, $mapId)
    {
        $tbname = $this->getTableName($uid);

        $sql = "DELETE FROM $tbname WHERE uid=:uid AND map_id=:map_id";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        return $wdb->query($sql, array('uid' => $uid, 'map_id' => $mapId));
    }

    public function init($uid, $info)
    {
        $tbname = $this->getTableName($uid);

        $this->clear($uid);

        $sql = "INSERT INTO $tbname(uid,map_id,`data`) VALUES ";
        $aryData = array();
        $aryPar = array();
        $aryPar['uid'] = $uid;
        foreach ($info as $key=>$data) {
            //$aryData[] = "(:uid,".$data['map_id'].",'".$data['data']."')";
            $aryData[] = "(:uid,".$data['map_id'].",:par$key)";
            $aryPar['par'.$key] = $data['data'];//$wdb->quote($uid)
        }

        $sql .= implode(",\n", $aryData);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
//info_log($sql, 'bbb');
        $wdb->query($sql, $aryPar);
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