package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author jj
	 */
	public class TransMagicVo extends BasicVo
	{
		public var trans_mid:uint;
		public var content:String="";
		public var name:String;
		public var class_name:String;
		public var mp:uint;
		public var needLevel:uint;
		public var needFriendNum:uint;
		public var decorId:Array;
		public var itemId:Array;
		public var coin:uint;
		public var gem:uint;
		public var exp:uint;
		public var time:uint;
		public function TransMagicVo() 
		{
			
		}
	}

}