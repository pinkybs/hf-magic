package happymagic.model.vo 
{
	import flash.text.TextField;
	import happymagic.manager.DataManager;
	/**
	 * 魔法基础表
	 * @author Beck
	 */
	public class MagicClassVo
	{
		public var magic_id:int;
		public var name:String;
		public var content:String="";
		public var magic_type:int;
		public var class_name:String;
		public var mp:int;
		public var exp:int;
		public var time:int;
		public var need_level:int;
		public var needFriendNum:uint;
		public var actMovie:String;
		
		public var coin:uint;
		
		public var learn_coin:uint;
		public var learn_gem:uint;
		
		public function MagicClassVo() 
		{
			
		}
		
		public function setData(obj:Object):MagicClassVo 
		{
			for (var name:String in obj) 
			{
				this[name] = obj[name];
			}
			return this;
		}
		
		
		public function get crystal2():int {
			
			return -1;
		}
	}

}