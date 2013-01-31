package happymagic.actModule.signAward.View.event 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class SignAwardEvent extends Event 
	{
		public static const COMPLETE:String = "signawardeventCompete";
		public function SignAwardEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new SignAwardEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("SignAwardEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}