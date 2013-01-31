package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * 道具卡基础表
	 * @author Beck
	 */
	public class ItemClassVo extends BasicVo
	{
		public var i_id:uint;
		public var sale:Boolean;
		public var name:String;
		public var content:String;
		public var class_name:String;
		public var type:uint;
		public var add_mp:uint;
		public var coin:uint;
		public var gem:uint;
		public function ItemClassVo() 
		{
			
		}
		
	}

}