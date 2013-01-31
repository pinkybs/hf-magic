package happyfish.actModule.giftGetAct.view.current 
{
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.actModule.giftGetAct.commond.FriendRequestCommand;
	import happyfish.actModule.giftGetAct.event.GiftGetActEvent;
	import happyfish.actModule.giftGetAct.manager.GiftDomain;
	import happyfish.actModule.giftGetAct.model.vo.GiftVo;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.IconView;
	import happyfish.manager.EventManager;
	import happyfish.utils.display.BtnStateControl;
	/**
	 * ...
	 * @author ZC
	 */
	public class CurrentItemView extends GridItem
	{
		public static const ITEM:int = 1;
		public static const DECTOR:int = 2;
		public static const MYWISH:int = 3;
		public static const FRIENDREQUEST:int = 4;
		public var iview:CurrentItemViewUi;
		public var type:uint;
		public var giftid:String;
		public var giftrequestid:String; //好友请求的数据ID
		public var frienduid:String;//好友的uid
		public var itemclassname:String;
		public var itemname:String;
		private var state:uint;//状态值
		public var hasGet:Boolean;//是否已经送过
		private var requestList:Array;
		public function CurrentItemView(_uview:MovieClip,_state:uint)		
		{
			super(_uview);
			iview = _uview as CurrentItemViewUi;
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			iview.Mywish.visible = false;
			iview.send.visible = false;
			iview.mouseChildren = true;
			iview.yesbtn.visible = false;
			iview.whitebackground.visible = false;
			iview.lock.visible = false;
			state = _state;
            BtnStateControl.setBtnState(iview.Mywish, true);
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
			{
				case "Mywish":

				break;
				
				case "send":
			           iview.removeEventListener(MouseEvent.CLICK, clickrun);
				       
				       var contentFriendRequestCommand:FriendRequestCommand = new FriendRequestCommand();
					   contentFriendRequestCommand.setData(giftrequestid, giftid);
					   contentFriendRequestCommand.addEventListener(Event.COMPLETE, contentFriendRequestComplete);
				break;
			}
		}
		
		private function contentFriendRequestComplete(e:Event):void 
		{  
			iview.addEventListener(MouseEvent.CLICK, clickrun);	
			e.target.removeEventListener(Event.COMPLETE, contentFriendRequestComplete);			
			if (e.target.data.result.status == -1)
			{
				return;
		    }
			
			
			var str:String = "你已经满足了" + GiftDomain.getInstance().getFriendUserVo(frienduid).name + "的愿望";
			GiftDomain.getInstance().showSysMsg(str);			
			GiftDomain.getInstance().setgiftRequestVoArr(giftrequestid);
			EventManager.getInstance().dispatchEvent(new GiftGetActEvent(GiftGetActEvent.SATISFYFRIENDREQUEST));
		}
		
		//state:4 好友的请求 3 我的愿望
		//data 格式[type,id,frienduid]
		override public function setData(vaule:Object):void
		{
			if (vaule is GiftVo)
			{
				type = (vaule as GiftVo).type;
				giftid = (vaule as GiftVo).id;
				itemclassname = (vaule as GiftVo).className;
				itemname = (vaule as GiftVo).name;
			}			
			else if (vaule is Array)
			{
			   type = vaule[1];
			   giftid = vaule[0];
			   itemname = vaule[2];
			   itemclassname = vaule[3];
			   giftrequestid = vaule[4];
			   hasGet = vaule[5];
			   frienduid = vaule[6];
			   requestList = vaule as Array;
			}
			
			switch(state)
			{
				case MYWISH:
				
				var giftvo:GiftVo = GiftDomain.getInstance().getGiftVo(giftid);
				
				if (GiftDomain.getInstance().currentUser.level >= giftvo.lockLevel)
				{
					iview.whitebackground.visible = true;
					iview.Mywish.visible = true;
					var temp:Array = GiftDomain.getInstance().getVar("MyWishArray");				
					for (var i:int = 0; i < temp.length; i++ )
					{
						if (temp[i].id == giftid)
						{
							iview.yesbtn.visible = true;
							BtnStateControl.setBtnState(iview.Mywish, false);
						}
					}					
				}
				else
				{
					iview.lock.visible = true;
					iview.whitebackground.visible = true;
				    iview.lock["levelnum"].text = giftvo.lockLevel;
					iview.removeEventListener(MouseEvent.CLICK, clickrun);
				}

				break;
				
				case FRIENDREQUEST:
				
				iview.send.visible = true;	

					
				break;
			}
			
			loadicon();
		}
		
		private function loadicon():void 
		{
			var icon:IconView = new IconView(50, 50, new Rectangle(13, 10, 50, 50));
			icon.setData(itemclassname);
			iview.addChildAt(icon, iview.getChildIndex(iview.yesbtn));
			GiftDomain.getInstance().showTips(icon,itemname);
			
			if (requestList)
			{
				if (!hasGet)
				{
					BtnStateControl.setBtnState(iview.send, false);
				}
				else
				{
					BtnStateControl.setBtnState(iview.send, true);				
				}				
			}

		}
				
	}

}