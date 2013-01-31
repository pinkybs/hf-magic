package happyfish.actModule.giftGetAct.view.selectFriend 
{
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.actModule.giftGetAct.commond.ReleaseMyWishCommond;
	import happyfish.actModule.giftGetAct.commond.SendGiftCommand;
	import happyfish.actModule.giftGetAct.event.GiftGetActEvent;
	import happyfish.actModule.giftGetAct.manager.GiftDomain;
	import happyfish.actModule.giftGetAct.model.vo.GiftFriendUserVo;
	import happyfish.actModule.giftGetAct.model.vo.GiftUserVo;
	import happyfish.actModule.giftGetAct.model.vo.GiftMyWishVo;
	import happyfish.actModule.giftGetAct.view.current.CurrentItemView;
	import happyfish.actModule.giftGetAct.view.giftGetAct.GiftGetActListView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.utils.display.BtnStateControl;
	/**
	 * ...
	 * @author ZC
	 */
	//选择送给哪个的好友界面
	public class SelectFriendView extends UISprite
	{
		public static const SENDGIFT:int = 0;//赠送礼物
		public static const MYWISH:int = 1;//我的愿望
		private var friendarr:Array;
		private var itemarr:Array;
		private var selectState:uint;
		private var iview:SelectFriendUI;
		private var itemlist:GiftGetActListView;
		private var friendlist:GiftGetActListView;
		private var selectlist:Array;
		private var giftid:Array;//请求时候用的礼物列表ID
		
		public function SelectFriendView() 
		{
			_view = new SelectFriendUI();
		    iview = _view as SelectFriendUI;
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			iview.unselect.visible = false;
			
			selectlist = new Array();
			
			GiftDomain.getInstance().setVar("selectlist", selectlist);
			
			itemlist = new GiftGetActListView(new GiftGetListUi(), iview, 3, true, false);
			itemlist.iview["pageNumTxt"].visible = false;
			itemlist.init(120, 360, 110, 110, -200, -175);
			itemlist.setGridItem(SelectFriendGiftItemView, FreeSendItemUi);
			
			
			
			friendlist = new GiftGetActListView(new GiftGetListUi(), iview, 16, false, false);
			friendlist.tweenTime = 0;
			friendlist.init(270, 270, 130, 25, -40, -257);
			friendlist.x = -20;
			friendlist.y = 125;
			friendlist.setGridItem(SelectFriendListItemView, SelectFriendListItemViewUi);


			
		    iview.sendgift.visible = false;
			iview.sendbtn.visible = false;
			iview.sendrequest.visible = false;
			iview.sendrequestbtn.visible = false;
		
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			var temp:Array;
			var i:int;
			switch(e.target.name)
		    {
				case "closebtn":
				   restoreData(itemarr);
				   closeMe(true);
				   
				break;
				
				case "sendbtn":
				   if(!GiftDomain.getInstance().isFullSendGift())
				   {
					   GiftDomain.getInstance().showPiaoStr(LocaleWords.getInstance().getWord("FullSendGift"));						   
				   }
				   
				   else if (GiftDomain.getInstance().getVar("selectlist").length == 0)
				   {
					   GiftDomain.getInstance().showPiaoStr(LocaleWords.getInstance().getWord("selectfriend"));			   
				   }
				   
				   else
				   {
					   iview.removeEventListener(MouseEvent.CLICK, clickrun);
					   
					   var sendGiftCommand:SendGiftCommand = new SendGiftCommand();
					   sendGiftCommand.setData(itemarr[0].id, GiftDomain.getInstance().getVar("selectlist"));
					   sendGiftCommand.addEventListener(Event.COMPLETE, sendGiftCommandcomplete);
				   }
				   
				   
				break;
				
				case "select":
				   iview.select.visible = false;
				   iview.unselect.visible = true;
				
				   temp = new Array();			    
				   var friend:Array;
				   friend = GiftDomain.getInstance().friends;
			       for (i= 0; i < GiftDomain.getInstance().friends.length; i++ )
			       {
					   temp.push((friend[i] as GiftFriendUserVo).uid);
			       }
				
				   GiftDomain.getInstance().setVar("selectlist", temp);
				
				   friendlist.initPage();
				
				break;
				
				case "unselect":
				   iview.select.visible = true;;
				   iview.unselect.visible = false;
				
				   temp = new Array();
				   GiftDomain.getInstance().setVar("selectlist", temp);
				   friendlist.initPage();
				
				break
				
				case "sendrequestbtn":
				   //if (GiftDomain.getInstance().getVar("selectlist").length == 0)
				   //{
					   //GiftDomain.getInstance().showPiaoStr(LocaleWords.getInstance().getWord("selectfriend"));
				   //}
				   //else if (GiftDomain.getInstance().getVar("giftUserVo").isReleaseWish)
				   //{
				      //giftid= new Array();
				      //for (i = 0; i < itemarr.length; i++ )
				      //{
					   //giftid.push(itemarr[i].id);
				      //}
				      //var releaseMyWishCommond:ReleaseMyWishCommond = new ReleaseMyWishCommond();
				      //releaseMyWishCommond.setData(giftid, GiftDomain.getInstance().getVar("selectlist"));	
					  //releaseMyWishCommond.addEventListener(Event.COMPLETE, releasemywishcommondcomplete);
				   //}
				   //else
				   //{
 					   //GiftDomain.getInstance().showPiaoStr(LocaleWords.getInstance().getWord("mywish"));                           
				   //}

				break;
			}
		}
		
		private function sendGiftCommandcomplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, sendGiftCommandcomplete);
		    iview.addEventListener(MouseEvent.CLICK, clickrun);
			var i :int = 0;
			var j:int = 0;
			if (e.target.data.result.isSuccess)
			{
			       var str:String = "你已经向";
				   var friendIdList:Array = GiftDomain.getInstance().getVar("selectlist");
				   var dataManagerFriend:Array = GiftDomain.getInstance().friends;
				   
				   for (i = 0; i < friendIdList.length; i++)
				   {
					   str += GiftDomain.getInstance().getFriendUserVo(friendIdList[i]).name + ",";
					   if (i == 4)
					   {
						   
						   break;
					   }
				   }
				   str += "等" + friendIdList.length + "位好友赠送了" + itemarr[0].name;				   

				   GiftDomain.getInstance().showSysMsg(str);
				   
				   for (i = 0; i < dataManagerFriend.length; i++ )
				   {
					   for (j = 0; j < friendIdList.length; j++ )
					   {
						   if (dataManagerFriend[i].uid == friendIdList[j])
						   {
					            dataManagerFriend[i].giftAble = false;							   
						   }
					   }
				   }
				   
				   GiftDomain.getInstance().friends = dataManagerFriend;
				   
				friendlist.setData(deletefriend(SENDGIFT));
				
				var newarr:Array = new Array();
				GiftDomain.getInstance().setVar("selectlist", newarr);
			}
		}
		
		//private function releasemywishcommondcomplete(e:Event):void 
		//{
			//var i :int = 0;
			//var j:int = 0;
			//if (e.target.data.result.isSuccess)
			//{
				//GiftDomain.getInstance().currentUser.giftRequestAble = false;
				//
			    //var str:String = "你已经向";
				//var friendIdList:Array = GiftDomain.getInstance().getVar("selectlist");
				//var dataManagerFriend:Array = GiftDomain.getInstance().friends;
				//
				//for (i = 0; i < friendIdList.length; i++)
				//{
				   //str += GiftDomain.getInstance().getFriendUserVo(friendIdList[i]).name + ",";
				   //if (i == 4)
				   //{
					  //break;
				   //}
				//}
				//
				//str += "等" + friendIdList.length + "位好友发布了我的愿望";
				//GiftDomain.getInstance().showSysMsg(str);
				   //
				   //for (i = 0; i < dataManagerFriend.length; i++ )
				   //{
					   //for (j = 0; j < friendIdList.length; j++ )
					   //{
						   //if (dataManagerFriend[i].uid == friendIdList[j])
						   //{
					            //dataManagerFriend[i].giftRequestAble = false;							   
						   //}
					   //}
				   //}
				//GiftDomain.getInstance().friends = dataManagerFriend;
				//将数据都恢复初始值
				//initData();
				//
				//(GiftDomain.getInstance().getVar("giftUserVo") as GiftUserVo).isReleaseWish = false;
			//}
				//
			//
		//}
		
		//设置数据跟状态
		//_state：SENDGIFT 代表赠送按钮后所弹出来的选择好友界面
		//_state：MYWISH   代表发布我的愿望按钮后所弹出来的选择好友界面
		public function setData(_state:uint,_id:Array):void
		{
			selectState = _state;
		    itemarr = _id;
			switch(selectState)
		    {
				case SENDGIFT:
				iview.sendgift.visible = true;
				iview.sendbtn.visible = true
				friendarr = deletefriend(SENDGIFT);
				break;
				
				//case MYWISH:
				//iview.sendrequest.visible = true;
				//iview.sendrequestbtn.visible = true;
				//deletedata(itemarr);
				//friendarr = deletefriend(MYWISH);
				//break;
			}
			
			initset();
		}
		
		//初始化列表数据
		private function initset():void
		{
			friendlist.setData(friendarr);

			itemlist.setData(itemarr);
			
			//if (GiftDomain.getInstance().getVar("giftUserVo").isReleaseWish)
			//{
				//BtnStateControl.setBtnState();
			//}
			//else
			//{
				//BtnStateControl.setBtnState();
			//}
		}
		
		//参数是一个UID 
		//作用查找数据里是否存在这个数据ID，存在就消除 不存在就添加
		public static function Update(_uid:String):void
		{
			var temp:Array = GiftDomain.getInstance().getVar("selectlist");
			if (temp.length == 0)
			{
				temp.push(_uid);
				GiftDomain.getInstance().setVar("selectlist", temp);
				return;
			}
			for (var i :int = 0; i < temp.length; i++ )
			{
				if (temp[i] == _uid)
				{
					
					temp.splice(i, 1);
					GiftDomain.getInstance().setVar("selectlist",temp);
					return;
					
				}		
			}
			
			temp.push(_uid);		
		}
		
		//处理掉为0的数据
		//private function deletedata(arr:Array):void
		//{
			//for (var i:int = 0; i < arr.length; i++ )
			//{
				//if ((arr[i] as GiftMyWishVo).id == "0")
				//{
					//arr.splice(i, arr.length-1);
				//}
			//}
		//}
		
		//从好友列表里处理掉已经发送过请求的好友
		//state 0表示处理掉已经赠送过的好友
		//      1表示处理掉已经发布过愿望的好友
		private function deletefriend(_state:uint):Array
		{
			var friendArray:Array = GiftDomain.getInstance().getVar("giftFriendUser");
			var i:int;
			var friendArrayTemp:Array = new Array();
			
			switch(_state)
			{
				case SENDGIFT:
				
			                for (i = 0; i < friendArray.length; i++ )
			                {
				                if (friendArray[i].giftAble)
								{
									friendArrayTemp.push(friendArray[i]);
								}
			                }
				break;
				
				case MYWISH:
			                for (i = 0; i < friendArray.length;i++)
			                {
				                if (friendArray[i].giftRequestAble)
								{
									friendArrayTemp.push(friendArray[i]);
								}
			                }				
				break;
				
			}
            friendarr  = friendArrayTemp;
	        return friendArrayTemp;
		}
		
		//还原被屏蔽的数据
		//TODO
		private function restoreData(arr:Array):void
		{
			
			//var mywish:Array = new Array();			
			//var temparr:Array = GiftDomain.getInstance().getVar("giftMyWishVoArray");			
			//for (var j:int = 0; j < temparr.length; i++ )
			//{
				//mywish.push(temparr[i]);
			//}

		    //GiftDomain.getInstance().setVar("MyWishArray", mywish);				
			
           while (arr.length < 3)
		   {
			   var mywishdata:GiftMyWishVo = new GiftMyWishVo();
			   mywishdata.id = "0";
			   mywishdata.type = 0;			   
			   arr.push(mywishdata);
		   }
		   GiftDomain.getInstance().setVar("MyWishArray", arr);	
		}
		
		//初始化临时保存的数据列表 MyWishData
		//private function initData():void
		//{
			//var temp:Array = new Array();
			//for (var i :int = 0; i < 3; i++ )
			//{
			   //var mywishdata:GiftMyWishVo = new GiftMyWishVo();
			   //mywishdata.id = "0";
			   //mywishdata.type = 0;			   
			   //temp.push(mywishdata);				
			//}
			//GiftDomain.getInstance().setVar("MyWishArray", temp);
		//}
		
	}

}