package happymagic.model.command 
{
	import com.adobe.serialization.json.JSON;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.events.PickCoinEvent;
	import happymagic.manager.DataManager;
	import happymagic.model.MagicUrlLoader;
	import happymagic.model.vo.DecorClassVo;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.StudentVo;
	import happymagic.scene.world.grid.item.Door;
	import happymagic.scene.world.grid.item.Desk;
	import happymagic.scene.world.grid.person.Student;
	import happymagic.scene.world.MagicWorld;
	
	/**
	 * ...
	 * @author Beck
	 */
	public class PickupCommand extends BaseDataCommand
	{
		private var decorIds:Array = [];
		public function PickupCommand() 
		{
			takeResult = false;
		}
		
		public function load($decor_ids:Array):void 
		{
			
			decorIds = $decor_ids;
			createLoad();
			
			createRequest(InterfaceURLManager.getInstance().getUrl('pickup'), { decor_ids:JSON.encode($decor_ids), uid:DataManager.getInstance().curSceneUser.uid } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			if (data.results) 
			{
				var results:Array = data.results;
				var students:Array = data.changeStudents;
				//按照顺序删除桌子上的水晶,只处理第一个
				for (var i:int = 0; i < results.length; i++) {
					DataManager.getInstance().pickUpResults[decorIds[i]] = results[i];
					//更新的学生数据
					if (students[i]) 
					{	
						DataManager.getInstance().pickUpStudentResults[decorIds[i]] = students[i];
					}else {
						DataManager.getInstance().pickUpStudentResults[decorIds[i]] = null;
					}
					
					//掉落物品
					if (data.addItem) 
					{
						DataManager.getInstance().pickUpItems[decorIds[i]] = data.addItem;
					}
				}
				
				if (data.changeStudents) 
				{
					var world:MagicWorld = DataManager.getInstance().worldState.world as MagicWorld;
					var tmpstudent:Student;
					for (var j:int = 0; j < data.changeStudents.length; j++) 
					{
						DataManager.getInstance().setStudentVo(data.changeStudents[j]);
						tmpstudent = world.getStudentBySid(data.changeStudents[j].sid);
						tmpstudent.countDown();
					}
				}
			}
			
			
			
			commandComplete();
			
			var event:PickCoinEvent = new PickCoinEvent(PickCoinEvent.PICK_COMPLETE);
			EventManager.getInstance().dispatchEvent(event);
		}
	}

}