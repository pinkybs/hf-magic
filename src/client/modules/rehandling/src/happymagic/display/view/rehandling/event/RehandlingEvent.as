package happymagic.display.view.rehandling.event 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author zc
	 */
	public class RehandlingEvent extends Event 
	{
		public static const SELECT:String = "RehandlingSelect";
		public static const COMPLETE:String = "RehandlingComplete";
		
		public function RehandlingEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new RehandlingEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("RehandlingEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}