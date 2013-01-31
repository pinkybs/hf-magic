<?php

/**
 * Dal Magic
 * MixiApp Magic Data Access Layer
 *
 * @package    Happyfish/Magic/Dal/Nbasic
 * @copyright  Copyright (c) 
 * @create     2010/07/23    zhangxin
 */
class Happyfish_Magic_Dal_Nbasic_NbCard extends Happyfish_Magic_Dal_Abstract
{

    /**
     * class default instance
     * @var self instance
     */
    protected static $_instance;

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
     * get 
     *
     * @param integer $cid
     * @return array
     */
    public function getInfo($cid)
    {
        $sql = "SELECT * FROM magic_nb_card WHERE cid=:cid ";
        return $this->_rdb->fetchRow($sql, array('cid'=>$cid));
    }
    
    /**
     * list 
     *
     * @param 
     * @return array
     */
    public function listInfo()
    {
        $sql = 'SELECT * FROM magic_nb_card ';
        return $this->_rdb->fetchAll($sql);
    }

    /**
     * insert 
     *
     * @param array $info
     * @return integer
     */
    public function insert($info)
    {
        return $this->_wdb->insert('magic_nb_card', $info);
    }

}