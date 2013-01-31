<?php


class Hapyfish2_Magic_Stat_Dal_DaycLoadTm
{
    protected static $_instance;

    private $_tbName = 'day_cload_tm';

    /**
     * Single Instance
     *
     * @return Hapyfish2_Magic_Stat_Dal_DaycLoadTm
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getRow($day)
    {
    	$tbname = $this->_tbName;
    	$sql = "SELECT * FROM $tbname WHERE log_time=:day";

        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $rdb = $db['r'];

        return $rdb->fetchRow($sql, array('day' => $day));
    }

    public function listData($dtBegin, $dtEnd)
    {
    	$tbname = $this->_tbName;
    	$sql = "SELECT * FROM $tbname WHERE log_time>=:dtBegin AND log_time<=:dtEnd";

        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $rdb = $db['r'];

        return $rdb->fetchAll($sql, array('dtBegin' => $dtBegin, 'dtEnd' => $dtEnd));
    }

    public function insert($info)
    {
    	$tbname = $this->_tbName;
        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];

        return $wdb->insert($tbname, $info);
    }

    public function update($day, $info)
    {
    	$tbname = $this->_tbName;
        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];

        $where = $wdb->quoteinto('log_time = ?', $day);
        return $wdb->update($tbname, $info, $where);
    }

    public function delete($day)
    {
        $tbname = $this->_tbName;
        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
    	$sql = "DELETE FROM $tbname WHERE log_time=:day ";

        return $wdb->query($sql, array('day'=>$day));
    }
}