<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * 多服务器不同的配置
 */

//游戏倍率
$config['speed'] = array(
	'base' => 1,
	'door_time' => 1,
	'study_time' => 1,
	'stone_time' => 1,
);

$config['site'] = 'XiaoNei';
$config['api_key'] = 'f58f38e2ce9245ffa0e593fb564fb082';
$config['secret_key'] = '332a88fd32674919bb95655e1d80e553';
$config['app_name'] = 'happymagic';

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