<?php


class Hapyfish2_Magic_Stat_Dal_Payment
{
    protected static $_instance;
    private $_tb_day_payment = 'day_payment';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Magic_Stat_Dal_Payment
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getTableName()
    {
    	return $this->_tb_day_payment;
    }
    
    public function getPayment($day)
    {
    	$tbname = $this->getTableName();
    	$sql = "SELECT log_time,amount,gold,trans_count FROM $tbname WHERE log_time=:day";

        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('day' => $day));
    }

}