package happymagic.model.command 
{
	import flash.events.Event;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.MagicUrlLoader;
	/**
	 * ...
	 * @author jj
	 */
	public class LoadUserInfoCommand extends BaseDataCommand
	{
		
		public function LoadUserInfoCommand() 
		{
			
		}
		
		public function load():void 
		{
			createLoad();
			
			//var request:URLRequest = new URLRequest(InterfaceURLManager.getInstance().getUrl('loadUserInfo'));
			var request:URLRequest = new URLRequest(InterfaceURLManager.getInstance().staticHost+"data/userInfo.txt");
			request.method = URLRequestMethod.POST;
			var vars:URLVariables = new URLVariables();
			request.data = vars;
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}