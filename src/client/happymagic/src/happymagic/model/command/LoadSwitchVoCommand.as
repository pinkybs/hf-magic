package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	/**
	 * ...
	 * @author jj
	 */
	public class LoadSwitchVoCommand extends BaseDataCommand
	{
		
		public function LoadSwitchVoCommand() 
		{
			
		}
		
		public function load():void {
			createLoad();
			
			createRequest(InterfaceURLManager.getInstance().getUrl("loadSwitchVo") );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}