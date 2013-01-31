<?php


class Hapyfish2_Magic_Stat_Dal_MainHour
{
    protected static $_instance;
    
    private $_tb_day_main_hour = 'day_main_hour';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Magic_Stat_Dal_MainHour
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getDay($day)
    {
    	$tbname = $this->_tb_day_main_hour;
    	$stime = $day . '00';
    	$etime = $day . '23';
    	$sql = "SELECT log_time,add_user,active_user FROM $tbname WHERE log_time>=:stime AND log_time<=:etime";
    	
        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('stime' => $stime, 'etime' => $etime));
    }
    
}