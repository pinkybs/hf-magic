package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	/**
	 * ...
	 * @author jj
	 */
	public class upgradeSwitchBagCommand extends BaseDataCommand
	{
		
		public function upgradeSwitchBagCommand() 
		{
			
		}
		
		public function upgrade():void {
			createLoad();
			
			createRequest(InterfaceURLManager.getInstance().getUrl("upSwitchBag"));
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}