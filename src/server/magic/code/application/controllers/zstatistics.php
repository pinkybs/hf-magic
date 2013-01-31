<?php

defined('SYSPATH') or die('No direct access allowed.');

class Zstatistics_Controller extends Controller {

    private static $result = array("status" => 0, "info" => NULL);
    private static $mydate = array(); //全局日期格式数组
    private static $key = "asdf312324"; //密钥KEY

    public function __construct() {
        parent::__construct();
        self::$mydate = $this->getDate();
        //$this->checkKey ( 'key' );
    }

    private function getDate() {
        $time = time();
        $arr = array();
        $arr ['nowdtime'] = date("Y-m-d H:i:s", $time);
        $arr ['prevstime'] = date("Y-m-d H:00:00", $time - 3600);
        $arr ['prevetime'] = date("Y-m-d H:59:59", $time - 3600);
        $arr ['prevday'] = date("Y-m-d", $time - 3600 * 24);
        $arr ['prevsdate'] = date("Y-m-d 00:00:00", $time - 3600 * 24);
        $arr ['prevedate'] = date("Y-m-d 23:59:59", $time - 3600 * 24);
        $arr ['firstmdate'] = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('n'), 1, date('Y')));
        $arr ['lastmdate'] = date('Y-m-d 23:59:59', mktime(0, 0, 0, date('n'), date('t'), date('Y')));
        return $arr;
    }

    //检测密钥是否正确，密钥格式： $key=md5("asdf312324").time();
    private function checkKey($key) {
        $t = time();
        $key = $this->getParam($key);
        $k = self::$key;
        if (substr($key, 0, 32) != $k) {
            self::$result ['info'] = "key error";
            echo json_encode(self::$result);
            exit();
        }
        $s = substr($key, 32);
        if ($s != $t) {
            if (intval($s) <= ($t - 60) || intval($s) >= ($t + 60)) {
                self::$result ['info'] = "key error";
                echo json_encode(self::$result);
                exit();
            }
        }
    }

    //检测提交参数是否存在
    private static function checkParam($param) {
        if (empty($param) || !isset($param)) {
            return false;
        }
        return true;
    }

    //返回结果状态
    private static function checkRow($v, $m) {
        self::$result ['status'] = $v;
        self::$result ['info'] = $m;
        echo json_encode(self::$result);
        exit();
    }

    //判断数组是否有值并返回
    private static function checkResult($data) {
        if (count($data)) {
            self::$result ['status'] = 1;
            self::$result ['info'] = $data;
            echo json_encode(self::$result);
        } else {
            self::$result ['info'] = 'Data is empty';
            echo json_encode(self::$result);
        }
        exit();
    }

    //获取GET或POST提交内空
    public function getParam($param) {
        $result = $this->input->post($param);
        if (!$result) {
            $result = $this->input->get($param);
        }
        return $result;
    }

    //计划任务，小时执行-----每小时获取一次用户信息，并插入记录
    public function w_getRoleCreateByHour() {
        $stats_model = Stats_Model::instance();
        $where = array("created_time>=" => self::$mydate ['prevstime'], "created_time<=" => self::$mydate ['prevetime']);
        //$where = array ("created_time>=" => "0000-00-00 00:00:00", "created_time<=" => self::$mydate['prevetime'] );
        $data = $stats_model->getInfoCreatByTime($where);
        $arr = array();
        $arr ['num'] = count($data);
        $arr ['time'] = self::$mydate ['prevstime'];
        $arr ['inerttime'] = self::$mydate ['nowdtime'];
        $statstask_model = Stats_task_Model::instance();
        $row = $statstask_model->getUsersCreateByTime(self::$mydate ['prevstime']);
        if (!$row->count()) {
            $data = $statstask_model->addUsersInstall($arr);
            self::checkRow(1, "Insert success");
        } else {
            self::checkRow(0, "Data already exist");
        }
    }

    //半划任务，每小时执行------每小时获取活跃的用户，并插入记录
    public function w_getRoleActiveByHour() {
        $stats_model = Stats_Model::instance();
        $where = array("login_time" => strtotime(self::$mydate ['prevstime']), "login_time" => strtotime(self::$mydate ['prevetime']));
        //$where = array("login_time>="=>"0", "login_time<="=>strtotime(self::$mydate['prevetime']));
        $data = $stats_model->getInfoLoginByTime($where);
        $arr = array();
        $arr ['num'] = count($data);
        $arr ['time'] = self::$mydate ['prevstime'];
        $arr ['inerttime'] = self::$mydate ['prevetime'];
        $statstask_model = Stats_task_Model::instance();
        $row = $statstask_model->getUsersActiveByTime(self::$mydate ['prevstime']);
        if (!$row->count()) {
            $data = $statstask_model->addUsersActive($arr);
            self::checkRow(1, "Insert success");
        } else {
            self::checkRow(0, "Data already exist");
        }
    }

    //计划任务，每天执行-----每天获取一次用户增长和活跃情况，并插入记录
    public function w_getActiveGrowthByDay() {
        $stats_model = Stats_Model::instance();
        $whereactive = array("login_time" => strtotime(self::$mydate ['prevsdate']), "login_time" => strtotime(self::$mydate ['prevedate']));
        //$whereactive = array("login_time>="=>"0", "login_time<="=>strtotime(self::$mydate['prevedate']));
        $dataactive = $stats_model->getInfoLoginByTime($whereactive);
        $whereinstall = array("created_time>=" => self::$mydate ['prevsdate'], "created_time<=" => self::$mydate ['prevedate']);
        $datainstall = $stats_model->getInfoCreatByTime($whereinstall);
        $alevel = array();
        foreach ($dataactive as $item) {
            $alevel [] = $item ['level'];
        }
        $arr = array();
        $arr ['numactive'] = count($dataactive);
        $arr ['numinstall'] = count($datainstall);
        $arr ['levelratio'] = json_encode(array_count_values($alevel));
        $arr ['time'] = self::$mydate ['prevday'];
        $arr ['inerttime'] = self::$mydate ['nowdtime'];
        $statstask_model = Stats_task_Model::instance();
        $row = $statstask_model->getUsersAgrowthByTime(self::$mydate ['prevday']);
        if (!$row->count()) {
            $data = $statstask_model->addUsersAgrowth($arr);
            self::checkRow(1, "Insert success");
        } else {
            self::checkRow(0, "Data already exist");
        }
    }

    //计划任务，每天执行-----每天获取一次用户任务完成情况，并插入记录
    public function w_getRoleNewBieByDay() {
        $stats_model = Stats_Model::instance();
        $where = array ("created_time>=" => self::$mydate['prevsdate'], "created_time<=" => self::$mydate['prevedate'] );
        //$where = array("created_time>=" => "0000-00-00 00:00:00", "created_time<=" => self::$mydate ['prevedate']);
        $data = $stats_model->getInfoCreatByTime($where);

        $completerow = array(0, 0, 0, 0, 0, 0, 0);
        $ratio = array("0%", "0%", "0%", "0%", "0%", "0%", "0%");
        for ($i = 0; $i < count($data); $i++) {
            $newbie = json_decode($data [$i] ['newbie'], true);
            for ($j = 0; $j < count($newbie [0]); $j++) {
                if ($newbie [0] [$j] [1] == 2) {
                    $completerow [$j] += 1;
                    if ($completerow [$j]) {
                        $ratio [$j] = round((($completerow [$j] / count($data)) * 100), 2) . "%";
                    } else {
                        $ratio [$j] = "0%";
                    }
                }
            }
        }
        $arr = array();
        $arr ['num'] = count($data);
        $arr ['newbie'] = json_encode($completerow);
        $arr ['time'] = self::$mydate ['prevday'];
        $arr ['ratio'] = json_encode($ratio);
        $arr ['inerttime'] = self::$mydate ['nowdtime'];
        $statstask_model = Stats_task_Model::instance();
        $row = $statstask_model->getUsersNewBieByTime(self::$mydate ['prevday']);
        if (!$row->count()) {
            $data = $statstask_model->addUsersNewbie($arr);
            self::checkRow(1, "Insert success");
        } else {
            self::checkRow(0, "Data already exist");
        }
    }

    //测试记录用户消费
    public function w_test() {
        $buyitem_model = Buyitem_Log_Model::instance(Role::getOwnRoleId());
        //插入一条用户消费记录,三个参数$itemid=物品参数,$itemnum=物品数量,$itemprice=物品单价
        echo $buyitem_model->insertBuyItem(8547, 154, 154);
    }

    //小时安装量分布图
    public function getRoleCreat() {
        $date = self::checkParam($this->getParam('date')) ? $this->getParam('date') : self::$mydate ['prevday'];
        $statstask_model = Stats_task_Model::instance();
        $data = $statstask_model->getUsersCreateByDate($date);
        $arr = array();
        $i = 0;
        foreach ($data as $item) {
            $arr [$i] ['id'] = $item ['id'];
            $arr [$i] ['num'] = $item ['num'];
            $arr [$i] ['time'] = $item ['time'];
            $i++;
        }
        self::checkResult($arr);
    }

    //新手引导流失统计
    public function getRoleNewBie() {
        $sdate = self::checkParam($this->getParam('sdate')) ? $this->getParam('sdate') : self::$mydate ['firstmdate'];
        $edate = self::checkParam($this->getParam('edate')) ? $this->getParam('edate') : self::$mydate ['lastmdate'];
        $statstask_model = Stats_task_Model::instance();
        $data = $statstask_model->getUsersNewBieByDate($sdate, $edate);
        $arr = array();
        $i = 0;
        foreach ($data as $item) {
            $arr [$i] ['id'] = $item ['id'];
            $arr [$i] ['num'] = $item ['num'];
            $arr [$i] ['newbie'] = $item ['newbie'];
            $arr [$i] ['ratio'] = $item ['ratio'];
            $arr [$i] ['time'] = $item ['time'];
            $i++;
        }
        self::checkResult($arr);
    }

    //用户消费统计
    public function getConsumstatis() {
        $sdate = self::checkParam($this->getParam('sdate')) ? $this->getParam('sdate') : self::$mydate ['firstmdate'];
        $edate = self::checkParam($this->getParam('edate')) ? $this->getParam('edate') : self::$mydate ['lastmdate'];
        $stats_model = Stats_Model::instance();
        $where = array("sumtime>=" => $sdate, "sumtime<=" => $edate);
        $data = $stats_model->getBuyItemLogByTime($where);
        self::checkResult($data);
    }

    //小时活跃数
    public function getRoleActive() {
        $date = self::checkParam($this->getParam('date')) ? $this->getParam('date') : self::$mydate ['prevday'];
        $statstask_model = Stats_task_Model::instance();
        $data = $statstask_model->getUsersActiveByDate($date);
        $arr = array();
        $i = 0;
        foreach ($data as $item) {
            $arr [$i] ['id'] = $item ['id'];
            $arr [$i] ['num'] = $item ['num'];
            $arr [$i] ['time'] = $item ['time'];
            $i++;
        }
        self::checkResult($arr);
    }

    //查询全部安装人数
    public function getRoleTotalInstall() {
        $statstask_model = Stats_task_Model::instance();
        $data = $statstask_model->getUsersInstallTotal();
        $arr = array();
        foreach ($data as $item) {
            $arr ['totalrow'] = $item ['totalrow'];
        }
        self::checkResult($arr);
    }

    //日活跃、日增长用户
    public function getActiveGrowth() {
        $sdate = self::checkParam($this->getParam('sdate')) ? $this->getParam('sdate') : self::$mydate ['firstmdate'];
        $edate = self::checkParam($this->getParam('edate')) ? $this->getParam('edate') : self::$mydate ['lastmdate'];
        $statstask_model = Stats_task_Model::instance();
        $data = $statstask_model->getUsersAgrowthByDate($sdate, $edate);
        $arr = array();
        $i = 0;
        foreach ($data as $item) {
            $arr [$i] ['id'] = $item ['id'];
            $arr [$i] ['numinstall'] = $item ['numinstall'];
            $arr [$i] ['numactive'] = $item ['numactive'];
            $arr [$i] ['time'] = $item ['time'];
            $i++;
        }
        self::checkResult($arr);
    }

    //获取活跃用户等级
    public function getActiveLevel() {
        $date = self::checkParam($this->getParam('date')) ? $this->getParam('date') : self::$mydate ['prevday'];
        $statstask_model = Stats_task_Model::instance();
        $data = $statstask_model->getUsersAgrowthByDate($date, $date);
        $arr = array();
        $i = 0;
        foreach ($data as $item) {
            $arr [$i] ['levelratio'] = $item ['levelratio'];
            $arr [$i] ['time'] = $item ['time'];
            $i++;
        }
        self::checkResult($arr);
    }

}