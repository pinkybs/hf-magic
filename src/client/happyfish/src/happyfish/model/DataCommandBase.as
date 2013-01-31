package happyfish.model 
{
	import com.brokenfunction.json.decodeJson;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.net.URLRequest;
	import flash.net.URLVariables;
	import flash.system.ApplicationDomain;
	import happyfish.model.autoTake.AutoTakeCommandBase;
	
	/**
	 * 所有数据请求类基类
	 * @author slamjj
	 */
	public class DataCommandBase extends EventDispatcher  
	{
		public var callBack:Function;
		public var data:Object;
		public var loader:UrlConnecter;
		public var request:URLRequest;
		public var objdata:Object;
		
		public function DataCommandBase(_callBack:Function=null) 
		{
			callBack = _callBack;
		}
		
		protected function createLoad():void {
			//DisplayManager.uiSprite.showLoading();
			
			loader = new UrlConnecter();
			loader.retry = true;
			loader.addEventListener(Event.COMPLETE, load_complete);
		}
		
		protected function createRequest(url:String,data:Object=null,method:String="POST"):void {
			request = new URLRequest(url);
			request.method = method;
			var vars:URLVariables = new URLVariables();
			if (data) 
			{
				for (var name:String in data) 
				{
					vars[name] = data[name];
				}
			}
			request.data = vars;
		}
		
		protected function load_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, load_complete);
			data = new Object();
			// decodeJson 反编译JS
			var takeClass:Class;
			var takeCommand:AutoTakeCommandBase;
			objdata = decodeJson(e.target.data);
			for (var name:String in objdata) 
			{
				var i:int;
				var j:int;
				try{
					takeClass = ApplicationDomain.currentDomain.getDefinition("Take" + name + "Command") as Class;
					takeCommand = new takeClass() as AutoTakeCommandBase;
					takeCommand.take(data, objdata[name]);
				}catch (error:Error) {
					continue;
				}
			}
			
			//commandComplete();
		}
		
		public function commandComplete():void
		{
			
			if (callBack!=null) 
			{
				callBack.call();
			}
			dispatchEvent(new Event(Event.COMPLETE));
		}
		
	}

}