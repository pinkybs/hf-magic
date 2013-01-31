<?php

defined('SYSPATH') or die('No direct access allowed.');

class common_Core {

    //判断字符串是否为空
    public static function isempty($str) {
        if (!is_string($str))
            return false; //是否是字符串类型
        if (empty($str))
            return false; //是否已设定
        if ($str == '')
            return false; //是否为空
        return true;
    }

    //根据数据结果，弹出消息框
    public static function alert($result, $url = "") {
        $location = $url ? $url : $_SERVER ["HTTP_REFERER"];
        if ($result) {
            echo "<script>alert('操作成功！');window.location = '$location';</script>";
        } else {
            echo "<script>alert('操作失败，请检查提交数据！');window.location = '$location';</script>";
        }
        exit();
    }

    //弹出自定义消息框
    public static function alertinfo($msg, $url = "") {
        $location = $url ? $url : $_SERVER ["HTTP_REFERER"];
        echo "<script>alert('$msg');window.location.href = '$location';</script>";
        exit();
    }

    //获取远程数据
    public static function getresult($url, $param) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    //返回各时间格式
    public static function getdate() {
        $time = time();
        $arr = array();
        $arr ['nowdtime'] = date("Y-m-d H:i:s", $time);
        $arr ['nowday'] = date("Y-m-d", $time);
        $arr ['prevstime'] = date("Y-m-d H:00:00", $time - 3600);
        $arr ['prevetime'] = date("Y-m-d H:59:59", $time - 3600);
        $arr ['prevday'] = date("Y-m-d", $time - 3600 * 24);
        $arr ['prevsdate'] = date("Y-m-d 00:00:00", $time - 3600 * 24);
        $arr ['prevedate'] = date("Y-m-d 23:59:59", $time - 3600 * 24);
        $arr ['firstmdate'] = date('Y-m-d', mktime(0, 0, 0, date('n'), 1, date('Y')));
        $arr ['lastmdate'] = date('Y-m-d', mktime(0, 0, 0, date('n'), date('t'), date('Y')));
        return $arr;
    }

    public static function fileup($file) {
        if ($file['type'] == "text/plain" || $file['type'] == "application/vnd.ms-excel") {
            if ($file['size'] <= 2097152) {
                $arr = array();
                $f = fopen($file['tmp_name'], "r");
                while (!feof($f)) {
                    $s = fgetcsv($f);
                    for ($i = 0; $i < count($s); $i++) {
                        $arr[] = trim($s[$i]);
                    }
                }
                fclose($f);
                return $arr;
            }
        }
        return false;
    }

}