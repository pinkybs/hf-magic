package happymagic.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author jj
	 */
	public class SwitchEvent extends Event 
	{
		public static const SHOW_SWITCH:String = "showSwitch";
		public function SwitchEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new SwitchEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("SwitchEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}