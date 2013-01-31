<?php


class Hapyfish2_Magic_Dal_PayOrder
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Island_Dal_PayOrder
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getPayOrderTableName($uid)
    {
    	return 'magic_user_payorder';
    }

    public function listOrder($uid)
    {
        $tbname = $this->getPayOrderTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $sql = "SELECT * FROM $tbname WHERE uid=:uid ORDER BY order_time DESC LIMIT 0,30";
        return $rdb->fetchAll($sql, array('uid' => $uid));
    }

    public function getOrder($uid, $orderid)
    {
        $tbname = $this->getPayOrderTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $sql = "SELECT * FROM $tbname WHERE orderid=:orderid AND uid=:uid";
        return $rdb->fetchRow($sql, array('orderid' => $orderid, 'uid' => $uid));
    }

    public function regOrder($uid, $info)
    {
        $tbname = $this->getPayOrderTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
    	return $wdb->insert($tbname, $info);
    }

    public function completeOrder($uid, $orderid, $info)
    {
        $tbname = $this->getPayOrderTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
    	$where = $wdb->quoteinto('orderid = ?', $orderid);

        return $wdb->update($tbname, $info, $where);
    }

}