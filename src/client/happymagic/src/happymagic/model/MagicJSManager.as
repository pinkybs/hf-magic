package happymagic.model 
{
	import happyfish.model.JSManager;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class MagicJSManager 
	{
		
		public function MagicJSManager(access:Private) 
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
				throw new Error( "MagicJSManager"+"单例" );
			}
		}
		
		public static function getInstance():MagicJSManager
		{
			if (instance == null)
			{
				instance = new MagicJSManager( new Private() );
			}
			return instance;
		}
		
		/**
		 * 邀请好友
		 */
		public function goInvite():void {
			JSManager.getInstance().call("goInvite");
		}
		
		/**
		 * 加粉丝
		 */
		public function goFans():void {
			JSManager.getInstance().call("goFans");
		}
		
		/**
		 * 充值
		 */
		public function goPay():void {
			JSManager.getInstance().call("goPay");
		}
		
		/**
		 * 发FEED
		 * @param	feed
		 */
		public function sendFeed(feed:String):void {
			JSManager.getInstance().call("sendFeed",feed);
		}
		
		/**
		 * 关闭页面上的LOADING
		 */
		public function hideLoading():void {
			JSManager.getInstance().call("hideLoading");
		}
		
		
		private static var instance:MagicJSManager;
		
	}
	
}
class Private {}