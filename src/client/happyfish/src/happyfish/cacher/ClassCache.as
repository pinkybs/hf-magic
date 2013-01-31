package happyfish.cacher
{
	import flash.display.Loader;
	import flash.display.Stage;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.ProgressEvent;
	import flash.net.URLRequest;
	import flash.system.ApplicationDomain;
	import flash.system.LoaderContext;
	import flash.system.SecurityDomain;
	import happyfish.manager.module.interfaces.IClassManager;
	import happyfish.util.queue.AbstractQueue;
	import happyfish.util.queue.driver.queue.ClassCacherQueue;
	
	[Event(name = "complete", type = "flash.events.Event")]
	
	/**
	 * ...
	 * @author slamjj
	 * 素材类的加载与管理类
	 */
	public class ClassCache extends EventDispatcher implements IClassManager
	{
		public var stage:Stage;
		public var url:String;
		private static var queueArray:Array = new Array();
		public function ClassCache(access:Private) 
		{
			
			if (access != null)
			{	
				if (instance == null)
				{				
					instance = this;
					loadedSwfList = new Object();
				}
			}
			else
			{	
				throw new Error( "ClassCache"+"单例" );
			}
		}
		
		/**
		 * 加载指定swf文件
		 * @param	url
		 */
		public function loadClassSwf(url:String):void {
			if (loadedSwfList[url]) 
			{
				dispatchEvent(new Event(Event.COMPLETE));
				return;
			}
			this.url = url;
			loadedPer = 0;
			var loader:Loader = new Loader();

			loader.contentLoaderInfo.addEventListener(Event.COMPLETE, loadClassSwf_complete);
			loader.contentLoaderInfo.addEventListener(ProgressEvent.PROGRESS, loadProgressFun);
			
			loader.load(new URLRequest(url), new LoaderContext(false, ApplicationDomain.currentDomain));//,SecurityDomain.currentDomain));
		}
		
		/**
		 * 把swf文件放入加载队列中
		 * @param	url
		 */
		public function load(url:String):void
		{
			if (!queueArray[url]) {
				queueArray[url] = true;
				var class_cacher_queue:ClassCacherQueue = ClassCacherQueue.getInstance();
				class_cacher_queue.push(url);
			}
		}
		
		/**
		 * 检查内存中是否已有指定类
		 * @param	className
		 * @return	[Boolean]是否有指定类
		 */
		public function hasClass(className:String):Boolean {
			if (ApplicationDomain.currentDomain.hasDefinition(className)) 
			{
				return true;
			}else {
				return false;
			}
		}
		
		/**
		 * 从内存中取出返回指定类
		 * @param	className
		 * @return	[Class]
		 */
		public function getClass(className:String):Class {
			if (ApplicationDomain.currentDomain.hasDefinition(className)) 
			{
				return ApplicationDomain.currentDomain.getDefinition(className) as Class;
			}
			return null;
		}
		
		private function loadProgressFun(e:ProgressEvent):void 
		{
			if (e.bytesLoaded>0) 
			{
				//trace(e.bytesLoaded,e.bytesTotal);
				loadedPer = Math.floor(e.bytesLoaded / e.bytesTotal * 100);
			}
			
		}
		
		private function loadClassSwf_complete(e:Event):void 
		{
			//trace("getClass complete");
			e.target.removeEventListener(Event.COMPLETE, loadClassSwf_complete);
			e.target.loader.contentLoaderInfo.removeEventListener(ProgressEvent.PROGRESS, loadProgressFun);
			loadedSwfList[this.url] = true;
			dispatchEvent(new Event(Event.COMPLETE));
		}
		
		private function dispatchComplete():void {
			dispatchEvent(new Event(Event.COMPLETE));
		}
		
		
		
		public static function getInstance():ClassCache
		{
			if (instance == null)
			{
				instance = new ClassCache( new Private() );
			}
			return instance;
		}
		
		
		private static var instance:ClassCache;
		public var loadedPer:uint;
		private var loadedSwfList:Object;
		
	}
	
}
class Private {}