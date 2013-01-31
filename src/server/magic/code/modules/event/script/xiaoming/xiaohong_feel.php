<?php
/*
 * Created on 2009-3-15
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
$config = array(
	'start_conditions' => array (
		//
		'level' => 2,
	),
	'dialogue' => array(
		'xiaoming' => '你觉得小红如何啊',
		'me' => array (
			'good' => '很好啊',
			'bad'  => '我觉得有些问题的',
			'not_known' => '说不清楚啊',
		)
	),
	'notice' => array(
			'npc' => 'xiaohong',
			'event' => ''
	),
	'type' => 'carry_letters',
	'end_conditions' => array(),
	'reward' => array (
		'good' => array (
					'property' => array('d' => 1),
					'goods' => array('pencil' => 1),
		),
		'bad' => array('l' => -1),
	),
)
?>
