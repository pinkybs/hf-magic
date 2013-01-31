package  happyfish.actModule.giftGetAct.view.giftGetAct
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.actModule.giftGetAct.commond.GiftGetKnowNewGiftCommand;
	import happyfish.actModule.giftGetAct.event.GiftGetActEvent;
	import happyfish.actModule.giftGetAct.manager.GiftDomain;
	import happyfish.actModule.giftGetAct.model.vo.GiftUserVo;
	import happyfish.actModule.giftGetAct.view.freeSend.FreeSendView;
	import happyfish.actModule.giftGetAct.view.friendRequest.FriendRequestView;
	import happyfish.actModule.giftGetAct.view.myWish.MyWishView;
	import happyfish.actModule.giftGetAct.view.receiveGift.ReceiveGiftView;
	import happyfish.display.ui.TabelView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	/**
	 * ...
	 * @author ZC
	 */
	//礼物模块的主界面
	public class GiftGetActView extends UISprite
	{
		public static const FREE_SEND:uint = 0;//免费赠送
		public static const FECEIVE_GIFT:uint = 1;//收到礼物
		public static const FRIEND_REQUEST:uint = 2;//好友的请求礼物
		public static const MY_WISH:uint = 3;//我的愿望
		
		private var selectstate:uint;
		private var iview:GiftGetActViewUI;
		private var topTab:TabelView;
		
        private var freeSendSprite:FreeSendView;
		private var friendRequestSprite:FriendRequestView;
		private var receiveGiftSprite:ReceiveGiftView;
		private var myWishSprite:MyWishView;
		
		public function GiftGetActView() 
		{
			_view = new GiftGetActViewUI();
			iview = _view as GiftGetActViewUI;
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			
			topTab = new TabelView();
			topTab.btwX = 10;
			topTab.x = -105;
			topTab.y = -157;
			topTab.setTabs([iview.sendgift, 1] , [iview.ReceiveGift, 2], 
			[iview.friendReceive, 3], [iview.MyWish, 4]);
			
			iview.addChild(topTab);
			topTab.addEventListener(Event.SELECT, tab_select)
			EventManager.getInstance().addEventListener(GiftGetActEvent.RECEIVEGIFTLOOPBACK, receivegiftloopback);
			EventManager.getInstance().addEventListener(GiftGetActEvent.RECEIVEGIFTLOOPBACKCOMPLETE, receivegiftloopbackcomplete);	
			
			iview.numbertips.visible = false;
		}
		
		private function receivegiftloopbackcomplete(e:GiftGetActEvent):void 
		{
			topTab.select(0);
		}
		
		private function receivegiftloopback(e:GiftGetActEvent):void 
		{
			        clear();			
				    freeSendSprite = new FreeSendView();
				    freeSendSprite.init();
					freeSendSprite.x = -265;
					freeSendSprite.y = -128;
					iview.addChild(freeSendSprite);
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
			{
				case "closebtn":
				EventManager.getInstance().dispatchEvent(new GiftGetActEvent(GiftGetActEvent.CLOSE));
				closeMe(true);
				break;
				
				case "send":
				
				break;
			}
		}
		
		private function tab_select(e:Event):void 
		{
			clear();
			var loopback:Boolean = false;
			GiftDomain.getInstance().setVar("loopbackboolean", loopback);
			
			switch((e.target as TabelView).selectIndex)
			{
				case FREE_SEND:
				    freeSendSprite = new FreeSendView();
				    freeSendSprite.init();
					freeSendSprite.x = -265;
					freeSendSprite.y = -128;
					iview.addChild(freeSendSprite);
				break;
				
				case FECEIVE_GIFT:
				    receiveGiftSprite = new ReceiveGiftView();
				    receiveGiftSprite.init();
					receiveGiftSprite.x = -265;
					receiveGiftSprite.y = -128;
					iview.addChild(receiveGiftSprite);
					
					if (GiftDomain.getInstance().getVar("IsNewGift"))
					{
					  topTab.removeEventListener(Event.SELECT, tab_select);
                      var giftGetKnowNewGiftCommand:GiftGetKnowNewGiftCommand = new GiftGetKnowNewGiftCommand();
					  giftGetKnowNewGiftCommand.setData();
					  giftGetKnowNewGiftCommand.addEventListener(Event.COMPLETE,giftGetKnowNewGiftCommandComplete)
					}
					else
					{
						
					}			
				break;
				
				case FRIEND_REQUEST:				
				    friendRequestSprite = new FriendRequestView();
				    friendRequestSprite.init();
					friendRequestSprite.x = -265;
					friendRequestSprite.y = -128;
					iview.addChild(friendRequestSprite);			
			
				break;
				
				case MY_WISH:
					myWishSprite = new MyWishView();
					myWishSprite.init();
					myWishSprite.x = -265;
					myWishSprite.y = -128;
					iview.addChild(myWishSprite);
				break
			}
		}
		
		private function giftGetKnowNewGiftCommandComplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, giftGetKnowNewGiftCommandComplete);
			
			topTab.addEventListener(Event.SELECT, tab_select);
			
			GiftDomain.getInstance().setVar("IsNewGift", false);	
			EventManager.getInstance().dispatchEvent(new GiftGetActEvent(GiftGetActEvent.CLOSE_NUMBERSHOW));			
		}
		
		public function setData(state:uint):void
		{
			selectstate = state;
			
			 if (GiftDomain.getInstance().getVar("IsNewGift"))
			 {
				 iview.numbertips.visible = true;
				 var giftuservo:GiftUserVo = GiftDomain.getInstance().getVar("giftUserVo");
				 iview.numbertips["num"].text = String(giftuservo.giftNum);
			 }
			 else
			 {
				 iview.numbertips.visible = false;				 
			 }				
			//选择初始框TAB
			topTab.select(selectstate);
		}
		
		//清除
		private function clear():void
		{
			if (freeSendSprite)
			{
				if (iview.contains(freeSendSprite))
				{
					iview.removeChild(freeSendSprite)
					
				}
			}
			
			if (friendRequestSprite)
			{
				if (iview.contains(friendRequestSprite))
				{
					iview.removeChild(friendRequestSprite);
				}
			}
			
			if (receiveGiftSprite)
			{
				if (iview.contains(receiveGiftSprite))
				{
					iview.removeChild(receiveGiftSprite);
					
				}
			}
			
			if (myWishSprite)
			{
				if (iview.contains(myWishSprite))
				{
					iview.removeChild(myWishSprite);
				}
			}
			freeSendSprite = null;
			receiveGiftSprite = null;
			myWishSprite = null;
			friendRequestSprite = null;
			
		}
	}

}