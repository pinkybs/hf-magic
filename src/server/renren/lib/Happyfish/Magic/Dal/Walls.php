<?php

/**
 * Dal Magic
 * MixiApp Magic Data Access Layer
 *
 * @package    Happyfish/Magic/Dal
 * @copyright  Copyright (c)
 * @create     2010/09/08    zhangxin
 */
class Happyfish_Magic_Dal_Walls extends Happyfish_Magic_Dal_Abstract
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
    protected $table_user = 'magic_user_wall';
    
    /**
     * user table name
     *
     * @var string
     */
    protected $table_user_inbag = 'magic_user_wall_inbag';

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

	/**
     * get user walls
     *
     * @param integer $uid
     * @return array
     */
    public function getUserWalls($uid)
    {
        $sql = "SELECT * FROM $this->table_user WHERE uid=:uid ";
        return $this->_rdb->fetchRow($sql, array('uid'=>$uid));
    }

    /**
     * insert
     *
     * @param array $info
     * @return integer
     */
    public function insert($info)
    {
        return $this->_wdb->insert($this->table_user, $info);
    }

    /**
     * update
     *
     * @param array $info
     * @param string uid
     * @return integer
     */
    public function update($info, $uid)
    {
        $where = $this->_wdb->quoteInto('uid=?', $uid);
        return $this->_wdb->update($this->table_user, $info, $where);
    }

	/**
     * delete
     *
     * @param integer $id
     * @param integer $uid
     * @return integer
     */
    public function delete($uid)
    {
        $sql = "DELETE FROM $this->table_user WHERE uid=:uid ";
        return $this->_wdb->query($sql, array('uid' => $uid));
    }
    
    
    /* walls in bag */
    
	private function _getTableName($uid)
    {
        $n = $uid % 10;
        //return $this->table_user_inbag . '_' . $n;
        return $this->table_user_inbag;
    }

	/**
     * list user walls
     *
     * @param integer $uid
     * @return array
     */
    public function lstUserWallInBag($uid)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "SELECT * FROM $tname WHERE uid=:uid ";
        return $this->_rdb->fetchAll($sql, array('uid'=>$uid));
    }

    /**
     * get 
     *
     * @param integer $uid
     * @param integer $wallId
     * @return array
     */
    public function getUserWallInBag($uid, $wallId)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "SELECT * FROM $tname WHERE uid=:uid AND wall_id=:wall_id ";
        return $this->_rdb->fetchRow($sql, array('uid'=>$uid, 'wall_id'=>$wallId));
    }
    
    /**
     * insert
     *
     * @param array $info
     * @return integer
     */
    public function insertUserWallInBag($info)
    {
    	$tname = $this->_getTableName($info['uid']);
        return $this->_wdb->insert($tname, $info);
    }

	/**
     * update 
     *
     * @param array $info
     * @param integer $uid
     * @param integer $fid
     * @return integer
     */
    public function updateUserWallInBag($info, $uid, $wid)
    {
    	$tname = $this->_getTableName($uid);
        $where = array($this->_wdb->quoteInto('uid=?', $uid),
        		       $this->_wdb->quoteInto('wall_id=?', $wid));
        return $this->_wdb->update($tname, $info, $where);
    }
    
    /**
     * update
     *
     * @param integer $uid
     * @param integer $wid
     * @param string $field
     * @param integer $change
     * @return integer
     */
    public function updateUserWallInBagByField($uid, $wid, $field, $change)
    {
    	$tname = $this->_getTableName($uid);
    	$sql = "UPDATE $tname SET $field = $field + :change WHERE uid=:uid AND wall_id=:wall_id";
        $this->_wdb->query($sql,array('uid'=>$uid, 'wall_id'=>$wid, 'change'=>$change));
    }

	/**
     * delete
     *
     * @param integer $uid
     * @param integer $wid
     * @return integer
     */
    public function deleteUserWallInBag($uid, $wid)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "DELETE FROM $tname WHERE uid=:uid AND wall_id=:wall_id";
        return $this->_wdb->query($sql, array('id' => $id, 'wall_id'=>$wid));
    }

	/**
     * delete user walls
     *
     * @param integer $uid
     * @return integer
     */
    public function deleteUserWallInBagAll($uid)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "DELETE FROM $tname WHERE uid=:uid ";
        return $this->_wdb->query($sql, array('uid' => $uid));
    }
    
}