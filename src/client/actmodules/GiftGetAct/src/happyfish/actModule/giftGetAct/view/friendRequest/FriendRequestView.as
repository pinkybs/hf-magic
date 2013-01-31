package happyfish.actModule.giftGetAct.view.friendRequest 
{
	import flash.events.MouseEvent;
	import happyfish.actModule.giftGetAct.event.GiftGetActEvent;
	import happyfish.actModule.giftGetAct.manager.GiftDomain;
	import happyfish.actModule.giftGetAct.view.giftGetAct.GiftGetActListView;
	import happyfish.manager.EventManager;
	/**
	 * ...
	 * @author ZC
	 */
	public class FriendRequestView extends FriendRequestViewUi
	{
		private var list:GiftGetActListView;
		public function FriendRequestView() 
		{
			addEventListener(MouseEvent.CLICK, clickrun);

			this.wordage.visible = false;
            EventManager.getInstance().addEventListener(GiftGetActEvent.SATISFYFRIENDREQUEST, satisfyFriendRequestComplete);	
			
		}
		
		private function satisfyFriendRequestComplete(e:GiftGetActEvent):void 
		{
			list.initPage();
		}
		
		public function init():void
		{
			if (GiftDomain.getInstance().getVar("giftRequests").length > 0)
			{
				list = new GiftGetActListView(new GiftGetListUi(), this, 3, false, false);
				list.init(580, 300, 560, 95, -210, -297);
				list.setGridItem(FriendRequestItemView, FriendRequestItemViewUi);
				list.x = 230;
				list.y = 317;				
			    list.setData(GiftDomain.getInstance().getVar("giftRequests"));				
			}
			else
			{
			    this.wordage.visible = true;				
			}

		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
			{
				case "send":

				break;
			}
		}
	}

}