<?php

/**
 * platform user friend
 *
 *
 * @package    Dal
 * @create      2010/09/25    Hulj
 */
class Hapyfish2_Platform_Dal_Friend
{

    protected static $_instance;

    /**
     * 
     *
     * @return Hapyfish2_Platform_Dal_Friend
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public static function getTableName($uid)
    {
    	$id = floor($uid/4) % 10;
    	return 'platform_user_friend_' . $id;
    }
    
    public function getFriend($uid)
    {
    	$tbname = $this->getTableName($uid);
        $sql = "SELECT uid,fids,count FROM $tbname WHERE uid=:uid";
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        
        return $rdb->fetchRow($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }

    /**
     * insert new friends
     *
     * @param int $uid
     * @param string $fids
     * @param int $count
     */
    public function add($uid, $fids, $count)
    {
        $tbname = $this->getTableName($uid);
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
        $fids = $wdb->quote($fids);
        
        $sql = "INSERT INTO $tbname (uid, fids, count) VALUES"
              . '(' . $uid . ',' . $fids . ',' . $count .')'
              . ' ON DUPLICATE KEY UPDATE '
              . 'fids=' . $fids
              . ',count=' . $count;
        
        return $wdb->query($sql);
    }

    /**
     * update user friends
     *
     * @param int $uid
     * @param string $fids
     * @param int $count
     */
    public function update($uid, $fids, $count)
    {
        $tbname = $this->getTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
        $where = $wdb->quoteinto('uid = ?', $uid);
        $info = array('fids' => $fids, 'count' => $count);
        
        return $wdb->update($tbname, $info, $where); 
    }
    
}