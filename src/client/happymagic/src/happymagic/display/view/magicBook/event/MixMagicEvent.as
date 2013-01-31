package happymagic.display.view.magicBook.event 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class MixMagicEvent extends Event 
	{
        public static const LAST:String = "last";
		public static const MIX:String = "mix";
		public static const BUY:String = "buy";
		public var msgs:*;
		public function MixMagicEvent(type:String, _msg:*, bubbles:Boolean = false, cancelable:Boolean = false) 
		{ 
			msgs = _msg;
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new MixMagicEvent(type,msgs, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("MixMagicEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}