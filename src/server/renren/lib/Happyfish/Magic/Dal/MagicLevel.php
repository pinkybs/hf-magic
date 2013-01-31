<?php

/**
 * Dal Magic
 * MixiApp Magic Data Access Layer
 *
 * @package    Happyfish/Magic/Dal
 * @copyright  Copyright (c) 
 * @create     2010/08/11    zhangxin
 */
class Happyfish_Magic_Dal_MagicLevel extends Happyfish_Magic_Dal_Abstract
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
     * list user magic level
     *
     * @param integer $uid
     * @return array
     */
    public function lstUserMagicLevel($uid)
    {
        $sql = "SELECT * FROM magic_user_magic_level WHERE uid=:uid ";
        return $this->_rdb->fetchAll($sql, array('uid'=>$uid));
    }

    /**
     * get info
     *
     * @param integer $uid
     * @param integer $magicType
     * @return array
     */
    public function getInfo($uid, $magicType)
    {
        $sql = "SELECT * FROM magic_user_magic_level WHERE uid=:uid AND magic_type=:magic_type ";
        return $this->_rdb->fetchRow($sql, array('uid'=>$uid, 'magic_type'=>$magicType));
    }
    
    /**
     * insert 
     *
     * @param array $info
     * @return integer
     */
    public function insert($info)
    {
        return $this->_wdb->insert('magic_user_magic_level', $info);
    }

    /**
     * update 
     *
     * @param array $info
     * @param integer $uid
     * @param integer $type
     * @return integer
     */
    public function update($info, $uid, $type)
    {
        $where = array($this->_wdb->quoteInto('uid=?', $uid),
        		       $this->_wdb->quoteInto('magic_type=?', $type));
        return $this->_wdb->update('magic_user_magic_level', $info, $where);
    }
    
	/**
     * update user magic level by field name
     *
     * @param integer $uid
     * @param integer $type
     * @param string $field
     * @param integer $change
     * @return void
     */
    public function updateUserMagicLevelByField($uid, $type, $field, $change)
    {
        $sql = "UPDATE magic_user_magic_level SET $field = $field + :change WHERE uid=:uid AND magic_type=:magic_type ";
        $this->_wdb->query($sql,array('change'=>$change, 'uid'=>$uid, 'magic_type'=>$type));
    }

	/**
     * update user magic level by multiple field name
     *
     * @param integer $uid
     * @param integer $magicType
     * @param array $param
     * @return void
     */
    public function updateUserMagicLevelByMultipleField($uid, $magicType, $param)
    {
        $change = array();
        foreach ( $param as $k => $v ) {
            $change[] = $k . '=' . $k . '+' . $v;
        }
        $s1 = join(',', $change);

        $sql = "UPDATE magic_user_magic_level SET $s1 WHERE uid=:uid AND magic_type=:magic_type";
        $this->_wdb->query($sql,array('uid'=>$uid, 'magic_type'=>$magicType));
    }
    
	/**
     * delete 
     *
     * @param integer $uid
     * @param integer $mid
     * @return integer
     */
    public function delete($uid, $type)
    {
        $sql = "DELETE FROM magic_user_magic_level WHERE uid=:uid AND magic_type=:magic_type ";
        return $this->_wdb->query($sql, array('uid' => $uid, 'magic_type' => $type));
    }
    
	/**
     * delete user magic level
     *
     * @param integer $uid
     * @return integer
     */
    public function deleteUserUserMagicLevel($uid)
    {
        $sql = "DELETE FROM magic_user_magic_level WHERE uid=:uid ";
        return $this->_wdb->query($sql, array('uid' => $uid));
    }
    
}