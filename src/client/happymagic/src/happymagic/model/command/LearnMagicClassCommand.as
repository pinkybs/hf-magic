package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	/**
	 * ...
	 * @author jj
	 */
	public class LearnMagicClassCommand extends BaseDataCommand
	{
		
		public function LearnMagicClassCommand() 
		{
			
		}
		
		public function learn(magic_id:uint):void {
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("learnMagicClass"), { magic_id:magic_id } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}