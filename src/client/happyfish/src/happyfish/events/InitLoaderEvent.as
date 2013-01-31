package happyfish.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class InitLoaderEvent extends Event 
	{
		public static const LOAD_WORDS_COMPLETE:String = "loadWordsComplete";
		public function InitLoaderEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new InitLoaderEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("InitLoaderEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}