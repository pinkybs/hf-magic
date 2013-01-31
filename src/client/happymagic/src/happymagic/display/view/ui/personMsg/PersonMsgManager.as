package happymagic.display.view.ui.personMsg 
{
	import happyfish.scene.world.grid.IsoItem;
	
	/**
	 * ...
	 * @author jj
	 */

	public class PersonMsgManager 
	{
		private var msgs:Object = new Object();
		public function PersonMsgManager(access:Private) 
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
				throw new Error( "PersonMsgManager"+"单例" );
			}
		}
		
		public static function getInstance():PersonMsgManager
		{
			if (instance == null)
			{
				instance = new PersonMsgManager( new Private() );
			}
			return instance;
		}
		
		public function addMsg(target:IsoItem,str:String,time:uint=2000,_callback:Function=null):void {
			if (msgs[target.view.name]) 
			{
				(msgs[target.view.name] as PersonMsgView).setData(str,time,_callback);
			}else {
				msgs[target.view.name] = new PersonMsgView(target,str,time,_callback);
			}
		}
		
		public function delMsg(name:String):void 
		{
			if (msgs[name]) 
			{
				msgs[name] = null;
			}
		}
		
		
		private static var instance:PersonMsgManager;
		
	}
	
}
class Private {}