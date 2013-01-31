package happyfish.actModule.giftGetAct.commond 
{
	import flash.events.Event;
	import happyfish.actModule.giftGetAct.manager.GiftDomain;
	import happymagic.model.command.BaseDataCommand;
	/**
	 * ...
	 * @author ZC
	 */
	//忽略礼物请求
	public class IgnoreGiftCommand extends BaseDataCommand
	{
		
		public function IgnoreGiftCommand() 
		{
			
		}
	
		public function setData(_giftDiaryId:String):void 
		{
			createLoad();
			createRequest(GiftDomain.getInstance().getInterfaceUrl("IgnoreGift"), { giftDiaryId:_giftDiaryId } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
			
		}		
	}

}