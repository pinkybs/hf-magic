package happyfish.feed.Command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.command.BaseDataCommand;
	/**
	 * ...
	 * @author ZC
	 */
	public class FeedDataRequestCommond extends BaseDataCommand
	{
		
		public function FeedDataRequestCommond() 
		{
			
		}
		
		public function setData(_id:uint):void
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("FeedDataRequest"), { id:_id } );
			
			loader.load(request);			
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}