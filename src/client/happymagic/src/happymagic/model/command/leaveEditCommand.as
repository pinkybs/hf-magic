package happymagic.model.command 
{
	import com.adobe.serialization.json.JSON;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import flash.utils.setTimeout;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.utils.SysTracer;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.MagicUrlLoader;
	import happymagic.model.vo.DecorClassVo;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.ResultVo;
	import happymagic.model.vo.StudentVo;
	import happymagic.scene.world.grid.item.Desk;
	
	/**
	 * ...
	 * @author Beck
	 */
	public class leaveEditCommand extends BaseDataCommand
	{
		
		public function leaveEditCommand() 
		{
			
		}
		
		public function load():void {
			
			//判断是否没有修改,如果没有,就直接跳出DIY
			var hasData:Boolean = false;
			
			var name:String;
			for (name in DataManager.getInstance().decorChangeList) 
			{
				hasData = true;
				break;
			}
			for (name in DataManager.getInstance().decorChangeBagList) 
			{
				hasData = true;
				break;
			}
			for (name in DataManager.getInstance().floorChangeList) 
			{
				hasData = true;
				break;
			}
			for (name in DataManager.getInstance().wallChangeList) 
			{
				hasData = true;
				break;
			}
			
			if (!hasData) 
			{
				saveEmpty();
				return;
			}
			
			DisplayManager.uiSprite.showLoading();
			
			createLoad();
			
			var tmpobj:Object = new Object();
			//diy传递数据	
			//解析一下装饰物的数据KEYNAME,去除"&"和TYPE值,只留下id
			tmpobj.decorChangeList = JSON.encode(filterChangeData(DataManager.getInstance().decorChangeList));
			tmpobj.decorBagChangeList = JSON.encode(filterChangeData(DataManager.getInstance().decorChangeBagList));
			tmpobj.floorChangeList = JSON.encode(DataManager.getInstance().floorChangeList);
			//tmpobj.floorBagChangeList = JSON.encode(DataManager.getInstance().floorChangeBagList);
			tmpobj.wallChangeList = JSON.encode(DataManager.getInstance().wallChangeList);
			//tmpobj.wallBagChangeList = JSON.encode(DataManager.getInstance().wallChangeBagList);
			
			createRequest(InterfaceURLManager.getInstance().getUrl('leaveedit'), tmpobj);
			
			loader.load(request);
			
		}
		/**
		 * 去掉修改数据里keyname里的type值
		 * @param	value
		 * @return
		 */
		private function filterChangeData(value:Object):Object {
			var newobj:Object = new Object();
			var tmpstr:String;
			for (var name:String in value) 
			{
				tmpstr = name.split("&")[0];
				newobj[tmpstr] = value[name];
			}
			
			return newobj;
		}
		
		private function saveEmpty():void {
			data = new Object();
			var tmpresult:ResultVo = new ResultVo();
			tmpresult.status = ResultVo.SUCCESS;
			//派发完成事件SceneEvent.DIY_FINISHED
			EventManager.getInstance().dispatchEvent(new SceneEvent(SceneEvent.DIY_FINISHED));
			commandComplete();
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			if (data.result.status==ResultVo.SUCCESS) 
			{
				DataManager.getInstance().decorChangeList = new Object();
				DataManager.getInstance().decorChangeBagList = new Object();
				DataManager.getInstance().floorChangeList = new Object();
				DataManager.getInstance().floorChangeBagList = new Object();
				DataManager.getInstance().wallChangeList = new Object();
				DataManager.getInstance().wallChangeBagList = new Object();
				
				//学生状态改变事件
				if (data.changeStudents) 
				{
					takeChangeStudents(data.changeStudents);
				}
				
				//派发完成事件SceneEvent.DIY_FINISHED
				EventManager.getInstance().dispatchEvent(new SceneEvent(SceneEvent.DIY_FINISHED));
			}
			
			DisplayManager.uiSprite.closeLoading();
			commandComplete();
		}
		
		private function takeChangeStudents(value:Array):void {
			var tmpstudent:StudentVo;
					var tmpdesk:Desk;
					for (var i:int = 0; i < value.length; i++) 
					{
						tmpstudent = value[i];
						tmpdesk = DataManager.getInstance().worldState.world.getDecorByIdType(tmpstudent.decor_id,Desk) as Desk;
						if (tmpdesk) 
						{
							tmpdesk.fiddleGoToDesk(tmpstudent);
						}else {
							SysTracer.systrace("changeStudent no desk ",tmpstudent.decor_id);
						}
					}
		}
	}

}