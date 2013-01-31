package happyfish.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class SwfClassCacheEvent extends Event 
	{
		public static const COMPLETE:String = "ClassCacheComplete";
		
		
		public var className:String;
		public var hasClass:Boolean;
		public function SwfClassCacheEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new SwfClassCacheEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("SwfClassCacheEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}