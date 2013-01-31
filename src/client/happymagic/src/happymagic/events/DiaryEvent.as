package happymagic.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author jj
	 */
	public class DiaryEvent extends Event 
	{
		public static const DIARY_ADDED:String = "diaryAdded";
		public function DiaryEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new DiaryEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("DiaryEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}