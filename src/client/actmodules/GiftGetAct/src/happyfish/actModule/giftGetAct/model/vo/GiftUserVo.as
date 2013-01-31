package happyfish.actModule.giftGetAct.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author zc
	 */
	public class GiftUserVo extends BasicVo
	{
		public var giftNum:uint;//未收过的礼物数量
		public var giftRequestNum:uint; //你收到的请求数量 
		public var isReleaseWish:Boolean;// 你是否已经发布过愿望
		public var isNewGift:Boolean;// 是否有新的礼物
		
		public function GiftUserVo() 
		{
			
		}
		
	}

}