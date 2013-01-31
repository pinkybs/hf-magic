<?php

/**
 * platform user info
 *
 *
 * @package    Dal
 * @create      2010/09/25    Hulj
 */
class Hapyfish2_Platform_Dal_User
{

    protected static $_instance;

    /**
     * 
     *
     * @return Hapyfish2_Platform_Dal_User
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
    	$id = floor($uid/4) % 10;
    	return 'platform_user_info_' . $id;
    }
    
    public function getInfo($uid)
    {
    	$tbname = $this->getTableName($uid);
        $sql = "SELECT uid,puid,name,figureurl,gender,create_time FROM $tbname WHERE uid=:uid";
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        
        return $rdb->fetchRow($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }
    
    public function getVUID($uid)
    {
    	$tbname = $this->getTableName($uid);
        $sql = "SELECT vuid FROM $tbname WHERE uid=:uid";
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }

    /**
     * insert new platform uid
     *
     * @param string $puid
     * @return integer
     */
    public function add($user)
    {
    	$uid = $user['uid'];
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
        $puid = $wdb->quote($user['puid']);
        $name = $wdb->quote($user['name']);
        $gender = $user['gender'];
        $figureurl = $wdb->quote($user['figureurl']);
		$create_time = time();
        
        $tbname = $this->getTableName($uid);
        
        $sql = "INSERT INTO $tbname (uid, puid, name, gender, figureurl, create_time) VALUES"
              . '(' . $uid . ',' . $puid . ',' . $name . ',' . $gender . ',' . $figureurl . ',' . $create_time . ')'
              . ' ON DUPLICATE KEY UPDATE '
              . 'puid=' . $puid
              . ',name=' . $name
              . ',gender=' . $gender
              . ',figureurl=' . $figureurl;
        
        return $wdb->query($sql);
    }

    /**
     * get inner uid
     *
     * @param string $puid
     * @return integer
     */
    public function update($uid, $info)
    {
        $tbname = $this->getTableName($uid);
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
        $where = $wdb->quoteinto('uid = ?', $uid);
        
        return $wdb->update($tbname, $info, $where); 
    }
    
    public function updateStatus($uid, $status, $time = null)
    {
    	if (!$time) {
    		$time = time();
    	}
    	$tbname = $this->getTableName($uid);
    	$sql = "UPDATE $tbname SET status=:status,status_update_time=$time WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
        return $wdb->query($sql, array('uid' => $uid, 'status' => $status));
    }
    
    public function getStatus($uid)
    {
    	$tbname = $this->getTableName($uid);
        $sql = "SELECT status FROM $tbname WHERE uid=:uid";
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    public function getStatus2($uid)
    {
    	$tbname = $this->getTableName($uid);
        $sql = "SELECT status,status_update_time FROM $tbname WHERE uid=:uid";
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        
        return $rdb->fetchRow($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }
    
    public function getStatusUpdateTime($uid)
    {
    	$tbname = $this->getTableName($uid);
        $sql = "SELECT status_update_time FROM $tbname WHERE uid=:uid";
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
}