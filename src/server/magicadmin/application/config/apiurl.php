<?php

defined('SYSPATH') OR die('No direct access allowed.');

$config['url0'] = array(
    "hourinstall" => "zstatistics/getRoleCreat", //小时安装量地址
    "noviceguide" => "zstatistics/getRoleNewBie", //新手引导流失统计
    "consumstatis" => "zstatistics/getConsumstatis", //记录用户消费
    "houractive" => "zstatistics/getRoleActive", //小时活跃数
    "activegrowth" => "zstatistics/getActiveGrowth", //日活跃、日增长用户
    "activelevel" => "zstatistics/getActiveLevel", //活跃用户等级分布
    "installtotal" => "zstatistics/getRoleTotalInstall", //查询全部活跃用户
);
$config['url1'] = array(
    "rwritinnote" => "zoperating/c_NoteJson", //公告管理
    "itemrelease" => "zoperating/getItemRelease", //物品获取
    "ritemrelease" => "zoperating/c_ItemRelease", //物品发放
    "buildrelease" => "zoperating/getBuildRelease", //装饰物获取
    "rbuildrelease" => "zoperating/c_BuildRelease", //装饰物发放
);