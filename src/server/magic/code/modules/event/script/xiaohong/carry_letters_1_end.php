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
		'pre_event' => 'carry_letters_1',
	),
	'dialogue' => array(
		'xiaohong' => '信拿来了啊',
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
