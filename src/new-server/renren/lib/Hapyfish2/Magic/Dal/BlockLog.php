<?php

class Hapyfish2_Magic_Dal_BlockLog
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Magic_Dal_BlockLog
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
    	return 'magic_user_block_log';
    }
    
    public function getAll($uid)
    {
    	$tbname = $this->getTableName($uid);
    	$sql = "SELECT status,type,time FROM $tbname WHERE uid=:uid ORDER BY time DESC";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('uid' => $uid));
    }
    
    public function getRange($uid, $start, $end)
    {
    	$tbname = $this->getTableName($uid);
    	$sql = "SELECT uid,status,type,time FROM $tbname WHERE time>=$start AND time<$end";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql);
    }
    
    public function insert($uid, $info)
    {
    	$tbname = $this->getTableName($uid);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
    	return $wdb->insert($tbname, $info); 	
    }
    
    public function delete($uid)
    {
    	$tbname = $this->getTableName($uid);
    	$sql = "DELETE FROM $tbname WHERE uid=:uid";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
        return $wdb->query($sql, array('uid' => $uid)); 
    }
    
}