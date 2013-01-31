package happymagic.model.command 
{
	import flash.events.Event;
	/**
	 * ...
	 * @author slamjj
	 */
	public class TestCommand extends BaseDataCommand 
	{
		
		public function TestCommand() 
		{
			
		}
		
		public function test(url:String):void {
			createLoad();
			createRequest(url);
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			data = objdata;
			
			commandComplete();
		}
		
	}

}