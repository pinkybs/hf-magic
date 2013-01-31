package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author slamjj
	 */
	public class StoryActionVo extends BasicVo 
	{
		public var npcId:uint;
		public var avatarId:uint;
		public var x:uint;
		public var y:uint;
		public var faceX:uint;
		public var faceY:uint;
		public var content:String;
		public var chatTime:uint=2500;
		public var camera:uint;
		public var wait:uint;
		public var immediately:uint;
		public var taskId:uint;
		public var decorId:Array;
		public var itemId:Array;
		public var coin:uint;
		public var gem:uint;
		public var hide:uint;
		public function StoryActionVo() 
		{
			
		}
		
	}

}