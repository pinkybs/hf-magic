package happyfish.model 
{
	import br.com.stimuli.loading.BulkLoader;
	import br.com.stimuli.loading.BulkProgressEvent;
	import br.com.stimuli.loading.loadingtypes.LoadingItem;
	import flash.events.Event;
	import flash.system.LoaderContext;
	import happyfish.utils.SysTracer;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class SwfLoader 
	{
		private static var instance:SwfLoader;
		static public var loader:BulkLoader;
		public var loaderContext:LoaderContext;
		public function SwfLoader(access:Private) 
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
				throw new Error( "SwfLoader"+"单例" );
			}
		}
		
		public static function getInstance():SwfLoader
		{
			if (instance == null)
			{
				instance = new SwfLoader( new Private() );
				loader = new BulkLoader("swfLoader",1);
				loader.addEventListener(BulkLoader.ERROR, loaderError);
				loader.addEventListener(BulkProgressEvent.COMPLETE, loaderComplete);
			}
			return instance;
		}
		
		static private function loaderComplete(e:BulkProgressEvent):void 
		{
			
		}
		
		static private function loaderError(e:Event):void 
		{
			
		}
		
		public function addGroup(name:String,urls:Array):BulkLoader {
			var tmploader:BulkLoader = new BulkLoader(name,1);
			for (var i:int = 0; i < urls.length; i++) 
			{
				SysTracer.systrace(urls[i]);
				tmploader.add(urls[i], { id:urls[i], context:loaderContext, priority:1 } );
			}
			tmploader.start();
			
			return tmploader;
		}
		
		public function add(url:String, priority:uint = 3, context:LoaderContext = null):LoadingItem {
			var returnValue:LoadingItem;
			
			if (!context) 
			{
				context = loaderContext;
			}
			returnValue = loader.add(url, { id:url, context:context, priority:priority } );
			
			start();
			
			return returnValue;
		}
		
		public function load(url:String, priority:uint = 3, context:LoaderContext = null):LoadingItem {
			var returnValue:LoadingItem;
			if (context) 
			{
				returnValue = loader.add(url, { id:url, context:context, priority:priority } );
			}else {
				returnValue = loader.add(url, { id:url, context:loaderContext, priority:priority } );
			}
			
			start();
			
			return returnValue;
		}
		
		public function start():void {
			//如果还未在运行中
			if (!loader.isRunning) 
			{
				loader.start();
			}
		}
		
		
		
		
	}
	
}
class Private {}