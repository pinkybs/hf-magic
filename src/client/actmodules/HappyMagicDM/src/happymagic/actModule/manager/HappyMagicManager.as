package happymagic.actModule.manager 
{
	import flash.display.MovieClip;
	import happymagic.actModule.model.vo.HappyMagicDMVo;
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author zc
	 */
	public class HappyMagicManager 
	{
		private static var instance:HappyMagicManager;
		
		public function HappyMagicManager(access:Private) 
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
				throw new Error( "HappyMagicManager"+"单例" );
			}			
		}
		
		public static function getInstance():HappyMagicManager
		{
			if (instance == null)
			{
				instance = new HappyMagicManager( new Private() );
			}
			return instance;
		}
		
		//返回页数
		public  function getNum(_name:String):uint
		{
			var happyMagicDMVo:HappyMagicDMVo = DataManager.getInstance().getVar("happyMagicDMVo");
			
			for (var i:int = 0; i < happyMagicDMVo.internalConnectionArray.length; i++ )
			{
				if (happyMagicDMVo.internalConnectionArray[i][0] == _name)
				{
					return happyMagicDMVo.internalConnectionArray[i][1];
				}
			}
			
			return 0;
			
		}

		//返回物品的ID
		public  function getId(_name:String):uint
		{
			var happyMagicDMVo:HappyMagicDMVo = DataManager.getInstance().getVar("happyMagicDMVo");
			
			for (var i:int = 0; i < happyMagicDMVo.internalConnectionArray.length; i++ )
			{
				if (happyMagicDMVo.internalConnectionArray[i][0] == _name)
				{
					return happyMagicDMVo.internalConnectionArray[i][2];
				}
			}
			
			return 0;
			
		}		
		
	}

}
class Private {}