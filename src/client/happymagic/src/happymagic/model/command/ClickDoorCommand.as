package happymagic.model.command 
{
	import com.brokenfunction.json.decodeJson;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.geom.Point;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.manager.module.ModuleManager;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.events.DataManagerEvent;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.MagicUrlLoader;
	import happymagic.model.vo.DecorClassVo;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.ResultVo;
	import happymagic.model.vo.StudentVo;
	import happymagic.scene.world.grid.item.Door;
	/**
	 * ...
	 * @author Beck
	 */
	public class ClickDoorCommand extends BaseDataCommand
	{
		public var door:Door;
		public function ClickDoorCommand() 
		{
			
		}
		
		public function load($door:Door):void {
			door = $door;
			piaoMsg = true;
			piaoPoint = DisplayManager.getCurrentMousePosition();
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl('transfer'), { decor_id:door.data.id } );
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			if (data.result) 
			{
				if (!(data.result as ResultVo).isSuccess) 
				{
					EventManager.getInstance().dispatchEvent(new DataManagerEvent(DataManagerEvent.USERINFO_CHANGE));
				}
			}
			
			if (data.students) 
			{
				//减去door中的人数
				DecorVo(door.data).door_left_students_num = DecorVo(door.data).door_guest_limit -  data.students.length;
				
				//开始倒计时
				if (door.data.door_left_students_num <= 0) {
					//TODO 这里应该设置为decor的基本时间
					//重置时间
					door.data.door_left_time = door.data.door_refresh_time;
					
					door.countDown();
				}
				
				//暂时不用,直接表现在门上了
					//在用户头顶上表现经验值增加
					//var msgs:Array = [[PiaoMsgType.TYPE_EXP, data.result.exp]];
					//
					//var point:Point = DisplayManager.getPlayerPosition();
					//var event_msg:PiaoMsgEvent = new PiaoMsgEvent(PiaoMsgEvent.SHOW_PIAO_MSG, msgs, 
								//point.x, point.y);
					//EventManager.getInstance().dispatchEvent(event_msg);
					//
					//DataManager.getInstance().currentUser.exp += data.result.exp;
					//var event:DataManagerEvent = new DataManagerEvent(DataManagerEvent.USERINFO_CHANGE);
					//EventManager.getInstance().dispatchEvent(event);
			}
			
			commandComplete();
		}
		
		//private function load_complete(e:Event):void 
		//{
			//e.target.removeEventListener(Event.COMPLETE, load_complete);
			//
			//var data:Object = decodeJson(e.target.data);
			//
			//if (data.result.status != 1) {
				//漂字
				//var msgs_new:Array = [[PiaoMsgType.TYPE_BAD_STRING, data.result.content]];
				//var event_piao_msg:PiaoMsgEvent = new PiaoMsgEvent(PiaoMsgEvent.SHOW_PIAO_MSG, msgs_new, 200, 200);
				//EventManager.getInstance().dispatchEvent(event_piao_msg);
				//
				//return;
			//}
			//
			//DataManager.getInstance().openDoorStudents = new Array();
			//for ( var i:int = 0; i < data.students.length; i++ ) {
				//var student_vo:StudentVo =  new StudentVo();
				//student_vo.setValue(data.students[i]);
				//if (data.students[i].state == 0) {
					//DataManager.getInstance().fiddleStudents.push(student_vo);
				//} else {
					//DataManager.getInstance().onDeskStudents.push(student_vo);
				//}
				//DataManager.getInstance().openDoorStudents.push(student_vo);
			//}
			//
			//减去door中的人数
			//DecorVo(door.data).door_left_students_num = DecorVo(door.data).door_guest_limit -  data.length;
//
			//开始倒计时
			//if (door.data.door_left_students_num <= 0) {
				//TODO 这里应该设置为decor的基本时间
				//door.data.door_left_time = door.data.door_refresh_time;
				//
				//door.countDown();
			//}
			//
			//var msgs:Array = [[PiaoMsgType.TYPE_EXP, data.result.exp]];
			//
			//var point:Point = DataManager.getInstance().worldState.world.player.view.container.parent.localToGlobal(new Point(DataManager.getInstance().worldState.world.player.view.container.screenX, DataManager.getInstance().worldState.world.player.view.container.screenY));
			//var event_msg:PiaoMsgEvent = new PiaoMsgEvent(PiaoMsgEvent.SHOW_PIAO_MSG, msgs, 
						//point.x, point.y);
			//EventManager.getInstance().dispatchEvent(event_msg);
			//
			//DataManager.getInstance().currentUser.exp += data.result.exp;
			//var event:DataManagerEvent = new DataManagerEvent(DataManagerEvent.USERINFO_CHANGE);
			//EventManager.getInstance().dispatchEvent(event);
			//
			//dispatchEvent(new Event(Event.COMPLETE));
		//}
	}

}