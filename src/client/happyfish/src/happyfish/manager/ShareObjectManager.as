package happyfish.manager 
{
	import flash.net.SharedObject;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class ShareObjectManager 
	{
		private var data:Object;
		
		public function ShareObjectManager(access:Private) 
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
				throw new Error( "ShareObjectManager"+"单例" );
			}
		}
		
		public function init(soName:String,defaultObj:Object=null):void {
			//获取设置缓存
					so = SharedObject.getLocal(soName,"/");
					//so.data.setting = null;
					//so.flush();
					if (so.data.data) 
					{
						data = so.data.data;
					}else {
						data = new Object();
						for (var name:String in defaultObj) 
						{
							data[name] = defaultObj[name];
						}
						so.data.data = data;
						saveData();
					}
		}
		
		private function saveData():void {
			try{
				so.flush();
			}catch (err:Error)
			{
				
			}
		}
		
		public static function getInstance():ShareObjectManager
		{
			if (instance == null)
			{
				instance = new ShareObjectManager( new Private() );
			}
			return instance;
		}
		
		public function set bgSound(value:Boolean):void {
			data.bgSound = value;
			saveData();
		}
		
		public function get bgSound():Boolean {
			return data.bgSound;
		}
		
		public function set soundEffect(value:Boolean):void {
			data.soundEffect = value;
			saveData();
		}
		
		public function get soundEffect():Boolean {
			return data.soundEffect;
		}
		
		
		private static var instance:ShareObjectManager;
		private var so:SharedObject;
		
		
	}
	
}
class Private {}