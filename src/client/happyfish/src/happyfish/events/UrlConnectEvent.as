package happyfish.events
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class UrlConnectEvent extends Event 
	{
		public static const OUT_TIME:String = "outTime";
		public static const SYS_ERROR:String = "sysError";
		public static const IO_ERROR:String = "ioErroe";
		public static const ERROR:String = "UrlConnectError";
		public static const SECURITY_ERROR:String = "SecurityError";
		
		public var errorType:String;
		public function UrlConnectEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new UrlConnectEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("UrlConnectEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}