package happyfish.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author jj
	 */
	public class TriggerEvent extends Event 
	{
		
		
		public static const TRIGGER_COMPLETE:String = "triggerComplete";
		public static const TRIGGER_CANCEL:String = "triggerCancel";
		
		public var triggerName:String;
		public var value:*;
		
		public function TriggerEvent(type:String,_triggerName:String,_value:*, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			triggerName = _triggerName;
			value = _value;
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new TriggerEvent(type,triggerName,value, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("TriggerEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}