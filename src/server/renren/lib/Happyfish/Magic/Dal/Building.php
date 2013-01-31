<?php

/**
 * Dal Magic
 * MixiApp Magic Data Access Layer
 *
 * @package    Happyfish/Magic/Dal
 * @copyright  Copyright (c)
 * @create     2010/07/27    zhangxin
 */
class Happyfish_Magic_Dal_Building extends Happyfish_Magic_Dal_Abstract
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
    protected $table_user = 'magic_user_building';

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
     * @param integer $id
     * @return array
     */
    public function getUserBuilding($uid, $id)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "SELECT * FROM $tname WHERE id=:id ";
        return $this->_rdb->fetchRow($sql, array('id'=>$id));
    }
    
	/**
     * get building by building id
     *
     * @param integer $uid
     * @param integer $bid
     * @return array
     */
    public function getUserBuildingInBagByBid($uid, $bid)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "SELECT id,uid,building_id,status FROM $tname WHERE uid=:uid AND building_id=:building_id AND status=0 ";
        return $this->_rdb->fetchRow($sql, array('uid'=>$uid, 'building_id'=>$bid));
    }

	/**
     * list user building
     *
     * @param integer $uid
     * @return array
     */
    public function lstUserBuilding($uid)
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
        $this->_wdb->insert($tname, $info);
        return $this->_wdb->lastInsertId();
    }

    /**
     * update
     *
     * @param array $info
     * @param string $id
     * @return integer
     */
    public function update($info, $id)
    {
    	$tname = $this->_getTableName($info['uid']);
        $where = $this->_wdb->quoteInto('id=?', $id);
        return $this->_wdb->update($tname, $info, $where);
    }

	/**
     * delete
     *
     * @param integer $id
     * @param integer $uid
     * @return integer
     */
    public function delete($id, $uid)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "DELETE FROM $tname WHERE id=:id ";
        return $this->_wdb->query($sql, array('id' => $id));
    }

	/**
     * delete user building
     *
     * @param integer $uid
     * @return integer
     */
    public function deleteUserBuilding($uid)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "DELETE FROM $tname WHERE uid=:uid ";
        return $this->_wdb->query($sql, array('uid' => $uid));
    }

}