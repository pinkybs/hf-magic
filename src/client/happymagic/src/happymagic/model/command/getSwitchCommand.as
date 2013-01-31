package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	/**
	 * ...
	 * @author jj
	 */
	public class getSwitchCommand extends BaseDataCommand
	{
		
		public function getSwitchCommand() 
		{
			
		}
		
		public function getSwitch():void {
			createLoad();
			
			createRequest(InterfaceURLManager.getInstance().getUrl("getSwitch"));
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}