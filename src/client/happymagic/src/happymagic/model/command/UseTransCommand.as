package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	/**
	 * ...
	 * @author jj
	 */
	public class UseTransCommand extends BaseDataCommand
	{
		
		public function UseTransCommand() 
		{
			
		}
		
		public function useTrans(trans_mid:uint,uid:String):void {
			createLoad();
			
			createRequest(InterfaceURLManager.getInstance().getUrl("useTrans"), { trans_mid:trans_mid, uid:uid } );
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			takeResult = false;
			
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}