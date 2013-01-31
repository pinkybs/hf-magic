package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author jj
	 */
	public class NpcVo extends BasicVo
	{
		public var sceneId:uint;
		public var npcId:uint;
		public var avatarId:uint;
		public var name:String;
		public var x:uint;
		public var y:uint;
		public var z:uint;
		public var chats:String;
		public var faceX:int;
		public var faceY:int;
		public var shop:uint;
		public function NpcVo() 
		{
			
		}
		
		public function get class_name():String {
			return DataManager.getInstance().getAvatarVo(avatarId).className;
		}
		
		
		
	}

}