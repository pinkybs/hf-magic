package happyfish.modules.gift.interfaces 
{
	/**
	 * ...
	 * @author zc
	 */
	public interface IGiftUserVo 
	{
		function set uid(str:String):void
		function get uid():String
		function get name():String 
		function set name(value:String):void 
        function get face():String 	
		function set face(value:String):void 
        function get giftAble():Boolean 		
		function set giftAble(value:Boolean):void 		
		function get giftRequestAble():Boolean 
		function set giftRequestAble(value:Boolean):void 		
		function get giftNum():uint 		
		function set giftNum(value:uint):void 		
		function get giftRequestNum():uint 	
		function set giftRequestNum(value:uint):void 
		function get level():uint 
        function set level(value:uint):void 
		function get className():String 	
		function set className(value:String):void 	
	}

}