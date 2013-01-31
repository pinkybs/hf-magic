<?php

class Hapyfish2_Magic_Event_Dal_Basic
{
    protected static $_instance;

    protected function getDB()
    {
    	$key = 'db_0';
    	return Hapyfish2_Db_Factory::getEventDB($key);
    }

    /**
     * Single Instance
     *
     * @return Hapyfish2_Magic_Event_Dal_Basic
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getCollectionList($eventCode)
    {
        $db = $this->getDB();
        $rdb = $db['r'];
        $eventCode = $rdb->quote($eventCode);
        $sql = "SELECT * FROM magic_event_basic_collection WHERE event_code=$eventCode";
        return $rdb->fetchAssoc($sql);
    }

}