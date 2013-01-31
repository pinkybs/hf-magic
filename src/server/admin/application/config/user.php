<?php

defined('SYSPATH') OR die('No direct access allowed.');
//运营查询用户组
$config['user_statis'] = array
    (
    array('name' => 'admin', 'pass' => 'admin', 'level' => 2),
    array('name' => 'admin1', 'pass' => 'admin1', 'level' => 1),
);
//运营工具用户组
$config['user_operat'] = array
    (
    array('name' => 'admin2', 'pass' => 'admin2', 'level' => 1),
    array('name' => 'admin3', 'pass' => 'admin3', 'level' => 1),
);


//////////////////
//用户权限分配规则，0为公共页面，1为超级管理，2为普通用户，3.4.5.。。依此类推。
//////////////////
//运营查询用户权限
$config['user_statis_permiss'] = array
    (
    "0" => array("getparam", "index", "head", "left", "changeplant", "body", "bodyajax"),
    "1" => array("hourinstall", "noviceguide", "consumstatis", "houractive", "activegrowth", "activelevel"),
    "2" => array("activelevel"),
);
//运营工具用户权限
$config['user_operat_permiss'] = array
    (
    "0" => array("getparam", "index", "main", "head", "left", "changeplant", "body"),
    "1" => array("notemanage", "itemrelease", "itemsingle", "buildrelease", "buildingle"),
    "2" => array("activelevel"),
);