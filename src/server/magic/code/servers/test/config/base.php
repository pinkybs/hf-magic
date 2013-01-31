<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * 多服务器不同的配置
 */

//游戏倍率
$config['speed'] = array(
	'base' => 1,
	'door_time' => 10,
	'study_time' => 5,
	'stone_time' => 10,
);

$config['site'] = 'XiaoNei';
$config['api_key'] = '2eee456824fd441f8ce2424689572c37';
$config['secret_key'] = 'fc7ffde95fc34f6a914ff70656308b5a';
$config['app_name'] = 'devmagic';

//分表数量
$config['cut_table_num'] = 5;
//分库数量
$config['cut_database_num'] = 0;
//memcache分布式
$config['cut_memcached_num'] = 2;
//根据平台id的分库
$config['cut_database_num_by_platform_id'] = 0;
//db同步时间
$config['sync_time'] = 60*15;
//新手学习时间
$config['newbie_study_time'] = 5;

$config['crystal'] = array(
	'1' => 'red',
	'2' => 'blue',
	'3' => 'green',
);