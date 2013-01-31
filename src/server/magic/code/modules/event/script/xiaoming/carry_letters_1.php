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
		'xiaoming' => '帮我送封信吧~',
		'me' => array (
			'yes' => '好啊,帮你送吧',
			'no'  => '啊啊啊啊,今天不想去啊',//降低好感度.
		)
	),
	'notice' => array(
			'npc' => 'xiaohong',
			'event' => 'carry_letters_1_end'
	),
	'type' => 'carry_letters',
	'end_conditions' => array(),
)
?>
