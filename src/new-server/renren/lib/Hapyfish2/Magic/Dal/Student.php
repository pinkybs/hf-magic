<?php


class Hapyfish2_Magic_Dal_Student
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Magic_Dal_Student
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
    	return 'magic_user_student_' . $id;
    }
    
    public function get($uid, $sid)
    {
        $tbname = $this->getTableName($uid);
    	//$sql = "SELECT sid,exp,level,award_flg,state,desk_id,start_time,end_time,spend_time,event,event_time,magic_id,coin,stone_time FROM $tbname WHERE uid=:uid AND sid=:sid";
    	$sql = "SELECT sid,exp,level,award_flg,3 AS state,0 AS desk_id,0 AS start_time,0 AS end_time,0 AS spend_time,0 AS event,0 AS event_time,0 AS magic_id,0 AS coin,0 AS stone_time FROM $tbname WHERE uid=:uid AND sid=:sid";
        
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('uid' => $uid, 'sid' => $sid), Zend_Db::FETCH_NUM);
    }
    
    public function getUnlockStudentIds($uid)
    {
    	$tbname = $this->getTableName($uid);
    	$sql = "SELECT sid FROM $tbname WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        
        $ids = $rdb->fetchAll($sql, array('uid' => $uid));
        if (empty($ids)) {
        	return '';
        } else {
        	$d = array();
        	foreach ($ids as $v) {
        		$d[] = $v['sid'];
        	}
        	return join(',', $d);
        }
    }
    
    public function getAll($uid)
    {
    	$tbname = $this->getTableName($uid);
    	//$sql = "SELECT sid,exp,level,award_flg,state,desk_id,start_time,end_time,spend_time,event,event_time,magic_id,coin,stone_time FROM $tbname WHERE uid=:uid";
    	$sql = "SELECT sid,exp,level,award_flg,3 AS state,0 AS desk_id,0 AS start_time,0 AS end_time,0 AS spend_time,0 AS event,0 AS event_time,0 AS magic_id,0 AS coin,0 AS stone_time FROM $tbname WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }
    
    public function insert($uid, $data)
    {
        $tbname = $this->getTableName($uid);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	return $wdb->insert($tbname, $data);
    }
    
    public function update($uid, $sid, $info)
    {
        $tbname = $this->getTableName($uid);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	$uid = $wdb->quote($uid);
        $sid = $wdb->quote($sid);
    	$where = "uid=$uid AND sid=$sid";

        return $wdb->update($tbname, $info, $where);
    }
    
    public function init($uid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "INSERT INTO $tbname(uid, sid) VALUES(:uid, 1), (:uid, 2), (:uid, 3)";
        
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