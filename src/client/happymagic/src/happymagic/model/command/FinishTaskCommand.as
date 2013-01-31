package happymagic.model.command
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.command.BaseDataCommand;
	
	/**
	 * ...
	 * @author jj
	 */
	public class FinishTaskCommand extends BaseDataCommand
	{
		
		public function FinishTaskCommand() 
		{
			
		}
		
		public function finish(t_id:uint):void {
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("finishTask"),{t_id:t_id});
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}