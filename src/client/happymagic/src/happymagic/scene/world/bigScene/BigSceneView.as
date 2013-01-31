package happymagic.scene.world.bigScene 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.TimerEvent;
	import flash.geom.Point;
	import flash.utils.Timer;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.AlginType;
	import happyfish.scene.astar.Node;
	//import happyfish.scene.personAction.control.PersonActionControl;
	import happyfish.scene.world.grid.Person;
	import happyfish.scene.world.WorldState;
	import happyfish.utils.CustomTools;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.task.TaskInfoView;
	import happymagic.events.TaskEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.AvatarType;
	import happymagic.model.vo.AvatarVo;
	import happymagic.model.vo.EnemyVo;
	import happymagic.model.vo.NpcVo;
	import happymagic.model.vo.SceneVo;
	import happymagic.scene.world.bigScene.events.BigSceneEvent;
	
	/**
	 * ...
	 * @author jj
	 */
	public class BigSceneView extends EventDispatcher
	{
		
		private var sceneData:SceneVo;
		private var npcVos:Array;
		
		private var npcs:Array;
		private var enemyVos:Array;
		
		private var enemyList:Array;
		private var npcList:Array;
		private var enemyTimer:Timer;
		private var _worldState:WorldState;
		private var massesList:Array;
		
		public function BigSceneView($world_state:WorldState) 
		{
			_worldState = $world_state;
			
			enemyList = new Array();
			npcList = new Array();
			massesList = new Array();
			
			EventManager.getInstance().addEventListener(TaskEvent.TASKS_STATE_CHANGE, taskChange);
		}
		
		private function taskChange(e:TaskEvent):void 
		{
			initAllNpcTaskState();
		}
		
		/**
		 * 刷新所有NPC的任务状态表现
		 */
		public function initAllNpcTaskState():void {
			for (var i:int = 0; i < npcList.length; i++) 
			{
				npcList[i].initPaoIcon();
			}
		}
		
		public function setData(__sceneVo:SceneVo, __npcs:Array,__enemys:Array):void {
			sceneData = __sceneVo;
			npcVos = __npcs;
			enemyVos = __enemys;
			
			clear();
			
			//PersonActionControl.getInstance().state = _worldState;
			//PersonActionControl.getInstance().actions = DataManager.getInstance().getSceneClassById(sceneData.sceneId).actions;
			
			createNpc();
		}
		
		public function clear():void {
			
			var i:int;
			for (i = 0; i < npcList.length; i++) 
			{
				npcList[i].remove();
			}
			npcList = new Array();
			
			for (i = 0; i < enemyList.length; i++) 
			{
				enemyList[i].remove();
			}
			enemyList = new Array();
			
			for (i = 0; i < massesList.length; i++) 
			{
				massesList[i].remove();
			}
			massesList = new Array();
			
			//PersonActionControl.getInstance().clear();
		}
		
		public function hideAllNpc():void 
		{
			for (var i:int = 0; i < npcList.length; i++) 
			{
				var item:NpcView = npcList[i];
				item.visible = false;
			}
		}
		
		public function showAllNpc():void {
			for (var i:int = 0; i < npcList.length; i++) 
			{
				var item:NpcView = npcList[i];
				item.visible = true;
			}
		}
		
		private function createNpc():void
		{
			
			var tmpNpc:NpcView;
			for (var i:int = 0; i < npcVos.length; i++) 
			{
				tmpNpc = new NpcView(npcVos[i],_worldState);
				npcList.push(tmpNpc);
				_worldState.world.addItem(tmpNpc);
				
			}
			
			//createMasses();
			
			createEnemys();
		}
		
		/**
		 * 创建行人群众
		 */
		private function createMasses():void
		{
			var tmpobj:Object;
			var tmpid:uint;
			var tmp:MassesView;
			for (var i:int = 0; i < 10; i++) 
			{
				tmpobj = new Object();
				
				var tmpnode:Node = DataManager.getInstance().getCustomSceneDoor();
				
				tmpobj.x = tmpnode.x;
				tmpobj.z = tmpnode.y;
				
				tmpobj.class_name = DataManager.getInstance().getCustomStudentAvatarVo().className;
				
				tmp = new MassesView(tmpobj, _worldState);
				massesList.push(tmp);
				_worldState.world.addItem(tmp);
				//PersonActionControl.getInstance().addPerson(tmp as Person);
			}
			
		}
		
		private function createEnemys():void
		{
			//if (!sceneData.enemy_xy) 
			//{
				//return;
			//}
			
			var tmp:EnemyView;
			var tmpenemyvo:EnemyVo;
			var tmpPoint:Point;
			for (var i:int = 0; i < enemyVos.length; i++) 
			{
				tmpenemyvo = enemyVos[i] as EnemyVo;
				tmpPoint = getEnemyXY();
				tmpenemyvo.x = tmpPoint.x;
				tmpenemyvo.z = tmpPoint.y;
				tmp = new EnemyView(tmpenemyvo,_worldState);
				enemyList.push(tmp);
				_worldState.world.addItem(tmp);
			}
			
			startHpTimer();
			
			dispatchEvent(new Event(Event.COMPLETE));
		}
		
		private function getEnemyXY(del:Boolean = true):Point {
			
			//var tmp:uint = Math.floor(Math.random() * (sceneData.enemy_xy.length - 1));
			//var point:Point = new Point(sceneData.enemy_xy[tmp].x, sceneData.enemy_xy[tmp].y);
			
			var wNode:Node = _worldState.getCustomOutRoomWalkAbleNode();
			return new Point(wNode.x,wNode.y);
		}
		
		private function startHpTimer():void
		{
			if (!enemyTimer) {
				enemyTimer = new Timer(1000);
				enemyTimer.addEventListener(TimerEvent.TIMER, enemyCheckFun);
			}
			enemyTimer.start();
		}
		
		private function enemyCheckFun(e:TimerEvent):void 
		{
			for (var i:int = 0; i < enemyList.length; i++) 
			{
				(enemyList[i] as EnemyView).changeHp((enemyList[i] as EnemyView).enemyVo.heal);
			}
		}
		
	}

}