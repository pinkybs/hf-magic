package happyfish.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class SwfLoaderEvent extends Event 
	{
		public static const COMPLETE:String = "swfLoaderComplete";
		public function SwfLoaderEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new SwfLoaderEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("SwfLoaderEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}