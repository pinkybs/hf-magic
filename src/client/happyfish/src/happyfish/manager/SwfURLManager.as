package happyfish.manager 
{
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class SwfURLManager 
	{
		private static var instance:SwfURLManager;
		private var urls:Object;
		public function SwfURLManager(access:Private) 
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
				throw new Error( "SwfURLManager"+"单例" );
			}
		}
		
		public static function getInstance():SwfURLManager
		{
			if (instance == null)
			{
				instance = new SwfURLManager( new Private() );
			}
			return instance;
		}
		
		
		public function setValue(value:Array):void {
			urls = new Object();
			var index:uint;
			var tmpArr:Array;
			var tmpstr:String;
			for (var i:int = 0; i < value.length; i++) 
			{
				tmpArr = (value[i] as String).split("/");
				tmpstr = tmpArr[tmpArr.length - 1];
				tmpstr = tmpstr.split(".swf")[0];
				urls[tmpstr] = value[i];
			}
		}
		
		/**
		 * 获取指定文件名的全地址
		 * @param	swfname	不带后缀名的swf文件名
		 * @return	版本管理里记录的地址
		 */
		public function getOtherSWfUrl(swfname:String):String {
			if (urls[swfname]) 
			{
				return urls[swfname];
			}
			
			//如果版本管理里没有,就拼出全地址返回
			return InterfaceURLManager.getInstance().staticHost+swfname+".swf";
		}
		
		public function hasClassURL(className:String):Boolean {
			var tmparr:Array = className.split(".");
			var tmpName:String = tmparr[0]+tmparr[1];
			if (urls[tmpName]) 
			{
				return true;
			}
			return false;
		}
		
		public function getClassURL(className:String):String {
			var tmparr:Array = className.split(".");
			var tmpName:String = tmparr[0] + tmparr[1];
			if (urls[tmpName]) 
			{
				return urls[tmpName];
			}
			return InterfaceURLManager.getInstance().staticHost + tmpName+".swf";
		}
		
		
	}
	
}
class Private {}