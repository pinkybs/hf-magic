<?php


class Hapyfish2_Magic_Stat_Dal_Main
{
    protected static $_instance;
    
    private $_tb_day_main = 'day_main';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Magic_Stat_Dal_Main
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
    	$tbname = $this->_tb_day_main;
    	$sql = "SELECT log_time,total_count,add_user,add_user_male,add_user_female,active,active_male,active_female FROM $tbname WHERE log_time=:day";

        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $rdb = $db['r'];
    	
        $result = $rdb->fetchRow($sql, array('day' => $day));
        return $result;
    }
    
    public function getRange($begin, $end)
    {
    	$tbname = $this->_tb_day_main;
    	$sql = "SELECT log_time,total_count,add_user,add_user_male,add_user_female,active,active_male,active_female FROM $tbname WHERE log_time>=:begin AND log_time<=:end ORDER BY log_time DESC";
    	
        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('begin' => $begin, 'end' => $end));
    }
    
    public function update($day, $info)
    {
        $tbname = $this->_tb_day_main;
        
        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
        
    	$where = $wdb->quoteinto('log_time = ?', $day);
    	
        $wdb->update($tbname, $info, $where);
    }
}