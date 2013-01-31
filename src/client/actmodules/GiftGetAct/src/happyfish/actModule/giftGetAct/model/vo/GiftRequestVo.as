package happyfish.actModule.giftGetAct.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class GiftRequestVo extends BasicVo
	{
		public var id:String;
		public var date:uint;
		public var expTime:Number;//过期时间
	    public var uid:String;//发送人的ID
		public var gifts:Array;//请求的礼物
		public var hasGet:Boolean;//是不是已经满足了对方的要求
		
		public function GiftRequestVo() 
		{
			
		}
		
	}

}