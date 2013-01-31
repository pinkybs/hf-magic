<?php

defined('SYSPATH') or die('No direct access allowed.');

class Zoperating_Controller extends Role_Controller {

    private static $result = array("status" => 0, "info" => NULL);
    private static $mydate = array(); //全局日期格式数组
    private static $key = "asdf312324"; //密钥KEY

    public function __construct() {
        parent::__construct();
        self::$mydate = $this->getDate();
        $this->checkKey ( 'key' );
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

    public function c_NoteJson() {
        $str = $this->getParam('str');
        $r = file_put_contents(DOCROOT . 'media/static/file/note.json', $str);
        if ($r == strlen($str)) {
            self::$result ['status'] = 1;
            self::$result ['info'] = "Write success";
        } else {
            self::$result ['info'] = "Writing failure";
        }
        echo json_encode(self::$result);
        exit();
    }

    //返回所有物品
    public function getItemRelease() {
        $basic_model = new Basic_Model();
        $data = $basic_model->getItemList();
        $arr = array();
        $i = 0;
        foreach ($data as $item) {
            $arr[$i]['id'] = $item[0]['id'];
            $arr[$i]['name'] = $item[0]['name'];
            $arr[$i]['type'] = $item[0]['type'];
            $i++;
        }
        self::checkResult($arr);
    }

    //返回所有装饰物
    public function getBuildRelease() {
        $basic_model = new Basic_Model();
        $data = $basic_model->getBuildingList();
        $arr = array();
        $i = 0;
        foreach ($data as $item) {
            $arr[$i]['id'] = $item[0]['id'];
            $arr[$i]['name'] = $item[0]['name'];
            $arr[$i]['type'] = $item[0]['type'];
            $i++;
        }
        self::checkResult($arr);
    }

    public function c_ItemRelease() {
        $str = $this->getParam('str');
        $arr = json_decode($str, true);
        $rs = array();
        $k = 0;
        for ($i = 0; $i < count($arr['uid']); $i++) {
            $item_model = Item_Model::instance($arr['uid'][$i]);
            for ($j = 0; $j < count($arr['item']); $j++) {
                $item_model->incrementUserItem($arr['uid'][$i], $arr['item'][$j]['itemid'], array('count' => $arr['item'][$j]['num']));
                $rs[$k] = array("uid" => $arr['uid'][$i], "itemid" => $arr['item'][$j]['itemid'], "num" => $arr['item'][$j]['num']);
                $k++;
            }
        }
        self::checkResult($rs);
    }

    public function c_BuildRelease() {
        $str = $this->getParam('str');
        $arr = json_decode($str, true);
        $rs = array();
        $k = 0;
        for ($i = 0; $i < count($arr['uid']); $i++) {
            $build_model = Building_Model::instance($arr['uid'][$i]);
            for ($j = 0; $j < count($arr['item']); $j++) {
                for ($z = 0; $z < $arr['item'][$j]['num']; $z++) {
                    $build_model->insertById($arr['item'][$j]['itemid']);
                }
                $rs[$k] = array("uid" => $arr['uid'][$i], "itemid" => $arr['item'][$j]['itemid'], "num" => $arr['item'][$j]['num']);
                $k++;
            }
        }
        self::checkResult($rs);
    }

}