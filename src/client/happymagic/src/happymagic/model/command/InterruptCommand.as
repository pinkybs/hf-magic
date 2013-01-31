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
	import happymagic.manager.DataManager;
	import happymagic.model.MagicUrlLoader;
	import happymagic.model.vo.DecorClassVo;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.StudentVo;
	import happymagic.scene.world.grid.item.Door;
	import happymagic.scene.world.grid.item.Desk;
	import happymagic.scene.world.grid.person.Student;
	
	/**
	 * ...
	 * @author Beck
	 */
	public class InterruptCommand extends BaseDataCommand
	{
		private var decorIds:Array = [];
		public function InterruptCommand() 
		{
			
		}
		
		public function load($decor_ids:Array):void 
		{
			this.decorIds = $decor_ids;
			
			createLoad();
			
			createRequest(InterfaceURLManager.getInstance().getUrl('interrupt'), { decor_ids:JSON.encode($decor_ids), uid:DataManager.getInstance().curSceneUser.uid } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			var i:int;
			var results:Array = data.results;
			//按照顺序删除桌子上的水晶,只处理第一个
			for (i = 0; i < results.length; i++) {
				DataManager.getInstance().interruptResults[decorIds[i]] = results[i];
			}
			
			//学生数据改变事件
			if (data.changeStudents) 
			{
				var tmpstudent:StudentVo;
				var tmpstudentview:Student;
				for (i = 0; i < data.changeStudents.length; i++) 
				{
					tmpstudent = data.changeStudents[i];
					tmpstudentview = DataManager.getInstance().worldState.world.getStudentBySid(tmpstudent.sid);
					if (tmpstudentview) 
					{
						tmpstudentview.data = tmpstudent;
					}
				}
			}
			
			commandComplete();
		}
		
	}

}