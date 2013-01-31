<?php

class Hapyfish2_Admin_Dal_Basic
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Admin_Dal_Basic
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getBasicList($tbName)
    {
        $db = Hapyfish2_Db_Factory::getBasicDB('db_0');
        $rdb = $db['r'];
    	$sql = "SELECT * FROM $tbName ";
        return $rdb->fetchAll($sql);
    }


    public function addInfo($tbName, $info)
    {
    	$db = Hapyfish2_Db_Factory::getBasicDB('db_0');
        $wdb = $db['w'];

        $aryCols = array();
        $aryVals = array();
        $aryColVal = array();
        foreach ($info as $key=>$val) {
            $aryCols[] = $key;
            $aryVals[] = $wdb->quote($val);
            $aryColVal[] = $key . '=' . $wdb->quote($val);
        }


        $sql = "INSERT INTO $tbName (" . implode(',', $aryCols) . ") VALUES"
              . '(' . implode(',', $aryVals) . ')'
              . ' ON DUPLICATE KEY UPDATE '
              . implode(',', $aryColVal);
info_log($sql, 'aa');
        return $wdb->query($sql);
    }

    public function deleteInfo($tbName, $field, $selVal)
    {
        $db = Hapyfish2_Db_Factory::getBasicDB('db_0');
        $wdb = $db['w'];

        $sql = "DELETE FROM $tbName WHERE $field=$selVal";
        return $wdb->query($sql);
    }

}