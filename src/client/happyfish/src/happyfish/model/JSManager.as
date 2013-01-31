package happyfish.model 
{
	import flash.external.ExternalInterface;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class JSManager 
	{
		
		public function JSManager(access:Private) 
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
				throw new Error( "JSManager"+"单例" );
			}
		}
		
		public static function getInstance():JSManager
		{
			if (instance == null)
			{
				instance = new JSManager( new Private() );
			}
			return instance;
		}
		
		/**
		 * 调用JS
		 * @param	command		JS方法名
		 * @param	...args		JS接受的参数
		 * @return	JS的返回
		 */
		public function call(command:String,...args):* {
			if (ExternalInterface.available) 
			{
				return ExternalInterface.call(command, args);
			}
			return "JS未启动";
		}
		
		
		private static var instance:JSManager;
		
	}
	
}
class Private {}