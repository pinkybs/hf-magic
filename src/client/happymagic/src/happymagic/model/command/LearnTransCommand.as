package happymagic.model.command
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.command.BaseDataCommand;
	
	/**
	 * ...
	 * @author jj
	 */
	public class LearnTransCommand extends BaseDataCommand
	{
		
		public function LearnTransCommand() 
		{
			
		}
		
		public function learn(trans_mid:uint):void {
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("learnTrans"), { trans_mid:trans_mid } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}