package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author Beck
	 */
	public class DecorClassVo extends BasicVo
	{
		public var d_id:uint;
		public var type:uint;
		public var type_show:uint;
		public var class_name:String;
		public var magic_type:uint;
		public var name:String;
		public var level:uint=1;
		public var size_x:uint;
		public var size_y:uint;
		public var size_z:uint;
		public var door_refresh_time:uint;
		//门的限制人数
		public var door_guest_limit:int;
		
		//卖的价钱
		public var sale_coin:uint;
		
		public var max_magic:int;
		
		public function DecorClassVo() 
		{
			
		}
		
		
		
	}

}