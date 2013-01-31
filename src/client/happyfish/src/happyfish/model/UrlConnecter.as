package happyfish.model 
{
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.IOErrorEvent;
	import flash.events.SecurityErrorEvent;
	import flash.net.URLLoader;
	import flash.net.URLLoaderDataFormat;
	import flash.net.URLRequest;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	import happyfish.events.UrlConnectEvent;
	import happyfish.utils.SysTracer;
	
	[Event(name = "complete", type = "flash.events.Event")]
	/**
	 * ...
	 * @author slamjj
	 */
	public class UrlConnecter extends EventDispatcher
	{
		public var data:*;
		private var _dataFormat:String=URLLoaderDataFormat.TEXT;
		protected var loader:URLLoader;
		
		public static var eventManager:EventDispatcher;
		
		private var outTimeId:uint;
		private var outTimeDelay:uint=500000;
		
		public var retry:Boolean = false;
		protected var takeError:Boolean=true;
		protected var requestbak:URLRequest;
		
		public function UrlConnecter() 
		{
			
		}
		
		public function load(request:URLRequest):void {
			
			requestbak = request;
			
			loader = new URLLoader();
			loader.addEventListener(Event.COMPLETE, load_complete);
			loader.addEventListener(IOErrorEvent.IO_ERROR, load_ioError);
			loader.addEventListener(SecurityErrorEvent.SECURITY_ERROR, load_SecurityError);
			loader.dataFormat = URLLoaderDataFormat.TEXT;
			SysTracer.systrace("load $$$ ", request.url);
			loader.load(request);
			
			//outTimeId=setTimeout(loadOutTime,outTimeDelay);
		}
		
		protected function loadOutTime():void
		{
			clearListener();
			
		}
		
		public function load_complete(e:Event):void 
		{
			SysTracer.systrace("load_complete");
			clearListener();
			
			if ((e.target as URLLoader).data) 
			{
				data = e.target.data;
			}
			
			clearTimer();
			dispatchEvent(e);
		}
		
		public function set dataFormat(value:String):void {
			_dataFormat = value;
		}
		
		protected function load_SecurityError(e:SecurityErrorEvent):void 
		{
			SysTracer.systrace("load SecurityError", e);
			clearListener();
			clearTimer();
			
		}
		
		protected function load_ioError(e:IOErrorEvent):void 
		{
			SysTracer.systrace("load ioError", e);
			clearListener();
			clearTimer();
		}
		
		protected function clearListener():void {
			loader.removeEventListener(Event.COMPLETE, load_complete);
			loader.removeEventListener(IOErrorEvent.IO_ERROR, load_ioError);
			loader.removeEventListener(SecurityErrorEvent.SECURITY_ERROR, load_SecurityError);
		}
		
		protected function clearTimer():void {
			if (outTimeId) clearTimeout(outTimeId);
			outTimeId = 0;
		}
		
		protected function dispatchError(errorStr:String):void {
			var e:UrlConnectEvent = new UrlConnectEvent(UrlConnectEvent.ERROR);
			e.errorType = errorStr;
			dispatchEvent(e);
		}
	}

}