package happyfish.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.command.BaseDataCommand;
	
	/**
	 * ...
	 * @author jj
	 */
	public class SaveGuidesCommand extends BaseDataCommand
	{
		
		public function SaveGuidesCommand() 
		{
			
		}
		
		public function save(gid:uint):void {
			createLoad();
			
			createRequest(InterfaceURLManager.getInstance().getUrl("saveGuides"), { gid:gid } );
			
			loader.load(request);
			//commandComplete();
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}