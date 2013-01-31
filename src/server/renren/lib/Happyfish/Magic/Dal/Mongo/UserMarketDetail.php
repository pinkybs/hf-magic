<?php

class Happyfish_Magic_Dal_Mongo_UserMarketDetail extends Happyfish_Magic_Dal_Mongo_Abstract
{
	/**
     * user table name
     *
     * @var string
     */
    protected $_tableName = 'user_market_d';

    protected static $_instance;

    /**
     * single instance
     *
     * @return Happyfish_Magic_Dal_Mongo_UserMarketDetail
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
     * @param integer $uid
     * @return array
     */
    public function getInfo($uid)
    {
    	
    	return $this->_mg->{$this->_tableName}->findOne(array('uid' => (string)$uid));	
    }
    
    /**
     * insert 
     *
     * @param array $info
     * @return boolean
     */
    public function insert($info)
    {
        return $this->_mg->{$this->_tableName}->insert($info);
    }

    public function batchInsert($info)
    {
        return $this->_mg->{$this->_tableName}->batchInsert($info);
    }

    public function update($uid, $info)
    {
        return $this->_mg->{$this->_tableName}->update(array('uid' => (string)$uid), array('$set' => $info), array('upsert' => true));
    }
    
	public function updateForInc($uid, $info)
    {
        return $this->_mg->{$this->_tableName}->update(array('uid' => (string)$uid), array('$inc' => $info), array('upsert' => true));
    }

	/**
     * delete
     *
     * @param integer $uid
     * @return void
     */
    public function delete($uid)
    {
        $this->_mg->{$this->_tableName}->remove(array('uid' => (string)$uid));
    }

}