package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author Beck
	 */
	public class StudentClassVo extends BasicVo
	{
		public var sid:uint;
		private var _avatar_id:uint;
		public var className:String;
		public var name:String;
		public var unLockMp:uint;
		//学生简介
		public var content:String;
		
		public function StudentClassVo() 
		{
			
		}
		
		public function get avatar_id():uint 
		{
			return _avatar_id;
		}
		
		public function set avatar_id(value:uint):void 
		{
			_avatar_id = value;
			
			var avatar:AvatarVo = DataManager.getInstance().getAvatarVo(_avatar_id);
			className = avatar.className;
			name = avatar.name;
		}
	}

}