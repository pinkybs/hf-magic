<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * 多服务器不同的配置
 */

//游戏倍率
$config['speed'] = array(
	'base' => 10,
	'door_time' => 1,
	'study_time' => 3,
	'stone_time' => 10,
);

//mac test

$config['site'] = 'XiaoNei';

$config['api_key'] = '2710a960f3354779afe5a33e57836093';
$config['secret_key'] = 'bfa7a7ef6d4247458e45acd0b702b738';
$config['app_name'] = 'testmagic';

//分表数量
$config['cut_table_num'] = 5;
//分库数量
$config['cut_database_num'] = 0;
//memcache分布式
$config['cut_memcached_num'] = 2;
//根据平台id的分库
$config['cut_database_num_by_platform_id'] = 0;
//db同步时间
$config['sync_time'] = 1000;
//新手学习时间
$config['newbie_study_time'] = 5;

$config['crystal'] = array(
	'1' => 'coin',
);