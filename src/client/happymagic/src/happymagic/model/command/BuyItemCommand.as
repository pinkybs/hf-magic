package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.ResultVo;
	/**
	 * ...
	 * @author jj
	 */
	public class BuyItemCommand extends BaseDataCommand
	{
		
		public function BuyItemCommand() 
		{
			
		}
		
		public function buyItem(i_id:uint,num:uint):void {
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("buyItem"), { i_id:i_id,num:num } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
	}

}