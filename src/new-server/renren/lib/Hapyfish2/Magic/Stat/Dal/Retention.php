<?php


class Hapyfish2_Magic_Stat_Dal_Retention
{
    protected static $_instance;
    
    private $_tb_day_user_retention = 'day_user_retention';
    private $_tb_day_main = 'day_main';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Magic_Stat_Dal_Retention
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getRetention($day)
    {
    	$tbRetention = $this->_tb_day_user_retention;
    	$tbMain = $this->_tb_day_main;
    	$sql = "SELECT b.add_user, a.* FROM $tbRetention AS a,$tbMain AS b WHERE a.log_time=b.log_time AND a.log_time=:day";
    	
        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('day' => $day));
    }
}