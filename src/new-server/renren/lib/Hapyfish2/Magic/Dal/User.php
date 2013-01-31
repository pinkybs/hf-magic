<?php


class Hapyfish2_Magic_Dal_User
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Magic_Dal_User
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
    	return 'magic_user_info_' . $id;
    }
    
    public function get($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT avatar_id,coin,gold,exp,level,house_level FROM $tbname WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }
    
    public function getAvatar($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT avatar_id,avatar_edit FROM $tbname WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }
    
    public function getExp($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT exp FROM $tbname WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    public function getCoin($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT coin FROM $tbname WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    public function getGold($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT gold FROM $tbname WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    public function incGold($uid, $gold)
    {
        $tbname = $this->getTableName($uid);
        $sql = "UPDATE $tbname SET gold=gold+:gold WHERE uid=:uid";
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
        return $wdb->query($sql, array('uid' => $uid, 'gold' => $gold));
    }
    
    public function decGold($uid, $gold)
    {
        $tbname = $this->getTableName($uid);
        $sql = "UPDATE $tbname SET gold=gold-:gold WHERE uid=:uid AND gold>=:gold";
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
        return $wdb->query($sql, array('uid' => $uid, 'gold' => $gold));
    }
    
    public function getLevel($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT level,house_level FROM $tbname WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }
    
    public function getScene($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT tile_x_length,tile_z_length,cur_scene_id,open_scene_list FROM $tbname WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }
    
    public function getMp($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT mp,max_mp,mp_set_time,mp_recovery_rate_plus FROM $tbname WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }
    
    public function getTrans($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT trans_type,trans_start_time FROM $tbname WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }
    
    public function getLoginInfo($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT last_login_time,today_login_count,active_login_count,max_active_login_count,all_login_count FROM $tbname WHERE uid=:uid";
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }
    
    public function update($uid, $info)
    {
        $tbname = $this->getTableName($uid);
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
    	$where = $wdb->quoteinto('uid = ?', $uid);
    	
        $wdb->update($tbname, $info, $where);   	
    }
    
    public function init($uid)
    {
        $tbname = $this->getTableName($uid);
        $coin = INIT_USER_COIN;
        $gold = INIT_USER_GOLD;
        $mp = INIT_USER_MP;
        $avatarId = INIT_USER_AVATAR_ID;
        
        $sql = "INSERT INTO $tbname(uid,avatar_id,coin,gold,mp,max_mp,cur_scene_id) VALUES(:uid,$avatarId,$coin,$gold,$mp,$mp,1000001)";
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid));
    }
    
}