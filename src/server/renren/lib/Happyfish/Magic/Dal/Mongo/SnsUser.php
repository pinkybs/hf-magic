<?php

class Happyfish_Magic_Dal_Mongo_SnsUser extends Happyfish_Magic_Dal_Mongo_Abstract
{
    /**
     * user table name
     *
     * @var string
     */
    protected $table_user = 'sns_user';
    
    protected static $_instance;
        
    /**
     * single instance of Happyfish_Magic_Dal_Mongo_SnsUser
     *
     * @return Happyfish_Magic_Dal_Mongo_SnsUser
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
        //return $this->table_user . '_' . $n;
        return $this->table_user;
    }

    public function getPerson($uid)
    {
        $tname = $this->getTableName($uid);
                
        $result = $this->_mg->$tname->findOne(array('uid' => (string)$uid));
        if ($result) {
            $user = array('uid' => $result['uid'],
                          'name' => $result['name'],
                          'sex' => $result['sex'],
                          'tinyurl' => $result['tinyurl'],
                          'headurl' => $result['headurl'],
                          'shop_id' => $result['shop_id']);
            return $user;
        }
        return false;
    }

    public function addPerson($user)
    {
        $sex = $user['sex'] == 0 ? '0' : '1';
        $shop_id = isset($user['shop_id']) ? $user['shop_id'] : '0';
        $updated = time();

        $tname = $this->getTableName($user['uid']);
                
        $newPerson = array('name'=> $user['name'],
                           'sex'=> $sex,
                           'tinyurl'=> $user['tinyurl'],
                           'headurl'=> $user['headurl'],
                           'shop_id'=> $shop_id,
                           'updated'=> $updated);
        return $this->_mg->$tname->update(array('uid' => (string)$user['uid']), array('$set' => $newPerson), array('upsert' => true));
    }
    
    public function updatePerson($uid, $info)
    {
        $info['updated'] = time();
        $tname = $this->getTableName($uid);
        
        $this->_mg->$tname->update(array('uid' => (string)$uid), array('$set' => $info), array('upsert' => true));
    }

    public function deletePerson($uid)
    {
        $tname = $this->getTableName($uid);
        return $this->_mg->$tname->remove(array('uid' => (string)$uid));
    }
    
    public function addPersonAry($array)
    {
        $tname = $this->table_user;
        return $this->_mg->$tname->batchInsert($array);
    }

}