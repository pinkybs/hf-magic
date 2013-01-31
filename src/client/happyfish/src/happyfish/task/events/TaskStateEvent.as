package happyfish.task.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class TaskStateEvent extends Event 
	{
		public static const NEED_CHECK_STATE:String = "needCheckTaskState";
		//有任务的状态变化了
		public static const STATE_CHANGE:String = "hasTaskStateChange";
		
		
		public function TaskStateEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new TaskStateEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("TaskStateEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}