package happymagic.actModule.event 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author zc
	 */
	public class HappyMagicDMEvent extends Event 
	{
		public static var COMPLETE:String  = "HappyMagicDMComplete";
		public function HappyMagicDMEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new HappyMagicDMEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("HappyMagicDMEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}