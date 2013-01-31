package happymagic.display.view.student.event 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author Mylovefly
	 */
	public class StudentListViewEvent extends Event 
	{
		public static const STUDENTAWARD:String = "studentawards";
		public function StudentListViewEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new StudentListViewEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("StudentListViewEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}