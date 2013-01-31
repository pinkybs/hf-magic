<?php

class Happyfish_Magic_Dal_Mongo_UserDesk extends Happyfish_Magic_Dal_Mongo_Abstract
{
	/**
     * user table name
     *
     * @var string
     */
    protected $_tableName = 'user_desk';

    protected static $_instance;

    /**
     * single instance
     *
     * @return Happyfish_Magic_Dal_Mongo_UserDesk
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
     * @param integer $did
     * @return array
     */
    public function getInfo($uid, $did)
    {
    	return $this->_mg->{$this->_tableName}->findOne(array('uid' => (string)$uid, 'desk_id' =>(string)$did));	
    }
    
    /**
     * insert desk
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

    public function update($uid, $did, $info)
    {
        return $this->_mg->{$this->_tableName}->update(array('uid' => (string)$uid, 'desk_id' =>(string)$did), array('$set' => $info), array('upsert' => true));
    }

	public function updateForInc($uid, $did, $info)
    {
        return $this->_mg->{$this->_tableName}->update(array('uid' => (string)$uid, 'desk_id' =>(string)$did), array('$inc' => $info), array('upsert' => true));
    }
    
	/**
     * delete
     *
     * @param integer $uid
     * @return void
     */
    public function delete($uid, $did)
    {
        $this->_mg->{$this->_tableName}->remove(array('uid' => (string)$uid, 'desk_id' =>(string)$did));
    }


    /**
     * list desk
     *
     * @param integer $uid
     * @param integer $pageIndex
     * @param integer $pageSize
     * @return array
     */
    public function lstDesk($uid, $pageIndex=1, $pageSize=10)
    {
        $start = ($pageIndex - 1) * $pageSize;

        $cursor = $this->_mg->{$this->_tableName}
                    ->find(array('uid' => (string)$uid))
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
     * delete user desk info
     *
     * @param integer $uid
     * @return void
     */
    public function deleteDesk($uid)
    {
        $this->_mg->{$this->_tableName}->remove(array('uid' => (string)$uid));
    }

}