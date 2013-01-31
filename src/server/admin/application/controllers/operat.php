<?php

defined('SYSPATH') or die('No direct access allowed.');

class Operat_Controller extends Controller {

    private static $skinpath = "operat/"; //模板名称
    private static $userinfo = array(); //用户信息
    private static $platlist = array(); //平台列表
    private static $platinfo = array(); //平台信息
    private static $mydate = array(); //全局日期格式数组
    private static $ajax = false; //判断是否是AJAX请求
    private static $key = "asdf312324"; //KEY

    public function __construct() {
        parent::__construct();
        $this->session = Session::instance();
        self::$userinfo = $this->session->get('userinfo');
        if (!self::$userinfo || self::$userinfo ['type'] != "1") {
            url::redirect(url::site('default'));
        }
        user::checkuser(self::$userinfo, Router::$method);
        self::$mydate = common::getdate();
        self::$ajax = array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) && $_SERVER ['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
        self::$platlist = Kohana::config('platform.list');
        self::$platinfo = $this->session->get('platinfo') ? $this->session->get('platinfo') : self::$platlist [0];
    }

    //获取GET或POST提交内空
    public function getparam($param) {
        $result = $this->input->post($param);
        if (!$result) {
            $result = $this->input->get($param);
        }
        return $result;
    }

    //生成远程获取数据URL，请求并返回数据
    private function geturl($str, $param, $pid = 0) {
        $apiurl = Kohana::config('apiurl.url1');
        $param ['key'] = md5(self::$key) . time();
        $url = self::$platlist [$pid] ['url'] . $apiurl [$str];
        return json_decode(common::getresult($url, $param), true);
    }

    public function index() {
        $view = new View(self::$skinpath . 'index');
        $view->render(true);
    }

    public function main() {
        $view = new View(self::$skinpath . 'main');
        $view->render(true);
    }

    public function left() {
        $view = new View(self::$skinpath . 'left');
        $ulevel = self::$userinfo ['level'];
        switch ($ulevel) {
            case 0 :
                $ulevel = "管理员";
                break;
            case 1 :
                $ulevel = "普通用户";
                break;
            default :
                break;
        }
        $view->set('userinfo', self::$userinfo);
        $view->set('platinfo', self::$platinfo);
        $view->ulevel = $ulevel;
        $view->platlist = json_encode(self::$platlist);
        $view->nowday = self::$mydate['nowday'];
        $view->render(true);
    }

    //切换选择平台
    public function changeplant() {
        $platid = $this->getparam('date') ? $this->getparam('date') : 0;
        $arr = array();
        $arr ['id'] = $platid;
        $arr ['title'] = self::$platlist [$platid] ['title'];
        $this->session->set('platinfo', $arr);
        echo "ok";
        exit();
    }

    //首页面
    public function body() {
        $view = new View(self::$skinpath . 'body');
        $platlist = self::$platlist;
        $view->plist = json_encode($platlist);
        $view->set('platformlist', $platlist);
        $view->render(true);
    }

    //公告管理
    public function notemanage() {
        if (self::$ajax) {
            $noteurl = "media/static/file/note.json";
            $param ['key'] = md5(self::$key) . time();
            $url = self::$platlist [self::$platinfo['id']] ['static'] . $noteurl;
            $temp = common::getresult($url, $param);
            $rs = json_decode($temp, true);
            $cmd = $this->getparam('cmd');
            $id = $this->getparam('id');
            switch ($cmd) {
                case "add":
                    $rs[count($rs)] = array('title' => $this->getparam('stitle'), 'type' => $this->getparam('stype'), 'link' => $this->getparam('slink'), 'date' => self::$mydate['nowdtime']);
                    $str = json_encode($rs);
                    $data = array('str' => $str);
                    $result = $this->geturl('rwritinnote', $data, self::$platinfo['id']);
                    echo $result['status'];
                    break;
                case "edit":
                    $rs[$id] = array('title' => $this->getparam('stitle'), 'type' => $this->getparam('stype'), 'link' => $this->getparam('slink'), 'date' => self::$mydate['nowdtime']);
                    $str = json_encode($rs);
                    $data = array('str' => $str);
                    $result = $this->geturl('rwritinnote', $data, self::$platinfo['id']);
                    echo $result['status'];
                    break;
                case "del":
                    array_splice($rs, $id, 1);
                    $str = json_encode($rs);
                    $data = array('str' => $str);
                    $result = $this->geturl('rwritinnote', $data, self::$platinfo['id']);
                    echo $result['status'];
                    break;
                default :
                    echo $temp;
                    break;
            }
            exit();
        } else {
            $view = new View(self::$skinpath . 'notemanage');
            $view->render(true);
        }
    }

