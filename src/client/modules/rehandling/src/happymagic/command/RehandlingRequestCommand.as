package happymagic.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.command.BaseDataCommand;
	/**
	 * ...
	 * @author zc
	 */
	public class RehandlingRequestCommand extends BaseDataCommand
	{
		
		public function RehandlingRequestCommand() 
		{
			
		}

		public function setData(_avatarId:int):void 
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("RehandlingRequest"), { avatarId : _avatarId } );
			
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
            super.load_complete(e);		
			commandComplete();
		}			
	}

}