package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author jj
	 */
	public class RoomSizeVo extends BasicVo
	{
		public var id:uint;
		public var sizeX:uint;
		public var sizeZ:uint;
		public var coin:uint;
		public var gem:uint;
		public var needLevel:uint;
		//需要的好友数
		public var needFriendNum:uint;
		public var crystal:uint;
		public function RoomSizeVo() 
		{
			
		}
		
		override public function setData(obj:Object):BasicVo 
		{
			return super.setData(obj);
		}
		
	}

}