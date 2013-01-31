<?php

/**
 * Dal Magic
 * MixiApp Magic Data Access Layer
 *
 * @package    Happyfish/Magic/Dal
 * @copyright  Copyright (c) 
 * @create     2010/07/27    zhangxin
 */
class Happyfish_Magic_Dal_Card extends Happyfish_Magic_Dal_Abstract
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
    protected $table_user = 'magic_user_card';
    
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
     * list user card
     *
     * @param integer $uid
     * @return array
     */
    public function lstUserCard($uid)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "SELECT * FROM $tname WHERE uid=:uid ";
        return $this->_rdb->fetchAll($sql, array('uid'=>$uid));
    }

    /**
     * get 
     *
     * @param integer $uid
     * @param integer $cid
     * @return array
     */
    public function getUserCard($uid, $cid)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "SELECT * FROM $tname WHERE uid=:uid AND cid=:cid ";
        return $this->_rdb->fetchRow($sql, array('uid'=>$uid, 'cid'=>$cid));
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
     * @param integer $cid
     * @return integer
     */
    public function update($info, $uid, $cid)
    {
    	$tname = $this->_getTableName($uid);
        $where = array($this->_wdb->quoteInto('uid=?', $uid),
        		       $this->_wdb->quoteInto('cid=?', $cid));
        return $this->_wdb->update($tname, $info, $where);
    }
    
	/**
     * update user card by field name
     *
     * @param integer $uid
     * @param integer $cid
     * @param string $field
     * @param integer $change
     * @return void
     */
    public function updateUserCardByField($uid, $cid, $field, $change)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "UPDATE $tname SET $field = $field + :change WHERE uid=:uid AND cid=:cid ";
        $this->_wdb->query($sql,array('change'=>$change, 'uid'=>$uid, 'cid'=>$cid));
    }

	/**
     * delete 
     *
     * @param integer $uid
     * @param integer $cid
     * @return integer
     */
    public function delete($uid, $cid)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "DELETE FROM $tname WHERE uid=:uid AND cid=:cid ";
        return $this->_wdb->query($sql, array('uid' => $uid, 'cid' => $cid));
    }
    
	/**
     * delete user card
     *
     * @param integer $uid
     * @return integer
     */
    public function deleteUserCard($uid)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "DELETE FROM $tname WHERE uid=:uid ";
        return $this->_wdb->query($sql, array('uid' => $uid));
    }
    
}