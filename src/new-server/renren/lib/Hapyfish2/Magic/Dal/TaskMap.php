<?php


class Hapyfish2_Magic_Dal_TaskMap
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Magic_Dal_TaskMap
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
    	return 'magic_user_task_map_' . $id;
    }

    public function getTaskMapIds($uid, $pMapId)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT tid FROM $tbname WHERE uid=:uid AND map_parent_id=:map_parent_id";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchCol($sql, array('uid' => $uid, 'map_parent_id' => $pMapId));
    }


    public function getAllTaskInMap($uid, $pMapId)
    {
        $tbname = $this->getTableName($uid);
		$sql = "SELECT tid,map_parent_id,cur_num,award_status,begin_time,end_time,complete_count FROM $tbname WHERE uid=:uid AND map_parent_id=:map_parent_id";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchAll($sql, array('uid' => $uid, 'map_parent_id' => $pMapId), Zend_Db::FETCH_NUM);
    }

    public function getOne($uid, $tid)
    {
        $tbname = $this->getTableName($uid);
    	//$sql = "SELECT id,cid,max_hp,cur_hp FROM $tbname WHERE uid=:uid AND id=:id";
		$sql = "SELECT tid,map_parent_id,cur_num,award_status,begin_time,end_time,complete_count FROM $tbname WHERE uid=:uid AND tid=:tid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchRow($sql, array('uid' => $uid, 'tid' => $tid), Zend_Db::FETCH_NUM);
    }

    public function insert($uid, $info)
    {
        $tbname = $this->getTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	return $wdb->insert($tbname, $info);
    }

    public function update($uid, $tid, $info)
    {
        $tbname = $this->getTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
    	$uid = $wdb->quote($uid);
    	$tid = $wdb->quote($tid);
    	$where = "uid=$uid AND tid=$tid";

        return $wdb->update($tbname, $info, $where);
    }

    public function delete($uid, $tid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "DELETE FROM $tbname WHERE uid=:uid AND tid=:tid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        return $wdb->query($sql, array('uid' => $uid, 'tid' => $tid));
    }

    public function init($uid, $pMapId, $info)
    {
        $tbname = $this->getTableName($uid);
        $this->clear($uid, $pMapId);

        $sql = "INSERT INTO $tbname(uid,tid,map_parent_id) VALUES ";
        $aryData = array();
        foreach ($info as $data) {
            $aryData[] = "(:uid," . $data['tid']."," . $data['map_parent_id'].")";
        }
        $sql .= implode(',', $aryData);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $wdb->query($sql, array('uid' => $uid));
        return true;
    }

    public function clear($uid, $pMapId)
    {
        $tbname = $this->getTableName($uid);
        $sql = "DELETE FROM $tbname WHERE uid=:uid AND map_parent_id=:map_parent_id";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        return $wdb->query($sql, array('uid' => $uid, 'map_parent_id' => $pMapId));
    }

    public function clearAll($uid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "DELETE FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        return $wdb->query($sql, array('uid' => $uid));
    }
}