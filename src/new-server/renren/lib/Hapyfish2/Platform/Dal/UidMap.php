<?php

/**
 * uid map between with magic and platform
 *
 *
 * @package    Dal
 * @create      2010/06/28    Hulj
 */
class Hapyfish2_Platform_Dal_UidMap
{

    protected static $_instance;

    /**
     * 
     *
     * @return Hapyfish2_Platform_Dal_UidMap
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getTableName($puid)
    {
    	$id = strtolower(substr($puid, -1, 1));
    	return 'uid_map_' . $id;
    }
    
    public function getDB($puid)
    {
    	$id = strtolower(substr($puid, -1, 1));
    	$key = 'db_' . $id;
    	return Hapyfish2_Db_Factory::getBasicDB($key);
    }
    
    public function getSequence($puid)
    {
    	$name = strtolower(substr($puid, -1, 1));
    	$sql = "UPDATE seq_uid SET id=LAST_INSERT_ID(id+10) WHERE `name`=:name";
    	
    	$db = $this->getDB($puid);
    	$wdb = $db['w'];
    	$wdb->query($sql, array('name' => $name));
    	
    	return $wdb->lastInsertId();
    }

    /**
     * insert new platform uid
     *
     * @param string $puid
     * @return integer
     */
    public function newUser($uid, $puid, $time)
    {
    	$tb = $this->getTableName($puid);
        
        $sql = "INSERT INTO $tb(uid, puid, create_time) VALUES($uid, :puid, $time)";

    	$db = $this->getDB($puid);
        $wdb = $db['w'];
        
    	return $wdb->query($sql, array('puid' => $puid));
    }

    /**
     * get inner uid
     *
     * @param string $puid
     * @return integer
     */
    public function getUser($puid)
    {
        $tb = $this->getTableName($puid);
    	$sql = "SELECT uid,status FROM $tb WHERE puid=:puid";
    	
    	$db = $this->getDB($puid);
        $rdb = $db['r'];
        
        return $rdb->fetchRow($sql, array('puid' => $puid), Zend_Db::FETCH_NUM);
    }
    
    public function updateStatus($puid, $status)
    {
        $tb = $this->getTableName($puid);
    	$sql = "UPDATE $tb SET status=$status WHERE puid=:puid";
    	
    	$db = $this->getDB($puid);
        $wdb = $db['w'];
        
        return $wdb->query($sql, array('puid' => $puid));
    }
    
}