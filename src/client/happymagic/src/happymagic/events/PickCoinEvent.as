package happymagic.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class PickCoinEvent extends Event 
	{
		public static const PICK_COMPLETE:String = "pickCoinComplete";
		public function PickCoinEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new PickCoinEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("PickCoinEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}