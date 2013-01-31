package happyfish.scene.personAction.control 
{
	import com.adobe.utils.ArrayUtil;
	import com.friendsofed.isometric.Point3D;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.utils.Dictionary;
	import flash.utils.setTimeout;
	import happyfish.events.TriggerEvent;
	import happyfish.scene.astar.Node;
	import happyfish.scene.astar.NodesUtil;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.personAction.PersonActionType;
	import happyfish.scene.personAction.PersonActionVo;
	import happyfish.scene.world.grid.Person;
	import happyfish.scene.world.WorldState;
	import happyfish.utils.CustomTools;
	import happymagic.manager.DataManager;
	import happymagic.scene.world.control.AvatarCommand;
	
	/**
	 * ...
	 * @author jj
	 */
	public class PersonActionControl 
	{
		
		public function PersonActionControl(access:Private) 
		{
			
			if (access != null)
			{	
				if (instance == null)
				{				
					instance = this;
				}
			}
			else
			{	
				throw new Error( "PersonActionControl"+"单例" );
			}
		}
		
		public static function getInstance():PersonActionControl
		{
			if (instance == null)
			{
				instance = new PersonActionControl( new Private() );
			}
			return instance;
		}
		
		
		private static var instance:PersonActionControl;
		public var state:WorldState;
		private var _actions:Array;
		private var _persons:Array = new Array();
		private var _freePersons:Array = new Array();
		private var activeActions:Dictionary = new Dictionary();
		private var _requestMeetList:Array = new Array();
		
		
		
		public function addPerson(value:Person):void {
			_persons.push(value);
			if (value.free) _freePersons.push(value);
			customAction();
		}
		
		public function customAction():void {
			//如果有meeting 请求,就先响应请求
			var tmp:Object = getNeedRequest();
			if (tmp) 
			{
				if (tmp.num>0) 
				{
					takeActions(tmp.actions);
					return;
				}
			}
			if (actions.length>0) 
			{
				takeActions(CustomTools.customFromArray(actions));
			}
			
		}
		
		
		
		public function takeActions(value:Array):void {
			var tmpPerson:Person = getFreePerson();
			if (!tmpPerson) 
			{
				return;
			}
			tmpPerson.free = false;
			ArrayUtil.removeValueFromArray(_freePersons, tmpPerson);
			activeActions[tmpPerson] = cloneActions(value);
			activeNextAction(tmpPerson);
		}
		
		public function clear():void
		{
			actions = new Array();
			_persons = new Array();
			_freePersons = new Array();
			activeActions = new Dictionary(); 
			_requestMeetList = new Array(); 
		}
		
		private function cloneActions(value:Array):Array
		{
			var tmp:Array = new Array();
			for (var i:int = 0; i < value.length; i++) 
			{
				tmp.push((value[i] as PersonActionVo).clone());
			}
			return tmp;
		}
		
		private function getFreePerson():Person {
			return CustomTools.customFromArray(_freePersons) as Person;
		}
		
		private function activeNextAction(person:Person):void {
			if (!activeActions[person]) 
			{
				return;
			}
			if (activeActions[person].length<=0) 
			{
				person.free = true;
				_freePersons.push(person);
				delete activeActions[person];
				//随机一次行为
				customAction();
				return;
			}
			
			var value:PersonActionVo = activeActions[person][0];
			(activeActions[person] as Array).shift();
			
			person.alpha = 1;
			
			var targetNode:Node;
			var tmprect:Rectangle
			switch (value.type) 
			{
				case PersonActionType.TO_NODE:
					if (!value.targetNode) {
						targetNode = state.getCustomOutRoomWalkAbleNode();
					}else {
						targetNode = value.targetNode;
					}
					person.addCommand(new AvatarCommand(nodeToPoint3D(targetNode),activeNextAction,nodeToPoint3D(value.towardsNode),0,"down",null,"",false,[person]));
				break;
				
				case PersonActionType.ROUND_RECT:
					tmprect = value.rect;
					person.addCommand(new AvatarCommand(nodeToPoint3D(new Node(tmprect.left,tmprect.top))));
					person.addCommand(new AvatarCommand(nodeToPoint3D(new Node(tmprect.right, tmprect.top))));
					person.addCommand(new AvatarCommand(nodeToPoint3D(new Node(tmprect.right,tmprect.bottom))));
					person.addCommand(new AvatarCommand(nodeToPoint3D(new Node(tmprect.left,tmprect.top)),activeNextAction,null,0,null,null,"",false,[person]));
				break;
				
				case PersonActionType.ROUND_ROOM:
					var roomsize:uint = DataManager.getInstance().curSceneUser.tile_x_length;
					tmprect = new Rectangle(23 - CustomTools.customInt(0, 4), 23 - CustomTools.customInt(0, 4),
											roomsize+CustomTools.customInt(2,5), roomsize+CustomTools.customInt(2,5));
					
					person.addCommand(new AvatarCommand(nodeToPoint3D(new Node(tmprect.left,tmprect.top))));
					person.addCommand(new AvatarCommand(nodeToPoint3D(new Node(tmprect.right, tmprect.top))));
					person.addCommand(new AvatarCommand(nodeToPoint3D(new Node(tmprect.right,tmprect.bottom))));
					person.addCommand(new AvatarCommand(nodeToPoint3D(new Node(tmprect.left,tmprect.top)),activeNextAction,null,0,"down",null,"",false,[person]));
				break;
				
				case PersonActionType.SHOW_MOOD:
					person.showMood(value.iconClass);
					setTimeout(showMoodComplete, value.showTime,person);
				break;
				
				case PersonActionType.OUT_SCENE:
					targetNode=DataManager.getInstance().getCustomSceneDoor();
					person.addCommand(new AvatarCommand(nodeToPoint3D(targetNode),activeNextAction,null,0,null,null,"",false,[person]));
				break;
				
				case PersonActionType.HIDE:
					person.alpha = 0;
					setTimeout(activeNextAction, value.showTime,person);
				break;
				
				case PersonActionType.REQUEST_MEET:
					var tmpnode:Node;
					//建立触发器
					if (!value.triggerName) 
					{
						var tmptriggerName:String = value.type + "_" + person.view.name;
						TriggerControl.getInstance().addTrigger(tmptriggerName, value.num);
						value.triggerName = tmptriggerName;
						
						//产生碰头点
						if (!value.targetNode) {
							targetNode = state.getCustomOutRoomWalkAbleNode();
						}else {
							targetNode = value.targetNode;
						}
						value.targetNode = targetNode;
						
						//复原完整行为队列
						activeActions[person].splice(0, 0, value);
						
						var tmpnodes:Array = getMeetNodes(value.targetNode);
						while (tmpnodes.length<=2) 
						{
							targetNode = state.getCustomOutRoomWalkAbleNode();
							value.targetNode = targetNode;
							tmpnodes=getMeetNodes(value.targetNode);
						}
						tmpnode = CustomTools.customFromArray(tmpnodes, null, true);
						
						//记录meeting请求
						_requestMeetList.push( {triggerName:tmptriggerName, num:value.num-1,nodes:tmpnodes,persons:[person], actions:cloneActions(activeActions[person]) } );
						
						//再重新删除这步行为
						activeActions[person].shift();
					}else {
						var tmpRequestMeetList:Object = getRequestByTrigger(value.triggerName);
						tmpRequestMeetList.persons.push(person);
						tmpRequestMeetList.num--;
						tmpnode = CustomTools.customFromArray(tmpRequestMeetList.nodes, null, true);
					}
					
					//侦听触发器完成
					TriggerControl.getInstance().addEventListener(TriggerEvent.TRIGGER_COMPLETE, triggerComplete);
					//走向碰头点,完成后标记触发器
					person.addCommand(new AvatarCommand(nodeToPoint3D(tmpnode), setTrigger, null, 0, null, null, "", false, [value.triggerName]));
				break;
			}
		}
		
		private function getMeetNodes(targetNode:Node):Array {
			
			var tmpnodes:Array = NodesUtil.getRectNodes(new Rectangle(targetNode.x, targetNode.y, 2, 2));
			tmpnodes = tmpnodes.filter(filterCanWalkNode);
			
			return tmpnodes;
		}
		
		private function filterCanWalkNode(element:*, index:int, arr:Array):Boolean
		{
			return state.grid.getNode(element.x, element.y).walkable;
		}
		
		private function nodeToPoint3D(node:Node):Point3D {
			if (node) return new Point3D(node.x, 0, node.y);
			return null;
			
		}
		
		private function showMoodComplete(person:Person):void
		{
			person.removeMood();
			activeNextAction(person);
		}
		
		/**
		 * 触发器完成,所有相关person执行下一步
		 * @param	e
		 */
		private function triggerComplete(e:TriggerEvent):void 
		{
			var persons:Array = getRequestByTrigger(e.triggerName).persons as Array;
			for (var i:int = 0; i < persons.length; i++) 
			{
				activeNextAction(persons[i] as Person);
			}
		}
		
		/**
		 * 标记触发器,如果触发器完成
		 * @param	person
		 * @param	triggerName
		 */
		private function setTrigger(triggerName:String):void {
			TriggerControl.getInstance().changeTrigger(triggerName, 1);
		}
		
		
		public function get actions():Array { return _actions; }
		
		public function set actions(value:Array):void 
		{
			_actions = value;
		}
		
		public function get persons():Array { return _persons; }
		
		public function set persons(value:Array):void 
		{
			_persons = value;
		}
		
		
		private function getRequestByTrigger(value:String):Object {
			for (var i:int = 0; i < _requestMeetList.length; i++) 
			{
				if (_requestMeetList[i].triggerName==value) 
				{
					return _requestMeetList[i] as Object;
				}
			}
			return null;
		}
		
		private function getNeedRequest():Object
		{
			for (var i:int = 0; i < _requestMeetList.length; i++) 
			{
				if (_requestMeetList[i].num>0) 
				{
					return _requestMeetList[i] as Object;
				}
			}
			return null;
		}
		
	}
	
}
class Private {}