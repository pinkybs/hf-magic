package happyfish.manager
{
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class InterfaceURLManager 
	{
		private static var instance:InterfaceURLManager;
		public var staticHost:String;
		public var interfaceHost:String;
		public var urls:Object = { };
		public var tmpUrls:Array = new Array();
		public function InterfaceURLManager(access:Private) 
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
				throw new Error( "InterfaceURLManager"+"单例" );
			}
		}
		
		public static function getInstance():InterfaceURLManager
		{
			if (instance == null)
			{
				instance = new InterfaceURLManager( new Private() );
			}
			return instance;
		}
		
		public function setUrl(urlName:String, value:String):void {
			urls[urlName] = value;
		}
		
		public function hasUrl(urlName:String):Boolean {
			if (urls[urlName]) 
			{
				return true;
			}else {
				return false;
			}
		}
		
		public function getUrl(urlName:String, withTime:Boolean = false):String {
			if (this.tmpUrls.indexOf(urlName) != -1) {
				return 'data/' + urlName + ".txt";
			}
			
			if (urls[urlName]) 
			{
				if (withTime) 
				{
					return interfaceHost+urls[urlName]+time();
				}else {
					return interfaceHost+urls[urlName];
				}
				
			}
			return null;
		}
		
		public function time():String {
			return "?time="+new Date().getTime().toString();
		}
		
		
	}
	
}
class Private {}