package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author jj
	 */
	public class EnemyClassVo extends BasicVo
	{
		public var enemyCid:uint;
		public var name:uint;
		public var avatarId:uint;
		public var hp:int;
		public var heal:uint;
		
		public var class_name:String;
		public function EnemyClassVo() 
		{
			
		}
		
		override public function setData(obj:Object):BasicVo 
		{
			super.setData(obj);
			
			class_name = DataManager.getInstance().getAvatarVo(avatarId).className;
			
			return this;
		}
		
	}

}