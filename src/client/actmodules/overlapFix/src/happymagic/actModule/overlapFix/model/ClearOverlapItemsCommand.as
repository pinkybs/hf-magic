package happymagic.actModule.overlapFix.model 
{
	import com.brokenfunction.json.decodeJson;
	import com.brokenfunction.json.encodeJson;
	import flash.events.Event;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.vo.ResultVo;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class ClearOverlapItemsCommand extends BaseDataCommand 
	{
		public var needRefresh:Boolean;
		public function ClearOverlapItemsCommand() 
		{
			
		}
		
		public function load(items:Object):void {
			
			//判断是否没有修改,如果没有,就直接跳出DIY
			var hasData:Boolean = false;
			
			var name:String;
			for (name in items) 
			{
				hasData = true;
				break;
			}
			
			if (!hasData) 
			{
				saveEmpty();
				return;
			}
			
			needRefresh = true;
			
			DisplayManager.uiSprite.showLoading();
			
			createLoad();
			loader.retry = true;
			
			var tmpobj:Object = new Object();
			
			tmpobj.decorBagChangeList = encodeJson(filterChangeData(items));
			
			createRequest(InterfaceURLManager.getInstance().getUrl('leaveedit'), tmpobj);
			
			loader.load(request);
			
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			DisplayManager.uiSprite.closeLoading();
			commandComplete();
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
			
			commandComplete();
		}
	}

}