package happyfish.actModule.giftGetAct.commond 
{
	import flash.events.Event;
	import happyfish.actModule.giftGetAct.event.GiftGetActEvent;
	import happyfish.actModule.giftGetAct.manager.GiftDomain;
	import happymagic.model.command.BaseDataCommand;
	/**
	 * ...
	 * @author zc
	 */
	public class GiftGetKnowNewGiftCommand extends BaseDataCommand
	{
		
		public function GiftGetKnowNewGiftCommand() 
		{
			
		}

		public function setData():void 
		{
			createLoad();
			createRequest(GiftDomain.getInstance().getInterfaceUrl("GiftGetKnowNew"));
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);			
			
			commandComplete();
			
		}		
	}

}