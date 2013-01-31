package happymagic.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class StudentEvent extends Event 
	{
		public var sid:uint;
		public static const REFRESH_INSCENE_STUDENT:String = "refreshInsceneStudent";
		public function StudentEvent(type:String,_sid:uint, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			sid = _sid;
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new StudentEvent(type,sid, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("StudentEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}