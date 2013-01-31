<?php

class Happyfish_Magic_Dal_Mongo_UserMessage extends Happyfish_Magic_Dal_Mongo_Abstract
{                    
	/**
     * user table name
     *
     * @var string
     */
    protected $_tableName = 'user_message';
    
    protected static $_instance;
    
    /**
     * single instance of 
     *
     * @return 
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * insert Message
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
        
    /**
     * list message
     *
     * @param integer $uid
     * @param integer $pageIndex
     * @param integer $pageSize
     * @return array
     */
    public function lstMessage($uid, $pageIndex=1, $pageSize=10)
    {
        $start = ($pageIndex - 1) * $pageSize;
        
        $cursor = $this->_mg->{$this->_tableName}
                    ->find(array('uid' => (string)$uid))
                    ->sort(array('create_time' => -1))
                    ->skip($start)
                    ->limit($pageSize);
        
        $result = array();
        while($cursor->hasNext()) {
            $v = $cursor->getNext();
            //unset($v['_id']);
            $result[] = $v;
        }
        
        return $result;
    }
    
	/**
     * delete user Message info
     *
     * @param integer $uid
     * @return void
     */
    public function deleteMessage($uid)
    {        
        $this->_mg->{$this->_tableName}->remove(array('target_uid' => (string)$uid));
    }
    
}