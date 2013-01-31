package happyfish.actModule.giftGetAct.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author zc
	 */
	public class GiftFriendUserVo extends BasicVo
	{
		public var face:String;//玩家的头像
		public var giftAble:Boolean;//这个人还能不能送他礼物 boolean
		public var name:String;//玩家的名字
		public var uid:String;//玩家的uid
		public var giftRequestAble:uint;//你能不能对他发请求 boolean
		
		public function GiftFriendUserVo() 
		{
			
		}
		
	}

}