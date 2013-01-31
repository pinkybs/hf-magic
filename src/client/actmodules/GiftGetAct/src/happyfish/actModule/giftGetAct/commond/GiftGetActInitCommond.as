package happyfish.actModule.giftGetAct.commond 
{
	import flash.events.Event;
	import happyfish.actModule.giftGetAct.manager.GiftDomain;
	import happyfish.actModule.giftGetAct.model.vo.GiftDiaryVo;
	import happyfish.actModule.giftGetAct.model.vo.GiftFriendUserVo;
	import happyfish.actModule.giftGetAct.model.vo.GiftMyWishVo;
	import happyfish.actModule.giftGetAct.model.vo.GiftRequestVo;
	import happyfish.actModule.giftGetAct.model.vo.GiftUserVo;
	import happyfish.actModule.giftGetAct.model.vo.GiftVo;
	import happymagic.model.command.BaseDataCommand;
	/**
	 * ...
	 * @author ZC
	 */
	//请求数据类
	public class GiftGetActInitCommond extends BaseDataCommand
	{
		
		public function GiftGetActInitCommond() 
		{
			
		}
		
		public function setData():void 
		{
			createLoad();
			createRequest(GiftDomain.getInstance().getInterfaceUrl("GiftGetActInit"));
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			var temp:Array = new Array();
			var i:int = 0 ;
			
			if (objdata.giftDiarys)
			{
				//礼物日志数据
				for (i = 0; i < objdata.giftDiarys.length; i++) 
				{
					temp.push(new GiftDiaryVo().setData(objdata.giftDiarys[i]));
				}				
			}
			
			for (i = 0; i < temp.length; i++)
			{
				temp[i].name = GiftDomain.getInstance().getGiftDiaryVoName(temp[i].giftType,temp[i].giftCid);
				temp[i].className = GiftDomain.getInstance().getGiftDiaryVoClassName(temp[i].giftType, temp[i].giftCid);
			}
			
			GiftDomain.getInstance().setVar("giftDiarys", temp);

			temp = new Array();
			
			if (objdata.giftRequests)
			{
				//好友请求数据
				for (i = 0; i < objdata.giftRequests.length; i++) 
				{
					temp.push(new GiftRequestVo().setData(objdata.giftRequests[i]));
				}					
			}
			
			GiftDomain.getInstance().setVar("giftRequests", temp);
			
			var giftUserVo:GiftUserVo;
			
			if (objdata.giftUser)
			{
				//自己收到的礼物数量
				giftUserVo = new GiftUserVo();
				giftUserVo.setData(objdata.giftUser);
			}

			GiftDomain.getInstance().setVar("IsNewGift", giftUserVo.isNewGift);			
			
			GiftDomain.getInstance().setVar("giftUserVo", giftUserVo);
			
			temp = new Array();
			
			if (objdata.giftFriendUser)
			{
				//好友收到的个人信息
				for (i = 0; i < objdata.giftFriendUser.length; i++) 
				{
					temp.push(new GiftFriendUserVo().setData(objdata.giftFriendUser[i]));
				}	
			}
			
			GiftDomain.getInstance().setVar("giftFriendUser", temp);			

			temp = new Array();
			
			if (objdata.giftMyWish)
			{
			   //用户选择愿望的数据
				for (i = 0; i < objdata.giftMyWish.length; i++) 
				{
					temp.push(new GiftMyWishVo().setData(objdata.giftMyWish[i]));
				}					
			}
			
			GiftDomain.getInstance().setVar("giftMyWishVoArray", temp);			
			
			commandComplete();
		}
	}

}