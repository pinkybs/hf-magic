package happymagic.actModule.signAward.Commond 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.command.BaseDataCommand;
	/**
	 * ...
	 * @author zc
	 */
	public class SignAwardCommond extends BaseDataCommand
	{
		
		public function SignAwardCommond() 
		{
			
		}

		
		public function init():void
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl('SignAward') );
			loader.load(request);
		}
		
		
		
		override protected function load_complete(e:Event):void 
		{
			
			super.load_complete(e);
			commandComplete();
		}		
	}

}