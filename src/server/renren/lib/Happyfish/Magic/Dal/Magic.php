<?php

/**
 * Dal Magic
 * MixiApp Magic Data Access Layer
 *
 * @package    Happyfish/Magic/Dal
 * @copyright  Copyright (c) 
 * @create     2010/07/27    zhangxin
 */
class Happyfish_Magic_Dal_Magic extends Happyfish_Magic_Dal_Abstract
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
    protected $table_user = 'magic_user_magic';
    
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
     * get 
     *
     * @param integer $uid
     * @param integer $magicId
     * @return array
     */
    public function getUserMagic($uid, $magicId)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "SELECT * FROM $tname WHERE uid=:uid AND magic_id=:magic_id ";
        return $this->_rdb->fetchRow($sql, array('uid'=>$uid, 'magic_id'=>$magicId));
    }
    
	/**
     * list user magic
     *
     * @param integer $uid
     * @return array
     */
    public function lstUserMagic($uid)
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
     * @param integer $magicId
     * @return integer
     */
    public function update($info, $uid, $magicId)
    {
    	$tname = $this->_getTableName($uid);
        $where = array($this->_wdb->quoteInto('uid=?', $uid),
        		       $this->_wdb->quoteInto('magic_id=?', $magicId));
        return $this->_wdb->update($tname, $info, $where);
    }
    
	/**
     * update user card by field name
     *
     * @param integer $uid
     * @param integer $magicId
     * @param string $field
     * @param integer $change
     * @return void
     */
    public function updateUserMagicByField($uid, $magicId, $field, $change)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "UPDATE $tname SET $field = $field + :change WHERE uid=:uid AND magic_id=:magic_id ";
        $this->_wdb->query($sql,array('change'=>$change, 'uid'=>$uid, 'magic_id'=>$magicId));
    }

	/**
     * delete 
     *
     * @param integer $uid
     * @param integer $magicId
     * @return integer
     */
    public function delete($uid, $magicId)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "DELETE FROM $tname WHERE uid=:uid AND magic_id=:magic_id ";
        return $this->_wdb->query($sql, array('uid' => $uid, 'magic_id' => $magicId));
    }
    
	/**
     * delete user magic
     *
     * @param integer $uid
     * @return integer
     */
    public function deleteUserMagic($uid)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "DELETE FROM $tname WHERE uid=:uid ";
        return $this->_wdb->query($sql, array('uid' => $uid));
    }
    
}