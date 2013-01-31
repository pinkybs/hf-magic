<?php

class Hapyfish2_Magic_Stat_Dal_DailyPayment
{
    protected static $_instance;
    protected static $dbadp;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Stat_Dal_DailyPayment
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
            $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
            self::$dbadp = $db['w'];
        }
        return self::$_instance;
    }

    //id 0-9
    public function getPayTableName($id)
    {
    	//return 'Magic_user_payment_' . $id;
    	return 'magic_user_payorder';
    }

    public function getDailyPaymentStat($uid, $id, $startDate, $endDate)
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

    	$tbname = $this->getPayTableName($id);
    	$sql = "SELECT IFNULL(SUM(gold),0) as gold, IFNULL(SUM(amount),0) as amount,COUNT(orderid) as cnt FROM $tbname
    			WHERE order_time>=$startDate AND order_time<$endDate AND status=1 ";

        return $rdb->fetchRow($sql);
    }


    public function getRow($date)
    {
    	$tbname = 'day_payment';
    	$wdb = self::$dbadp;
		$sql = "SELECT * FROM $tbname WHERE log_time=:log_time ";

        return $wdb->fetchRow($sql, array('log_time'=>$date));
    }

    public function insert($info)
    {
    	$tbname = 'day_payment';
    	$wdb = self::$dbadp;

    	return $wdb->insert($tbname, $info);
    }

	/**
     * update by multiple field name
     *
     * @param integer $uid
     * @param array $param
     * @return void
     */
    public function updateByMultipleField($date, $param)
    {
        $change = array();
        foreach ( $param as $k => $v ) {
            $change[] = $k . '=' . $k . '+' . $v;
        }
        $s1 = join(',', $change);

        $tbname = 'day_payment';
    	$wdb = self::$dbadp;;
        $sql = "UPDATE $tbname SET $s1 WHERE log_time=:log_time ";

        return $wdb->query($sql, array('log_time'=>$date));
    }

    public function delete($date)
    {
    	$tbname = 'day_payment';
    	$wdb = self::$dbadp;;
    	$sql = "DELETE FROM $tbname WHERE log_time=:log_time ";

        return $wdb->query($sql, array('log_time'=>$date));
    }

}