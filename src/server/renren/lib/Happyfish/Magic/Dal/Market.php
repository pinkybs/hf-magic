<?php

/**
 * Dal Magic
 * MixiApp Magic Data Access Layer
 *
 * @package    Happyfish/Magic/Dal
 * @copyright  Copyright (c) 
 * @create     2010/08/17    zhangxin
 */
class Happyfish_Magic_Dal_Market extends Happyfish_Magic_Dal_Abstract
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
    protected $table_user = 'magic_user_market';
    
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
     * get user market
     *
     * @param integer $uid
     * @return array
     */
    public function getUserMarket($uid)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "SELECT * FROM $tname WHERE uid=:uid ";
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
    	$tname = $this->_getTableName($info['uid']);
        return $this->_wdb->insert($tname, $info);
    }

    /**
     * update 
     *
     * @param array $info
     * @param integer $uid
     * @return integer
     */
    public function update($info, $uid)
    {
    	$tname = $this->_getTableName($uid);
        $where = $this->_wdb->quoteInto('uid=?', $uid);
        return $this->_wdb->update($tname, $info, $where);
    }
    
	/**
     * update user market by field name
     *
     * @param integer $uid
     * @param string $field
     * @param integer $change
     * @return void
     */
    public function updateUserMarketByField($uid, $field, $change)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "UPDATE $tname SET $field = $field + :change WHERE uid=:uid ";
        $this->_wdb->query($sql,array('change'=>$change, 'uid'=>$uid));
    }

	/**
     * update user market info by multiple field name
     *
     * @param integer $uid
     * @param array $param
     * @return void
     */
    public function updateUserMarketByMultipleField($uid, $param)
    {
    	$tname = $this->_getTableName($uid);
        $change = array();
        foreach ( $param as $k => $v ) {
            $change[] = $k . '=' . $k . '+' . $v;
        }
        $s1 = join(',', $change);

        $sql = "UPDATE $tname SET $s1 WHERE uid=:uid ";
        $this->_wdb->query($sql,array('uid'=>$uid));
    }
    
	/**
     * delete 
     *
     * @param integer $uid
     * @return integer
     */
    public function delete($uid)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "DELETE FROM $tname WHERE uid=:uid ";
        return $this->_wdb->query($sql, array('uid' => $uid));
    }
    
}