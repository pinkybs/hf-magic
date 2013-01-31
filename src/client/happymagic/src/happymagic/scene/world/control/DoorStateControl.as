package happymagic.scene.world.control 
{
	import com.friendsofed.isometric.Point3D;
	import flash.events.Event;
	import flash.utils.clearTimeout;
	import flash.utils.Dictionary;
	import flash.utils.setTimeout;
	import happyfish.scene.astar.Node;
	import happyfish.scene.astar.NodesUtil;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.grid.Person;
	import happyfish.scene.world.WorldState;
	import happymagic.scene.world.bigScene.MassesView;
	import happymagic.scene.world.grid.item.Door;
	import happymagic.scene.world.grid.person.Player;
	/**
	 * ...
	 * @author jj
	 */
	public class DoorStateControl
	{
		private var _state:WorldState;
		private var doorNodes:Object;
		private var checkDooring:Boolean;
		private var disableList:Object = new Dictionary();
		private var doorArr:Array;
		
		private var openTimes:Array;
		
		public function DoorStateControl(__state:WorldState) 
		{
			_state = __state;
			doorNodes = new Array();
		}
		
		public function removePerson(person:Person):void {
			if (!openTimes) 
			{
				return;
			}
			var tmpperson:Person;
			var timeid:uint;
			for (var i:int = 0; i < openTimes.length; i++) 
			{
				tmpperson = openTimes[i].person;
				timeid=openTimes[i].timeid;
				if (tmpperson==person) 
				{
					clearTimeout(timeid);
					openTimes.splice(i, 1);
					removePerson(tmpperson);
					return;
				}
			}
		}
		
		public function addPerson(person:Person):void {
			if (!openTimes) openTimes = new Array();
			if (!doorArr) 
			{
				return;
			}
			var tmpindex:int;
			var tmpid:uint;
			var speed:Number;
			for (var i:int = 0; i < doorArr.length; i++) 
			{
				tmpindex = NodesUtil.checkPathThrowNode(person.path, (doorArr[i] as Door).getNode());
				if (tmpindex>=0) 
				{
					speed = 250;
					if (person is MassesView) 
					{
						speed = 1800;
					}
					tmpid = setTimeout(openDoor, speed * (tmpindex), doorArr[i] as Door);
					openTimes.push( { timeid:tmpid, person:person } );
				}
			}
		}
		
		private function openDoor(door:Door):void
		{
			door.openDoor();
		}
		
		/**
		 * 整理获取当前场景内所有的门
		 */
		public function getAllDoorNodes():void {
			doorArr = new Array();
			var i:int;
			var tmplist:Array = _state.world.items;
			for ( i= 0; i < tmplist.length; i++) 
			{
				if (tmplist[i] is Door) 
				{
					doorArr.push( tmplist[i] );
				}
			}
		}
		
		public function closeDoor(door:Door):void {
			door.closeDoor();
			delete disableList[door];
		}
		
	}

}