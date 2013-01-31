<?php

defined('SYSPATH') or die('No direct access allowed.');

class Phptools_Controller extends Controller {

    protected $db;
    protected static $libnum = 0;
    protected static $tabnum = 0;
    protected static $pointables = array();

    public function __construct() {
        parent::__construct();
        self::$libnum = Kohana::config("base.cut_database_num");
        self::$tabnum = Kohana::config("base.cut_table_num");
        self::$pointables = Kohana::config('table.cut');
        $db_config_name = 'main';
        $config = Kohana::config('database.' . $db_config_name);
        $this->db = Database::instance($db_config_name, $config);
    }

    public function tool() {
        $this->getlibrary("main");
    }

    //返回本地表结构创建SQL语句
    private function getsql($oldtable, $newtable) {
        $sql = "SHOW CREATE TABLE $oldtable";
        $query = $this->db->query($sql);
        $str = substr_replace($query[0]["Create Table"], $newtable, 14, strlen($oldtable));
        return $str;
    }

    //获取本地各表结构
    private function getable() {
        $str = "Tables_in_" . $this->db->config['connection']['database'];
        $sql = "SHOW TABLES";
        $query = $this->db->query($sql);
        $arr = array();
        for ($i = 0; $i < $query->count(); $i++) {
            $arr[] = $query[$i][$str];
        }
        return $arr;
    }

    //创建分表
    private function creatable($name, $config) {
        $db = Database::instance($name, $config);
        $libname = $db->config['connection']['database'];
        $alltable = $this->getable();
        $arrtable = array_keys(self::$pointables);
        for ($i = 0; $i < count($alltable); $i++) {
            $tname = substr($alltable[$i], strlen($db->config['table_prefix']));
            if (in_array($tname, $arrtable)) {
                if (self::$pointables[$tname] > 0) {
                    for ($k = 0; $k < self::$pointables[$tname]; $k++) {
                        $tnames = $alltable[$i] . "_" . $k;
                        $sql = $this->getsql($alltable[$i], $tnames);
                        $db->query($sql);
                        $this->getecho($libname, $tnames);
                    }
                } else {
                    $sql = $this->getsql($alltable[$i], $alltable[$i]);
                    $db->query($sql);
                    $this->getecho($libname, $alltable[$i]);
                }
            } else {
                $sql = $this->getsql($alltable[$i], $alltable[$i]);
                $db->query($sql);
                $this->getecho($libname, $alltable[$i]);
            }
        }
    }

    private function getecho($libname, $tname) {
        $str = $libname . "库" . $tname . "表创建完成！</br>";
        echo $str;
    }

    //获取加载各分库
    private function getlibrary($name) {
        for ($i = 0; $i < self::$libnum; $i++) {
            $this->creatlib($name . "_" . $i);
        }
    }

    private function creatlib($name) {
    	
        $config = Kohana::config('database.' . $name);
        $dbname = $config['connection']['database'];
        $host = $config['connection']['host'];
        $user = $config['connection']['user'];
        $pass = $config['connection']['pass'];
        $con = @mysql_connect($host, $user, $pass);
        if ($con) {
            if (!@mysql_select_db($dbname, $con)) {
                $sql = "CREATE DATABASE `$dbname`CHARACTER SET utf8 COLLATE utf8_general_ci;";
                if (@mysql_query($sql, $con)) {
                    $this->creatable($name, $config);
                }
            }
        }
    }

}