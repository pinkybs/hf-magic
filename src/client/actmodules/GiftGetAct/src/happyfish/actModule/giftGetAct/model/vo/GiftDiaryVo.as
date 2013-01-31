package happyfish.actModule.giftGetAct.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author ZC
	 */
	//礼物日志数据
	public class GiftDiaryVo extends BasicVo
	{
		public var id:String;
		public var date:uint;//发送时间
		public var expTime:Number;//过期时间
		public var uid:String;//发送人uid
		public var hasGet:Boolean;//该礼物是否已经被接受过
		public var giftType:uint;//礼物分类
		public var giftCid:uint;//礼物ID
		public var name:String;//礼物的名字
		public var className:String;//礼物的类名
		public var map:String;//备用
		
		public function GiftDiaryVo() 
		{
			
		}
		
	}

}