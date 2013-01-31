package happyfish.actModule.giftGetAct.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author zc
	 */
	//选择时候的临时数据
	public class GiftMyWishVo extends BasicVo
	{
		public var id:String; //0代表是不存在
		public var type:uint;
		public var name:String;
		public var className:String
		
		public function GiftMyWishVo() 
		{
			
		}
		
	}

}