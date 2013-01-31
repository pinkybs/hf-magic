<?php
/*
 * Created on 2009-3-17
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * 伪脚本
 * 测试
 */
switch(true) {
	case getEventStatus('event2') == 0 :
		//如果等级小于5 或者 事件1未发生 或者 智属性小于30,则返回错误,事件不能发生
//		if (getPlayerLevel() < 5 || !eventHappen('event1') || getPlayerProperty('zhi') < 30) {
//			return false;
//		}
		//将对话放入显示状态
		addMsgShow('npc1','你好啊');
		addMsgShow('player','你好啊');
		addMsgShow('npc1','你好啊');
		addMsgShow('player','你好啊');
		addMsgShow('npc1','你好啊');
		addMsgShow('player','你好啊');
		addChooseMsg('npc1', 'yes:好啊', 'no:这样不好吧');
		//改变事件状态
		changeEventStatus(1);
	case getEventStatus('event2') == 1 :
		//如果玩家选择yes,
		if (getPlayerChoose() == 'yes') {
			//触发事件2
			notice('event2');
			
			//奖励铅笔一只
			reward('pencil', 1);
			
			//增加德属性1
			addProperty('de', 1);
		} else {
		//玩家选择no
			//降低体属性2
			addProperty('ti', -1);
			//触发事件3
			notice('event3');
		}
}
?>
