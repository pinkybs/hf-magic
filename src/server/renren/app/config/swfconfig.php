<?php

$staticUrl = Zend_Registry::get('static');

// swf list
// 'swf/v00/some.swf'
// url rewrite ==> 'swf/some.swf'
// v00 is version
$swfList = array(
    $staticUrl . '/swf/v14/swc.swf',
    $staticUrl . '/swf/v19/swc2.swf',
    $staticUrl . '/swf/v03/help.swf',
    $staticUrl . '/swf/v01/levelUp.swf',
    $staticUrl . '/swf/v09/building1.swf',
    $staticUrl . '/swf/v09/building2.swf',
    $staticUrl . '/swf/v08/building3.swf',
    $staticUrl . '/swf/v09/building4.swf',
    $staticUrl . '/swf/v10/building5.swf',
    $staticUrl . '/swf/v01/building6.swf',
    $staticUrl . '/swf/v01/building7.swf',
    $staticUrl . '/swf/v02/island1.swf',
    $staticUrl . '/swf/v01/sky1.swf',
    $staticUrl . '/swf/v01/sea1.swf',
    $staticUrl . '/swf/v01/dock1.swf',
    $staticUrl . '/swf/v01/boat1.swf',
    $staticUrl . '/swf/v02/itemcard1.swf',
    $staticUrl . '/swf/v04/player1.swf',
    $staticUrl . '/swf/v02/sound1.swf'
);

// interface list
$interfaces = array(
	'init'              => 'api/initgame',
    'loadDeco'          => 'api/getdecoration',
    'saveDeco'          => 'api/savedecoration',

    'getfifagift'       => 'api/getfifagift'
);

$swfResult = array(
	'staticHost' => Zend_Registry::get('static') . '/',
    'interfaceHost' => Zend_Registry::get('host') . '/',
	'initSwf'	=> $swfList,
	'interfaces' => $interfaces,
	'mainClass'	=> '',
	'otherSwf' => array(),
	'modules'   => array(
						array('name' => 'itembox', 'className' => '', 'algin' => 'top_left', 'x' => 0, 'y' => 0)
						),
	'bgMusic'	=> '',
	'gameData'	=> array()
);
