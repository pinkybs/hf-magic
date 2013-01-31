<?php

defined('SYSPATH') OR die('No direct access allowed.');

class user_Core {

    //获取登陆用户名和密码
    public static function users($type) {
        $users = array();
        if ($type == "0") {
            $users = Kohana::config('user.user_statis');
        } elseif ($type == "1") {
            $users = Kohana::config('user.user_operat');
        }
        return $users;
    }

    //判断用户是否有权限登陆
    public static function isauth($uname, $upass, $utype) {
        $arr = self::users($utype);
        for ($i = 0; $i < count($arr); $i++) {
            if ($uname == $arr[$i]['name'] && $upass == $arr[$i]['pass']) {
                return $arr[$i];
            }
        }
        return false;
    }

    public static function checkuser($uinfo, $page) {
        $permiss = array();
        if ($uinfo['type'] == "0") {
            $permiss = Kohana::config('user.user_statis_permiss');
        } else {
            $permiss = Kohana::config('user.user_operat_permiss');
        }
        if (!in_array($page, array_merge($permiss[0], $permiss[$uinfo['level']]))) {
            echo "您没有权限访问当前页面！";
            exit();
        }
    }

}