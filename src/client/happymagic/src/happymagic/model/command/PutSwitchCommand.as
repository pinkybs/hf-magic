package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	/**
	 * ...
	 * @author jj
	 */
	public class PutSwitchCommand extends BaseDataCommand
	{
		public var curNum:uint;
		
		public function PutSwitchCommand() 
		{
			
		}
		
		public function put(num:uint):void {
			
			curNum = num;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("putSwitch"), { num:num } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}