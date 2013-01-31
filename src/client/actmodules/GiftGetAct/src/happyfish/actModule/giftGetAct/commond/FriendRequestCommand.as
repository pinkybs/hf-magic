package happyfish.actModule.giftGetAct.commond 
{
	import flash.events.Event;
	import happyfish.actModule.giftGetAct.manager.GiftDomain;
	import happymagic.model.command.BaseDataCommand;
	/**
	 * ...
	 * @author ZC
	 */
	//满足好友请求接口
	public class FriendRequestCommand extends BaseDataCommand
	{
		
		public function FriendRequestCommand() 
		{
			
		}

		public function setData(_giftrequestid:String,_giftid:String ):void 
		{
			createLoad();
			createRequest(GiftDomain.getInstance().getInterfaceUrl("FriendRequest"), {giftRequestId:_giftrequestid,giftId:_giftid } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
			
		}		
	}

}