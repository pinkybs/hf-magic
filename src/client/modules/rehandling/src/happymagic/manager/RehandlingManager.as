package happymagic.manager 
{
	import happymagic.model.vo.RehandlingStateVo;
	import happymagic.model.vo.RehandlingVo;
	/**
	 * ...
	 * @author zc
	 */
	public class RehandlingManager 
	{
		private static var instance:RehandlingManager;		
		public function RehandlingManager(access:Private) 
		{
			if (access != null)
			{	
				if (instance == null)
				{				
					instance = this;
				}
			}
			else
			{	
				throw new Error( "GiftDomain"+"单例" );
			}			
		}
			
		public static function getInstance():RehandlingManager
		{
			if (instance == null)
			{
				instance = new RehandlingManager( new Private() );
			}
			return instance;
		}
		
		public function getRehandlingStateVo(_avatarId:int):RehandlingStateVo
		{
			var temp:Array = DataManager.getInstance().getVar("RehandlingInit");
			
			for (var i:int = 0; i < temp.length; i++ )
			{
				if ((temp[i] as RehandlingStateVo).avatarId == _avatarId)
				{
					return temp[i];
				}
			}
			
			return null;
		}

		public function getRehandlingVo(_avatarId:int):RehandlingVo
		{
			var temp:Array = DataManager.getInstance().getVar("RehandlingInitstatic");
			
			for (var i:int = 0; i < temp.length; i++ )
			{
				if ((temp[i] as RehandlingVo).avatarId == _avatarId)
				{
					return temp[i];
				}
			}
			
			return null;
		}	
		
		//修改动态数据
		public function setRehandlingStateVo(_avatarId:int):void
		{
			var temp:Array = DataManager.getInstance().getVar("RehandlingInit");
			
			for (var i:int = 0; i < temp.length; i++ )
			{
				if ((temp[i] as RehandlingStateVo).avatarId == _avatarId)
				{
					(temp[i] as RehandlingStateVo).lock = 0;
				}
			}					
		}
	}

}
class Private {}