    //单个ITEM发放
    public function itemsingle() {
        if (self::$ajax) {
            $uid = $this->getparam('uid');
            $item = $this->getparam('item');
            $num = $this->getparam('num');
            $str = json_encode(array("uid" => array($uid), "item" => array(array("itemid" => $item, "num" => $num))));
            $data = array('str' => $str);
            $result = $this->geturl('ritemrelease', $data, self::$platinfo['id']);
            if ($result ['status'] == 1) {
                if ($result['info'][0]['uid'] == $uid) {
                    echo "1";
                    exit();
                }
            }
            echo "0";
            exit();
        } else {
            $view = new View(self::$skinpath . 'itemsingle');
            $view->render(true);
        }
    }

    //单个装饰物发放
    public function buildingle() {
        if (self::$ajax) {
            $uid = $this->getparam('uid');
            $item = $this->getparam('item');
            $num = $this->getparam('num');
            $str = json_encode(array("uid" => array($uid), "item" => array(array("itemid" => $item, "num" => $num))));
            $data = array('str' => $str);
            $result = $this->geturl('rbuildrelease', $data, self::$platinfo['id']);
            if ($result ['status'] == 1) {
                if ($result['info'][0]['uid'] == $uid) {
                    echo "1";
                    exit();
                }
            }
            echo "0";
            exit();
        } else {
            $view = new View(self::$skinpath . 'buildingle');
            $view->render(true);
        }
    }

    //ITEM发放工具
    public function itemrelease() {
        if ($this->getparam("but_issuelease")) {
            $file = common::fileup($_FILES['file_upload']);
            if (!$file) {
                common::alertinfo("UID为空，或者格式不正确！", url::site('operat/itemrelease'));
            }
            $item = $this->getparam("hid_item");
            $t = array("uid" => $file, "item" => json_decode($item, true));
            $str = json_encode($t);
            $data = array('str' => $str);
            $result = $this->geturl('ritemrelease', $data, self::$platinfo['id']);
            $arr = array();
            if ($result ['status'] == 1) {
                $arr = $result ['info'];
            }
            $rs = array();
            $k = 0;
            for ($i = 0; $i < count($t['uid']); $i++) {
                for ($j = 0; $j < count($t['item']); $j++) {
                    $rs[$k] = array("uid" => $t['uid'][$i], "itemid" => $t['item'][$j]['itemid'], "num" => $t['item'][$j]['num']);
                    $k++;
                }
            }
            $view = new View(self::$skinpath . 'itemreleass');
            $view->set("rs", $rs);
            $view->set("info", $arr);
            $view->render(true);
        } else {
            $arr = array();
            $result = $this->geturl("itemrelease", $arr, self::$platinfo['id']);
            if ($result ['status'] == 1) {
                $arr = $result ['info'];
            }
            $view = new View(self::$skinpath . 'itemrelease');
            $view->set("items", $arr);
            $view->render(true);
        }
    }

    //装饰物发放工具
    public function buildrelease() {
        if ($this->getparam("but_issuelease")) {
            $file = common::fileup($_FILES['file_upload']);
            if (!$file) {
                common::alertinfo("UID为空，或者格式不正确！", url::site('operat/buildrelease'));
            }
            $item = $this->getparam("hid_item");
            $t = array("uid" => $file, "item" => json_decode($item, true));
            $str = json_encode($t);
            $data = array('str' => $str);
            $result = $this->geturl('rbuildrelease', $data, self::$platinfo['id']);
            $arr = array();
            if ($result ['status'] == 1) {
                $arr = $result ['info'];
            }
            $rs = array();
            $k = 0;
            for ($i = 0; $i < count($t['uid']); $i++) {
                for ($j = 0; $j < count($t['item']); $j++) {
                    $rs[$k] = array("uid" => $t['uid'][$i], "itemid" => $t['item'][$j]['itemid'], "num" => $t['item'][$j]['num']);
                    $k++;
                }
            }
            $view = new View(self::$skinpath . 'buildreleass');
            $view->set("rs", $rs);
            $view->set("info", $arr);
            $view->render(true);
        } else {
            $arr = array();
            $result = $this->geturl("buildrelease", $arr, self::$platinfo['id']);
            if ($result ['status'] == 1) {
                $arr = $result ['info'];
            }
            $view = new View(self::$skinpath . 'buildrelease');
            $view->set("items", $arr);
            $view->render(true);
        }
    }

}
