<?php defined('SYSPATH') or die('No direct access allowed.');

class fcharts_Core {

    //生成小时安装量分布图
    public static function creatfcharByHourInstall($fname, $width, $height, $arr, $param) {
        $FC = new FusionCharts($fname, $width, $height);
        $FC->setSWFPath(url::fchartpath());
        $newarr [] = array();
        if (count($arr) > 0) {
            for ($i = 0; $i < count($arr); $i++) {
                $newarr [] = $arr [$i] ['num'];
            }
        }
        $str = intval(max($newarr)) + 5;
        $strParam = "caption=" . $param ['caption'] . ";xAxisName=" . $param ['xname'] . ";yAxisMaxValue=" . $str . ";yAxisMinValue=0;;yAxisName=" . $param ['yname'] . ";decimalPrecision=0;formatNumberScale=0;";
        $FC->setChartParams($strParam);
        if (count($arr) > 0) {
            for ($i = 0; $i < count($arr); $i++) {
                $FC->addChartData($arr [$i] ['num'], "name=" . date("H", strtotime($arr [$i] ['time'])) . "时");
            }
        }
        return $FC->renderChart();
    }

    public static function creatfcharByDayActerGrowth($fname, $width, $height, $arr, $param) {
        $FC = new FusionCharts($fname, $width, $height);
        $FC->setSWFPath(url::fchartpath());
        $newinstall [] = array();
        $newactive [] = array();
        if (count($arr) > 0) {
            for ($i = 0; $i < count($arr); $i++) {
                $newinstall [] = $arr [$i] ['numinstall'];
                $newactive [] = $arr [$i] ['numactive'];
            }
        }
        if (intval(max($newinstall)) >= intval(max($newactive))) {
            $str = intval(max($newinstall)) + 5;
        } else {
            $str = intval(max($newactive)) + 5;
        }
        $strParam = "caption=" . $param ['caption'] . ";hovercapbg=FFECAA;hovercapborder=F47E00;xAxisName=" . $param ['xname'] . ";yAxisMaxValue=" . $str . ";yAxisMinValue=0;;yAxisName=" . $param ['yname'] . ";decimalPrecision=0;formatNumberScale=0;";
        $FC->setChartParams($strParam);
        if (count($arr) > 0) {
            for ($i = 0; $i < count($arr); $i++) {
                $FC->addCategory(date("m-d", strtotime($arr [$i] ['time'])) . "日");
            }
            $FC->addDataset("用户增长度");
            for ($i = 0; $i < count($arr); $i++) {
                $FC->addChartData($arr [$i] ['numinstall']);
            }
            $FC->addDataset("用户活跃度");
            for ($i = 0; $i < count($arr); $i++) {
                $FC->addChartData($arr [$i] ['numactive']);
            }
        }
        return $FC->renderChart();
    }

}