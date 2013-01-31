package happymagic.actModule.signAward.Commond 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.command.BaseDataCommand;
	/**
	 * ...
	 * @author zc
	 */
	//发送请求变成粉丝
	public class SignAwardIsFanCommand extends BaseDataCommand
	{
		
		public function SignAwardIsFanCommand() 
		{
			
		}

		public function init():void
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl('SignAwardIsFan') );
			loader.load(request);
		}
		
		
		
		override protected function load_complete(e:Event):void 
		{
			
			super.load_complete(e);
			commandComplete();
		}			
	}

}