package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	/**
	 * ...
	 * @author jj
	 */
	public class UseItemCommand extends BaseDataCommand
	{
		
		public function UseItemCommand() 
		{
			
		}
		
		public function useItem(itemId:uint):void {
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("useItem"), { id:itemId } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}