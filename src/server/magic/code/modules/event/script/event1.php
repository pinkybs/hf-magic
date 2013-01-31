<?php
/*
 * Created on 2009-3-12
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * 
 * 这是一个npc事件config,是顺序结构的
 */
$config = array(
	'event1' => array (
		'start_conditions' => array (
			//
		),
		'dialogue' => 'event1',
		'npc' => array(),
		'npc2' => array('1111'),
		'type' => '',
		'end_conditions' => array(),
	),
	'event2' => array (
		'conditions' => array (
			//
			'pre_event' => 'event1',
		),
		'dialogue' => 'event1',
		'npc' => array(),
		'npc2' => array('1111'),
		'type' => '',
	),
	'event3' => array (
		'conditions' => array (
			// 
			'pre_event' => 'event1',
		),
		'dialogue' => 'event1',
		'npc' => array(),
		'npc2' => array('1111'),
		'type' => '',
		'notice' => 'event4',
	),
);
?>
