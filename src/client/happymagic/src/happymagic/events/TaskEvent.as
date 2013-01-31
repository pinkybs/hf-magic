package happymagic.events 
{
	import flash.events.Event;
	import happymagic.model.vo.TaskVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class TaskEvent extends Event 
	{
		
		public static const TASKS_STATE_CHANGE:String = "taskStateChange";
		
		public var changeTasks:Array;
		public var addTasks:Array;
		public var finishTasks:Array;
		
		public function TaskEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			changeTasks = new Array();
			addTasks = new Array();
			finishTasks = new Array();
		} 
		
		public override function clone():Event 
		{ 
			return new TaskEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("TaskEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}