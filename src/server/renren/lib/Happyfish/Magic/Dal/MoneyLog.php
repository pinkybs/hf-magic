<?php

/**
 * Dal Magic
 * MixiApp Magic Data Access Layer
 *
 * @package    Happyfish/Magic/Dal
 * @copyright  Copyright (c) 
 * @create     2010/08/27    zhangxin
 */
class Happyfish_Magic_Dal_MoneyLog extends Happyfish_Magic_Dal_Abstract
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
    protected $table_user = 'magic_user_money_log';

	private function _getTableName($uid)
    {
        $n = $uid % 10;
        //return $this->table_user . '_' . $n;
        return $this->table_user;
    }
    
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
     * list user money income log
     *
     * @param integer $uid
     * @return array
     */
    public function lstUserMoneyLog($uid)
    {
    	$tname = $this->_getTableName($uid);
        $sql = "SELECT * FROM $tname WHERE uid=:uid ORDER BY create_time DESC";
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
    
}