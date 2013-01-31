package happyfish.actModule.giftGetAct.view.myWish 
{
	import flash.display.SimpleButton;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.actModule.giftGetAct.commond.ReleaseMyWishCommond;
	import happyfish.actModule.giftGetAct.GiftGetActDict;
	import happyfish.actModule.giftGetAct.manager.GiftDomain;
	import happyfish.actModule.giftGetAct.model.vo.GiftUserVo;
	import happyfish.actModule.giftGetAct.model.vo.GiftMyWishVo;
	import happyfish.actModule.giftGetAct.view.current.CurrentItemView;
	import happyfish.actModule.giftGetAct.view.current.CurrentListView;
	import happyfish.actModule.giftGetAct.view.giftGetAct.GiftGetActListView;
	import happyfish.actModule.giftGetAct.view.selectFriend.SelectFriendView;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.vo.ModuleVo;
	import happyfish.utils.display.BtnStateControl;
	/**
	 * ...
	 * @author ZC
	 */
	//我的愿望界面
	public class MyWishView extends MyWishViewUi
	{
		private var mywishlist:GiftGetActListView;
		private var itemlist:CurrentListView;
		private var mywish:Array;
		public function MyWishView() 
		{
			addEventListener(MouseEvent.CLICK, clickrun);
			mywishlist = new GiftGetActListView(new GiftGetListUi(), this, 3, true, false);
			mywishlist.init(285, 85, 88, 80, 55, 33);
			mywishlist.setGridItem(MyWishItemView, MyWishItemViewUi);
			mywishlist.iview.addEventListener(MouseEvent.CLICK, mywishlistclick);
			mywishlist.iview["pageNumTxt"].visible = false;
			mywishlist.x = 0;
			mywishlist.y = 0;
			mywishlist.tweenTime = 0;
			
			mywish = new Array();
			
			
			//for (var i:int = 0; i < 3; i++)
			//{
				//var mywishdata:GiftMyWishVo = new GiftMyWishVo();
				//mywishdata.id = "0";
				//mywishdata.type = 0;
				//mywishdata.className = "";
				//mywishdata.name = "";
				//mywish.push(mywishdata);
			//}
			
			var temparr:Array = GiftDomain.getInstance().getVar("giftMyWishVoArray");
			
			for (var j:int = 0; j < temparr.length; j++ )
			{
				mywish.push(temparr[j]);
			}
			for (var i:int = 0; i < mywish.length; i++)
			{
                  mywish[i].className = GiftDomain.getInstance().getGiftDiaryVoClassName(mywish[i].type, mywish[i].id);
                  mywish[i].name = GiftDomain.getInstance().getGiftDiaryVoName(mywish[i].type, mywish[i].id);			  
			}
			
			GiftDomain.getInstance().setVar("MyWishArray", mywish);
			
			itemlist = new CurrentListView(new GiftGetListUi(), this, 10, false, false,CurrentItemView.MYWISH);
			itemlist.init(500, 200, 90, 90, -180, -185);
			itemlist.setGridItem(CurrentItemView, CurrentItemViewUi);
			itemlist.iview.addEventListener(MouseEvent.CLICK, itemlistclick);
			itemlist.x = 230;
			itemlist.y = 317;
			itemlist.tweenTime = 0;
			
		}
		
		private function itemlistclick(e:MouseEvent):void 
		{
		    if (e.target is SimpleButton)
			{ 
				if (e.target.name == "Mywish")
				{
				    var temp:Array = GiftDomain.getInstance().getVar("MyWishArray");
					update(temp,e);
				
				}

			}			
		}
		
		private function mywishlistclick(e:MouseEvent):void 
		{
		    if (e.target is SimpleButton)
			{
				if (e.target.name == "DeleteBtn")
				{
					var temp:Array = GiftDomain.getInstance().getVar("MyWishArray");
					deletedate(temp, e.target.parent.control.data.id);
				    itemlist.initPage();	
					mywishlist.initPage();	
				}
			
			}
		}
		
		public function init():void
		{
			mywishlist.setData(GiftDomain.getInstance().getVar("MyWishArray"));
			itemlist.setData(GiftDomain.getInstance().getVar("gifts"));
			
			if (GiftDomain.getInstance().getVar("giftUserVo").isReleaseWish)
			{
				BtnStateControl.setBtnState(this.releaseMyWish, true);
			}
			else
			{
				BtnStateControl.setBtnState(this.releaseMyWish, false);				
			}
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			var i :int = 0;
			switch(e.target.name)
			{
				case "releaseMyWish":
				
				     if (isfull(GiftDomain.getInstance().getVar("MyWishArray")))
				     {
						 if(GiftDomain.getInstance().getVar("giftFriendUser").length == 0)
						 {
							GiftDomain.getInstance().showPiaoStr(LocaleWords.getInstance().getWord("nofriend"));
							return;
						 }						 
						  //var modlueVo:ModuleVo = new ModuleVo();
			              //modlueVo.name = GiftGetActDict.ACTDICT_SELECTFRIEND;
			              //modlueVo.className = GiftGetActDict.ACTDICT_SELECTFRIEND_CLASS;
			              //modlueVo.algin = "center";
			              //modlueVo.mvTime = 0.5;
			              //modlueVo.mvType = "fromCenter";
			              //modlueVo.single = false; 
				          //var selectfriendview:SelectFriendView = GiftDomain.getInstance().addModule(modlueVo) as SelectFriendView;
				          //selectfriendview.setData(SelectFriendView.MYWISH, GiftDomain.getInstance().getVar("MyWishArray"));	
					      //GiftDomain.getInstance().setBg(selectfriendview);
                          var mywisharray:Array = GiftDomain.getInstance().getVar("MyWishArray");
						  
						  var giftid:Array = new Array();
						  
				          for (i = 0; i < mywisharray.length; i++ )
				          {
					          giftid.push(mywisharray[i].id);
				          }
						  
			              removeEventListener(MouseEvent.CLICK, clickrun);						  
						  var releaseMyWishCommond:ReleaseMyWishCommond = new ReleaseMyWishCommond();
						  releaseMyWishCommond.setData(giftid);
						  releaseMyWishCommond.addEventListener(Event.COMPLETE, releaseMyWishCommondComplete);
				     }
				  else
				     {
					  	  GiftDomain.getInstance().showPiaoStr(LocaleWords.getInstance().getWord("selectitem"));
				     }							  
		
				break;
			}
		}
		
		private function releaseMyWishCommondComplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, releaseMyWishCommondComplete);
			addEventListener(MouseEvent.CLICK, clickrun);	
			
			var i :int = 0;
			var j:int = 0;
			if (e.target.data.result.isSuccess)
			{
				GiftDomain.getInstance().currentUser.giftRequestAble = false;
				
			    var str:String = "你已经向";
				var friendIdList:Array = GiftDomain.getInstance().getVar("giftFriendUser");
				var dataManagerFriend:Array = GiftDomain.getInstance().friends;
				
				for (i = 0; i < friendIdList.length; i++)
				{
				   str += friendIdList[i].name + ",";
				   if (i == 4)
				   {
					  break;
				   }
				}
				
				str += "等" + friendIdList.length + "位好友发布了我的愿望";
				GiftDomain.getInstance().showSysMsg(str);
				   
				   for (i = 0; i < dataManagerFriend.length; i++ )
				   {
					   for (j = 0; j < friendIdList.length; j++ )
					   {
						   if (dataManagerFriend[i].uid == friendIdList[j])
						   {
					            dataManagerFriend[i].giftRequestAble = false;							   
						   }
					   }
				   }
				GiftDomain.getInstance().friends = dataManagerFriend;
				
				(GiftDomain.getInstance().getVar("giftUserVo") as GiftUserVo).isReleaseWish = false;	
				init();
		}
		
		}
		
		
		//判断是否已满需要删除礼物
		private function judgeIsEnough(arr:Array):Boolean
		{
		    for (var i :int = 0; i < arr.length ; i++ )
			{
				if ((arr[i] as GiftMyWishVo).id == "0")
				{
					return false;
 				}
			}	
			return true;
		}
		
	    //数据更新 
		private function update(arr:Array,e:MouseEvent):void
		{
			if (judgeIsEnough(arr))
			{
				GiftDomain.getInstance().showPiaoStr(LocaleWords.getInstance().getWord("selectitemisfull"));
			}
			else
			{
		        for (var i :int = 0; i < arr.length ; i++ )
			    {
				   if ((arr[i] as GiftMyWishVo).id == "0")
				   {
					(arr[i] as GiftMyWishVo).id = e.target.parent.control.giftid;
					(arr[i] as GiftMyWishVo).type = e.target.parent.control.type;
					(arr[i] as GiftMyWishVo).name = e.target.parent.control.itemname;
					(arr[i] as GiftMyWishVo).className = e.target.parent.control.itemclassname;
					
					GiftDomain.getInstance().setVar("MyWishArray", arr);	
					e.target.parent.yesbtn.visible = true;
					BtnStateControl.setBtnState(e.target.parent.Mywish, false);
					mywishlist.initPage();
					return;
 				   }
			    }				
			}
		}
		
		//删除更新
		//arr 代表是数据 根据ID来删除
		private function deletedate(arr:Array,_id:String):void
		{
			for (var i :int = 0; i < arr.length; i++ )
			{
				if ((arr[i] as GiftMyWishVo).id == _id)
				{
					arr.splice(i, 1);
					var data:GiftMyWishVo = new GiftMyWishVo();
					data.id = "0";
					data.type = 0;
					data.name = "";
					data.className = "";
					arr.push(data);
				}
			}
			GiftDomain.getInstance().setVar("MyWishArray",arr);
		}
		
		//判断是否一个礼物都没选
		private function isfull(arr:Array):Boolean
		{
		    for (var i :int = 0; i < arr.length ; i++ )
			{
				if ((arr[i] as GiftMyWishVo).id != "0")
				{
					return true;
 				}
			}	
			return false;			
		}
		
	}

}