package happyfish.task 
{
	
	/**
	 * ...
	 * @author slamjj
	 */
	public interface IConditionVo 
	{
		function get type():uint;
		function set type(value:uint):void;
		
		function get id():String;
		function set id(value:String):void;
		
		//需要的数量
		function get num():uint;
		function set num(value:uint):void;
		
		//当前数量
		function get currentNum():uint;
		function set currentNum(value:uint):void;
	}
	
}