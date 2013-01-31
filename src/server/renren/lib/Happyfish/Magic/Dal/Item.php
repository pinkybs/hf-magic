<?php

/**
 * Dal Magic
 * MixiApp Magic Data Access Layer
 *
 * @package    Happyfish/Magic/Dal
 * @copyright  Copyright (c) 
 * @create     2010/08/11    zhangxin
 */
class Happyfish_Magic_Dal_Item extends Happyfish_Magic_Dal_Abstract
{

    /**
     * class default instance
     * @var self instance
     */
    protected static $_instance;

    /**
     * user table name
     *
     * @var string
     */
    protected $table_user = 'magic_user_item';
    
    /**
     * return self's default instance
     *
     * @return self instance
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    private function _getTableName($uid)
    {
        $n = $uid % 10;
        //return $this->table_user . '_' . $n;
        return $this->table_user;
    }
    
	/**
     * add user item
     *
     * @param array $itemInfo
     * @return void
     */
    public function addUserItem($itemInfo)
    {
    	$tname = $this->_getTableName($itemInfo['uid']);
        $sql = "SELECT uid,mid FROM $tname WHERE uid=:uid AND mid=:mid ";
        $result = $this->_rdb->fetchRow($sql, array('uid'=>$itemInfo['uid'], 'mid'=>$itemInfo['mid']));

        if ( $result ) {
            $sql = "UPDATE $tname SET item_count = item_count + :change WHERE uid=:uid AND mid=:mid ";
            $this->_wdb->query($sql, array('uid'=>$itemInfo['uid'], 'mid'=>$itemInfo['mid'], 'change'=>$itemInfo['item_count']));
        }
        else {
            $this->_wdb->insert($tname, $itemInfo);
        }
    }
    
    /**
     * get 
     *
     * @param integer $uid
     * @param integer $mid
     * @return array
     */
    public function getUserItem($uid, $mid)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "SELECT * FROM $tname WHERE uid=:uid AND mid=:mid ";
        return $this->_rdb->fetchRow($sql, array('uid'=>$uid, 'mid'=>$mid));
    }
    
	/**
     * list user item
     *
     * @param integer $uid
     * @return array
     */
    public function lstUserItem($uid)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "SELECT * FROM $tname WHERE uid=:uid ";
        return $this->_rdb->fetchAll($sql, array('uid'=>$uid));
    }

    /**
     * insert 
     *
     * @param array $info
     * @return integer
     */
    public function insert($info)
    {
    	$tname = $this->_getTableName($info['uid']);
        return $this->_wdb->insert($tname, $info);
    }

    /**
     * update 
     *
     * @param array $info
     * @param integer $uid
     * @param integer $mid
     * @return integer
     */
    public function update($info, $uid, $mid)
    {
    	$tname = $this->_getTableName($uid);
        $where = array($this->_wdb->quoteInto('uid=?', $uid),
        		       $this->_wdb->quoteInto('mid=?', $mid));
        return $this->_wdb->update($tname, $info, $where);
    }
    
	/**
     * update user item by field name
     *
     * @param integer $uid
     * @param integer $mid
     * @param string $field
     * @param integer $change
     * @return void
     */
    public function updateUserItemByField($uid, $mid, $field, $change)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "UPDATE $tname SET $field = $field + :change WHERE uid=:uid AND mid=:mid ";
        $this->_wdb->query($sql,array('change'=>$change, 'uid'=>$uid, 'mid'=>$mid));
    }

	/**
     * delete 
     *
     * @param integer $uid
     * @param integer $mid
     * @return integer
     */
    public function delete($uid, $mid)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "DELETE FROM $tname WHERE uid=:uid AND mid=:mid ";
        return $this->_wdb->query($sql, array('uid' => $uid, 'mid' => $mid));
    }
    
	/**
     * delete user item
     *
     * @param integer $uid
     * @return integer
     */
    public function deleteUserItem($uid)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "DELETE FROM $tname WHERE uid=:uid ";
        return $this->_wdb->query($sql, array('uid' => $uid));
    }
    
}