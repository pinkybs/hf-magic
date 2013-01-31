<?php
/*
 * Created on 2009-3-19
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function getEventStatus($event)
{
	echo $event;
	return 0;
}

function getPlayerLevel()
{
	$npc = new Npc('xiaoming');
	return 10;
}

$script = '';
/**
 * 用于增加消息的脚本
 */
function addMsgShow($name, $content)
{
	global $script;
	$script .= "Game.addEvent(\"Game.showMsg('$name', '$content')\");";
}

function send()
{
	global $script;
	echo $script;
}
?>
