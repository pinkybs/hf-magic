<?php

defined('SYSPATH') or die('No direct access allowed.');

class Stats_task_Model {

    protected $dbs;
    protected static $table_users_install = "users_install";
    protected static $table_users_newbie = "users_newbie";
    protected static $table_users_active = "users_active";
    protected static $table_users_agrowth = "users_agrowth";
    protected static $table_prefix = "magic_";
    protected static $instance;

    public function __construct() {
        $db_config_name = 'statis';
        $config = Kohana::config('database.' . $db_config_name);
        $this->dbs = Database::instance($db_config_name, $config);
    }

    public static function instance() {
        $class_name = get_called_class();
        self::$instance [$class_name] = new $class_name ();
        return self::$instance [$class_name];
    }

    public static function getTableName($tname) {
        return self::$table_prefix . $tname;
    }

    //插入一条记录
    public function addUsersInstall($data) {
        $query = $this->dbs->insert(self::$table_users_install, $data);
        return $query->insert_id();
    }

    public function addUsersActive($data) {
        $query = $this->dbs->insert(self::$table_users_active, $data);
        return $query->insert_id();
    }

    public function addUsersNewbie($data) {
        $query = $this->dbs->insert(self::$table_users_newbie, $data);
        return $query->insert_id();
    }

    public function addUsersAgrowth($data) {
        $query = $this->dbs->insert(self::$table_users_agrowth, $data);
        return $query->insert_id();
    }

    //以时间查询返回行数
    public function getUsersCreateByTime($time) {
        $sql = "select * from " . self::getTableName(self::$table_users_install) . " where time = '{$time}'";
        $query = $this->dbs->query($sql);
        return $query->result(FALSE);
    }

    public function getUsersNewBieByTime($time) {
        $sql = "select * from " . self::getTableName(self::$table_users_newbie) . " where time = '{$time}'";
        $query = $this->dbs->query($sql);
        return $query->result(FALSE);
    }

    public function getUsersActiveByTime($time) {
        $sql = "select * from " . self::getTableName(self::$table_users_active) . " where time = '{$time}'";
        $query = $this->dbs->query($sql);
        return $query->result(FALSE);
    }

    public function getUsersAgrowthByTime($time) {
        $sql = "select * from " . self::getTableName(self::$table_users_agrowth) . " where time = '{$time}'";
        $query = $this->dbs->query($sql);
        return $query->result(FALSE);
    }

    //根据日期查询
    public function getUsersCreateByDate($date) {
        $sql = "select * from " . self::getTableName(self::$table_users_install) . " where left(time,10) = '{$date}'";
        $query = $this->dbs->query($sql);
        return $query->result(FALSE);
    }

    public function getUsersNewBieByDate($sdate, $edate) {
        $sql = "select * from " . self::getTableName(self::$table_users_newbie) . " where left(time, 10)>='{$sdate}' and left(time, 10)<='{$edate}'";
        $query = $this->dbs->query($sql);
        return $query->result(FALSE);
    }

    public function getUsersActiveByDate($date) {
        $sql = "select * from " . self::getTableName(self::$table_users_active) . " where left(time,10) = '{$date}'";
        $query = $this->dbs->query($sql);
        return $query->result(FALSE);
    }

    public function getUsersInstallTotal() {
        $sql = "select sum(num) as totalrow from " . self::getTableName(self::$table_users_install);
        $query = $this->dbs->query($sql);
        return $query->result(FALSE);
    }

    public function getUsersAgrowthByDate($sdate, $edate) {
        $sql = "select * from " . self::getTableName(self::$table_users_agrowth) . " where left(time, 10)>='{$sdate}' and left(time, 10)<='{$edate}'";
        $query = $this->dbs->query($sql);
        return $query->result(FALSE);
    }

}