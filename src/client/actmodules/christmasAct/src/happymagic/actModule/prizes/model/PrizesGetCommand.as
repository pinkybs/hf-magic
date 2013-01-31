package happymagic.actModule.prizes.model 
{
	import flash.events.Event;
	import happyfish.manager.actModule.vo.ActVo;
	import happymagic.manager.DataManager;
	import happymagic.model.command.BaseDataCommand;
	
	/**
	 * ...
	 * @author 
	 */
	public class PrizesGetCommand extends BaseDataCommand 
	{
		public var id:uint;
		
		public function PrizesGetCommand() 
		{
			
		}
		
		public function getAward(url:String, _id:uint):void {
			
			id = _id;
			
			createLoad();
			
			createRequest(url,{id:id});
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}