package happyfish.manager.keyboard 
{
	
	/**
	 * 键盘操作响应管理
	 * @author slamjj
	 */
	public class KeyManager 
	{
		
		public function KeyManager(access:Private) 
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
				throw new Error( "KeyManager"+"单例" );
			}
		}
		
		public function init():void {
			
		}
		
		public static function getInstance():KeyManager
		{
			if (instance == null)
			{
				instance = new KeyManager( new Private() );
			}
			return instance;
		}
		
		
		private static var instance:KeyManager;
		
	}
	
}
class Private {}