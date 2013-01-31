package happyfish.actModule.giftGetAct.commond 
{
	import com.brokenfunction.json.encodeJson;
	import flash.events.Event;
	import happyfish.actModule.giftGetAct.manager.GiftDomain;
	import happymagic.model.command.BaseDataCommand;
	/**
	 * ...
	 * @author ZC
	 */
	//接收礼物请求接口
	public class ReceiveGiftCommand extends BaseDataCommand
	{
		private var strgiftDiaryId:String = "";
		public function ReceiveGiftCommand() 
		{
			
		}
		
		public function setData(_giftDiaryId:Array):void 
		{
			strgiftDiaryId = _giftDiaryId.join("-");

			createLoad();
			createRequest(GiftDomain.getInstance().getInterfaceUrl("ReceiveGift"), { giftDiaryId : strgiftDiaryId } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
			
		}
		
	}

}