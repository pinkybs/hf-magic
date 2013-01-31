package happyfish.cacher 
{
	import br.com.stimuli.loading.BulkProgressEvent;
	import br.com.stimuli.loading.loadingtypes.LoadingItem;
	import flash.display.Loader;
	import flash.display.Stage;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.ProgressEvent;
	import flash.net.URLRequest;
	import flash.system.ApplicationDomain;
	import flash.system.LoaderContext;
	import flash.system.SecurityDomain;
	import happyfish.events.SwfClassCacheEvent;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.manager.SwfURLManager;
	import happyfish.model.SwfLoader;
	import happyfish.utils.SysTracer;
	
	//[Event(name = "complete", type = "flash.events.Event")]
	
	/**
	 * 素材类加载与管理类
	 * @author slamjj
	 */
	public class SwfClassCache extends EventDispatcher
	{
		public var stage:Stage;
		public var appDomain:ApplicationDomain;
		
		private static var instance:SwfClassCache;
		public var loadedPer:uint;
		private var loadedSwfList:Object;
		private var currentUrl:String;
		
		public var loadArr:Array = new Array();
		public var isLoading:Boolean;
		public var currentClass:String;
		public function SwfClassCache(access:Private) 
		{
			
			if (access != null)
			{	
				if (instance == null)
				{				
					instance = this;
					loadedSwfList = new Object();
					appDomain = ApplicationDomain.currentDomain;
				}
			}
			else
			{	
				throw new Error( "SwfClassCache"+"单例" );
			}
		}
		
		/**
		 * 通知开始获取指定class
		 * @param	className
		 */
		public function loadClass(className:String):void {
			
			//如果获取队列还有,就退出,并等待队伍排到
			if (loadArr.length>0) 
			{
				loadArr.push(className);
				return;
			}
			
			loadArr.push(className);
			currentClass = className;
			
			if (hasClass(className)) 
			{
				loadArr.splice(0, 1);
				dispatchComplete(currentClass);
			}else {
				//判断文件列表中有没有指定地址,如有就用,如无就自己组合地址
				var tmpurl:String = getClassSwfUrl(currentClass);
				
				if (loadedSwfList[tmpurl]==2) 
				{
					loadArr.splice(0, 1);
					dispatchComplete(currentClass);
				}else {
					//getClassURL会自动判断是否有版本号
					//如无就组合类名来获得文件名
					loadClassSwf(tmpurl);
				}
				
				//addEventListener(Event.COMPLETE,loadClassComplete);
			}
		}
		
		private function getClassSwfUrl(className:String):String {
			var tmpurl:String;
			tmpurl = SwfURLManager.getInstance().getClassURL(className);
			
			return tmpurl;
		}
		
		public function loadClassSwf(url:String):void {
			
			if (loadedSwfList[url]==1) 
			{
				return;
			}
			
			currentUrl = url;
			trace("getClass from", url,currentClass);
			loadedSwfList[url] = 1;
			loadedPer = 0;
			var loader:LoadingItem = SwfLoader.getInstance().add(url);
			loader.addEventListener(Event.COMPLETE, loadClassSwf_complete);
			//loader.addEventListener(ProgressEvent.PROGRESS, loadProgressFun);
		}
		
		private function loadProgressFun(e:ProgressEvent):void 
		{
			//loadedPer = Math.floor(e.weightPercent*100);
		}
		
		private function loadClassSwf_complete(e:Event):void 
		{
			//trace("getClass complete");
			loadedSwfList[currentUrl] = 2;
			e.target.removeEventListener(Event.COMPLETE, loadClassSwf_complete);
			//e.target.removeEventListener(BulkProgressEvent.PROGRESS, loadProgressFun);
			dispatchComplete(currentClass);
		}
		
		public function dispatchComplete(className:String):void {
			var e:SwfClassCacheEvent = new SwfClassCacheEvent(SwfClassCacheEvent.COMPLETE);
			e.className = className;
			e.hasClass = hasClass(className);
			if (!e.hasClass) 
			{
				SysTracer.systrace("no class", className);
			}
			dispatchEvent(e);
			
			if (loadArr.length>0) 
			{
				currentClass = loadArr[0] as String;
				if (hasClass(currentClass)) 
				{
					loadArr.splice(0, 1);
					dispatchComplete(currentClass);
				}else {
					var tmpurl:String = getClassSwfUrl(currentClass);
					if (loadedSwfList[tmpurl]==2) 
					{
						loadArr.splice(0, 1);
						dispatchComplete(currentClass);
					}else {
						loadClassSwf(tmpurl);
					}
					//addEventListener(Event.COMPLETE,loadClassComplete);
				}
			}
		}
		
		public function hasClass(className:String):Boolean {
			if (appDomain.hasDefinition(className)) 
			{
				return true;
			}else {
				return false;
			}
			
		}
		
		public function getClass(className:String):Class {
			if (appDomain.hasDefinition(className)) 
			{
				return appDomain.getDefinition(className) as Class;
			}
			return null;
		}
		
		public static function getInstance():SwfClassCache
		{
			if (instance == null)
			{
				instance = new SwfClassCache( new Private() );
			}
			return instance;
		}
		
	}
	
}
class Private {}