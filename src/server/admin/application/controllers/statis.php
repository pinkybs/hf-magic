<?php

defined('SYSPATH') or die('No direct access allowed.');

class Statis_Controller extends Controller {

    private static $skinpath = "statis/"; //模板名称
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
        if (!self::$userinfo || self::$userinfo ['type'] != "0") {
            url::redirect(url::site('default'));
        }
        user::checkuser(self::$userinfo, Router::$method);
        self::$mydate = common::getdate();
        self::$ajax = array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) && $_SERVER ['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
        self::$platlist = Kohana::config('platform.list');
        self::$platinfo = $this->session->get('platinfo') ? $this->session->get('platinfo') : self::$platlist [0];
        include Kohana::find_file('vendor', 'FusionCharts_Gen');
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
        $apiurl = Kohana::config("apiurl.url0");
        $param ['key'] = md5(self::$key) . time();
        $url = self::$platlist [$pid] ['url'] . $apiurl [$str];
        return json_decode(common::getresult($url, $param), true);
    }

    public function index() {
        $view = new View(self::$skinpath . 'index');
        $view->render(true);
    }

    public function head() {
        $view = new View(self::$skinpath . 'head');
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
        $platid = $this->getparam("date") ? $this->getparam("date") : 0;
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

    //响应body页AJAX请求
    public function bodyajax() {
        $pid = $this->getparam("pid");
        $arr = array("date" => self::$mydate['nowday']);
        $installrs = $this->geturl("hourinstall", $arr, $pid);
        $data = array();
        if ($installrs ['status'] == 1) {
            $installrow = 0;
            for ($i = 0; $i < count($installrs ['info']); $i++) {
                $installrow += $installrs ['info'] [$i] ['num'];
            }
            $data ['installnum'] = $installrow;
        } else {
            $data ['installnum'] = 0;
        }
        $activers = $this->geturl("houractive", $arr, $pid);
        if ($activers ['status'] == 1) {
            $activerow = 0;
            for ($i = 0; $i < count($activers ['info']); $i++) {
                $activerow += $activers ['info'] [$i] ['num'];
            }
            $data ['activenum'] = $activerow;
        } else {
            $data ['activenum'] = 0;
        }
        $activetotal = $this->geturl("installtotal", $arr, $pid);
        if ($activetotal ['status'] == 1) {
            $data ['activeratio'] = round((($data ['activenum'] / $activetotal ['info'] ['totalrow']) * 100), 2) . "%";
        } else {
            $data ['activeratio'] = "0%";
        }
        echo json_encode($data);
        exit();
    }

    //小时安装量
    public function hourinstall() {
        if (self::$ajax) {
            $date = $this->getparam("date") ? $this->getparam("date") : self::$mydate ['prevday'];
            $arr = array("date" => $date);
            $result = $this->geturl("hourinstall", $arr, self::$platinfo['id']);
            if ($result ['status'] == 1) {
                if ($this->getparam("type") == "hour") {
                    $param = array("caption" => "24小时安装量分布图", "xname" => "时间", "yname" => "数量");
                    fcharts::creatfcharByHourInstall("Column3D", "1150", "500", $result ['info'], $param);
                }
                if ($this->getparam("type") == "data") {
                    $totalrow = 0;
                    for ($i = 0; $i < count($result ['info']); $i++) {
                        $totalrow += $result ['info'] [$i] ['num'];
                    }
                    $arr = array();
                    $i = 0;
                    foreach ($result ['info'] as $item) {
                        $nowhour = date("H", strtotime($item ['time']));
                        $t = intval($nowhour) + 1;
                        if ($t <= 9) {
                            $t = "0" . $t;
                        }
                        $arr [$i] ['time'] = $nowhour . "~" . $t;
                        $arr [$i] ['num'] = $item ['num'];
                        if ($totalrow) {
                            $arr [$i] ['ratio'] = round((($item ['num'] / $totalrow) * 100), 2) . "%";
                        } else {
                            $arr [$i] ['ratio'] = "0%";
                        }
                        $i++;
                    }
                    echo json_encode($arr);
                }
            } else {
                echo "0";
            }
        } else {
            $view = new View(self::$skinpath . 'hourinstall');
            $view->render(true);
        }
    }

    //新手引导流失统计
    public function noviceguide() {
        if (self::$ajax) {
            $sdate = $this->getparam("sdate") ? $this->getparam("sdate") : self::$mydate ['firstmdate'];
            $edate = $this->getparam("edate") ? $this->getparam("edate") : self::$mydate ['lastmdate'];
            $arr = array("sdate" => $sdate, "edate" => $edate);
            $result = $this->geturl("noviceguide", $arr, self::$platinfo['id']);
            if ($result ['status'] == 1) {
                echo json_encode($result ['info']);
            } else {
                echo "0";
            }
        } else {
            $view = new View(self::$skinpath . 'noviceguide');
            $view->render(true);
        }
    }

    //用户消费统计
    public function consumstatis() {
        if (self::$ajax) {
            $sdate = $this->getparam("sdate") ? $this->getparam("sdate") : self::$mydate ['firstmdate'];
            $edate = $this->getparam("edate") ? $this->getparam("edate") : self::$mydate ['lastmdate'];
            $arr = array("sdate" => $sdate, "edate" => $edate);
            $result = $this->geturl("consumstatis", $arr, self::$platinfo['id']);
            if ($result ['status'] == 1) {
                echo json_encode($result ['info']);
            } else {
                echo "0";
            }
        } else {
            $view = new View(self::$skinpath . 'consumstatis');
            $view->render(true);
        }
    }

    //小时活跃
    public function houractive() {
        if (self::$ajax) {
            $date = $this->getparam("date") ? $this->getparam("date") : self::$mydate ['prevday'];
            $arr = array("date" => $date);
            $result = $this->geturl("houractive", $arr, self::$platinfo['id']);
            if ($result ['status'] == 1) {
                if ($this->getparam("type") == "hour") {
                    $param = array("caption" => "24小时活跃度分布图", "xname" => "时间", "yname" => "数量");
                    fcharts::creatfcharByHourInstall("Column3D", "1150", "500", $result ['info'], $param);
                }
                if ($this->getparam("type") == "data") {
                    $totalrow = 0;
                    for ($i = 0; $i < count($result ['info']); $i++) {
                        $totalrow += $result ['info'] [$i] ['num'];
                    }
                    $arr = array();
                    $i = 0;
                    foreach ($result ['info'] as $item) {
                        $nowhour = date("H", strtotime($item ['time']));
                        $t = intval($nowhour) + 1;
                        if ($t <= 9) {
                            $t = "0" . $t;
                        }
                        $arr [$i] ['time'] = $nowhour . "~" . $t;
                        $arr [$i] ['num'] = $item ['num'];
                        if ($totalrow) {
                            $arr [$i] ['ratio'] = round((($item ['num'] / $totalrow) * 100), 2) . "%";
                        } else {
                            $arr [$i] ['ratio'] = "0%";
                        }
                        $i++;
                    }
                    echo json_encode($arr);
                }
            } else {
                echo "0";
            }
        } else {
            $view = new View(self::$skinpath . 'houractive');
            $view->render(true);
        }
    }

    //日活跃、日增长用户
    public function activegrowth() {
        if (self::$ajax) {
            $sdate = $this->getparam("sdate") ? $this->getparam("sdate") : self::$mydate ['firstmdate'];
            $edate = $this->getparam("edate") ? $this->getparam("edate") : self::$mydate ['lastmdate'];
            $arr = array("sdate" => $sdate, "edate" => $edate);
            $result = $this->geturl("activegrowth", $arr, self::$platinfo['id']);
            if ($result ['status'] == 1) {
                $param = array("caption" => "日活跃、日增长用户", "xname" => "时间", "yname" => "数量");
                fcharts::creatfcharByDayActerGrowth("MSLine", "1150", "500", $result ['info'], $param);
            } else {
                echo "0";
            }
        } else {
            $view = new View(self::$skinpath . 'activegrowth');
            $view->render(true);
        }
    }

    public function activelevel() {
        if (self::$ajax) {
            $date = $this->getparam("date") ? $this->getparam("date") : self::$mydate ['prevday'];
            $arr = array("date" => $date);
            $result = $this->geturl("activelevel", $arr, self::$platinfo['id']);
            if ($result ['status'] == 1) {
                $arrdate = array();
                $i = 0;
                foreach ($result ['info'] as $item) {
                    if (count(json_decode($item ['levelratio'], true)) != 0) {
                        $arrdate [$i] = json_decode($item ['levelratio'], true);
                        $i++;
                    }
                }
                $merge = $sum = array();
                foreach ($arrdate as $row) {
                    $merge = array_unique(array_merge($merge, array_keys($row)));
                }
                foreach ($arrdate as $row) {
                    foreach ($merge as $key) {
                        if (!isset($sum [$key]))
                            $sum [$key] = 0;
                        $sum [$key] += isset($row [$key]) ? $row [$key] : 0;
                    }
                }
                if (count($sum) == 0) {
                    echo "0";
                    exit();
                }
                $arr = $sum;
                unset($sum);
                $param = array("xname" => "等级", "yname" => "人数");
                $FC = new FusionCharts("Column3D", "1150", "500");
                $FC->setSWFPath(url::fchartpath());
                $FC2 = new FusionCharts("Column3D", "1150", "500");
                $FC2->setSWFPath(url::fchartpath());
                $str = intval(max($arr)) + 5;
                $strParam = "caption=日活跃用户等级分布(1--50级);xAxisName=" . $param ['xname'] . ";yAxisMaxValue=" . $str . ";yAxisMinValue=0;;yAxisName=" . $param ['yname'] . ";decimalPrecision=0;formatNumberScale=0;";
                $FC->setChartParams($strParam);
                $strParam2 = "caption=日活跃用户等级分布(51--100级);xAxisName=" . $param ['xname'] . ";yAxisMaxValue=" . $str . ";yAxisMinValue=0;;yAxisName=" . $param ['yname'] . ";decimalPrecision=0;formatNumberScale=0;";
                $FC2->setChartParams($strParam2);
                if ($this->getparam("type") == "min") {
                    if (count($arr) > 0) {
                        for ($i = 1; $i <= 50; $i++) {
                            if (in_array($i, array_keys($arr))) {
                                $FC->addChartData($arr [$i], "name=" . $i);
                            } else {
                                $FC->addChartData("0", "name=" . $i);
                            }
                        }
                    }
                    $FC->renderChart();
                }
                if ($this->getparam("type") == "max") {
                    if (count($arr) > 0) {
                        for ($i = 51; $i <= 100; $i++) {
                            if (in_array($i, array_keys($arr))) {
                                $FC2->addChartData($arr [$i], "name=" . $i);
                            } else {
                                $FC2->addChartData("0", "name=" . $i);
                            }
                        }
                    }
                    $FC2->renderChart();
                }
            } else {
                echo "0";
            }
        } else {
            $view = new View(self::$skinpath . 'activelevel');
            $view->render(true);
        }
    }

}
