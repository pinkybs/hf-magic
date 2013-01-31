package happyfish.task.vo 
{
	
	/**
	 * ...
	 * @author slamjj
	 */
	public interface ITaskVo 
	{
		function get t_id():uint;
		function set t_id(value:uint):void;
		
		function get finish_condition():Array;
		function set finish_condition(value:Array):void;
		
		function get fc_curNums():Array;
		function set fc_curNums(value:Array):void;
		
		function get state():uint;
		function set state(value:uint):void;
	}
	
}