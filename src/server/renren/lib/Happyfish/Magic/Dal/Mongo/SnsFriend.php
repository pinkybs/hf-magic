<?php

class Happyfish_Magic_Dal_Mongo_SnsFriend extends Happyfish_Magic_Dal_Mongo_Abstract
{
    /**
     * user table name
     *
     * @var string
     */
    protected $table_friend = 'sns_friend';
    
    protected static $_instance;
        
    /**
     * single instance of Happyfish_Magic_Dal_Mongo_SnsFriend
     *
     * @return Happyfish_Magic_Dal_Mongo_SnsFriend
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function getTableName($uid)
    {
        //$n = $uid % 10;
        //return $this->table_friend . '_' . $n;
        return $this->table_friend;
    }
    
    public function getFriends($uid)
    {
        $tname = $this->getTableName($uid);
        $result = $this->_mg->$tname->findOne(array('uid' => (string)$uid));
        
        if ( $result ) {
            return $result['fids'];
        }
        return null;
    }

    public function insertFriend($uid, $fids)
    {
        $tname = $this->getTableName($uid);
        $this->_mg->$tname->update(array('uid' => (string)$uid), array('$set' => array('fids' => $fids)), array('upsert' => true));
    }
        
    public function deleteFriend($uid)
    {
        $tname = $this->getTableName($uid);
        $this->_mg->$tname->remove(array('uid' => (string)$uid));
    }
 
}