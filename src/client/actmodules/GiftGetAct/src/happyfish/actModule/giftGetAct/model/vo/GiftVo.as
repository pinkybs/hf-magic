package happyfish.actModule.giftGetAct.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class GiftVo extends BasicVo
	{
		public var type:uint;//礼物类型
		public var id:String;//礼物的ID
		public var lockLevel:uint;//礼物的解锁等级
		public var name:String;//礼物的名字
		public var className:String;//礼物的类名
		public var map:String;//备用
		
		public function GiftVo() 
		{
			
		}
		
	}

}