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
	
	//赠送好友礼物
	public class SendGiftCommand extends BaseDataCommand
	{
		private var strfriendId:String = "";
		
		public function SendGiftCommand() 
		{
			
		}
		
		public function setData(_giftId:String,_friendId:Array):void 
		{
			strfriendId = _friendId.join("-");			
			
			createLoad();
			createRequest(GiftDomain.getInstance().getInterfaceUrl("SendGift"), { giftId:_giftId, friendId:strfriendId } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
			
		}
	}

}