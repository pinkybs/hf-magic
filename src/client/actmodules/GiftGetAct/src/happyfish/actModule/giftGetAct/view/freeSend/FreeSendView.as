package happyfish.actModule.giftGetAct.view.freeSend 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.actModule.giftGetAct.commond.SendGiftCommand;
	import happyfish.actModule.giftGetAct.event.GiftGetActEvent;
	import happyfish.actModule.giftGetAct.GiftGetActDict;
	import happyfish.actModule.giftGetAct.manager.GiftDomain;
	import happyfish.actModule.giftGetAct.model.vo.GiftVo;
	import happyfish.actModule.giftGetAct.view.giftGetAct.GiftGetActListView;
	import happyfish.actModule.giftGetAct.view.selectFriend.SelectFriendView;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.vo.ModuleVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class FreeSendView extends FreeSendViewUi
	{
		private var list:GiftGetActListView;
		private var sendItemdata:GiftVo;
		public function FreeSendView() 
		{
			addEventListener(MouseEvent.CLICK, clickrun);
			list = new GiftGetActListView(new GiftGetListUi(), this, 10,false,false);
			list.init(570,250,110,120,-215,-240);
			list.x = 221;
			list.y = 270;
			list.setGridItem(FreeSendItemView, FreeSendItemUi);
			list.iview.addEventListener(MouseEvent.CLICK, listClickFun);
			
			sendItemdata = new GiftVo();
			sendItemdata.id = "0";
			sendItemdata.type = 0;
			sendItemdata.name = "";
			sendItemdata.className = "";
			
			GiftDomain.getInstance().setVar("senditemid", sendItemdata);
		}
		
		private function listClickFun(e:MouseEvent):void 
		{
			if (e.target as FreeSendItemUi)
			{
				sendItemdata.id = (e.target.control as FreeSendItemView).data.id;
				sendItemdata.type = (e.target.control as FreeSendItemView).data.type;
				sendItemdata.name = (e.target.control as FreeSendItemView).data.name;
				sendItemdata.className = (e.target.control as FreeSendItemView).data.className;
				GiftDomain.getInstance().setVar("senditemid", sendItemdata);
			}
		}
		
		public function init():void
		{
			var temp:Array = GiftDomain.getInstance().getVar("gifts");
			list.setData(GiftDomain.getInstance().getVar("gifts"));
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
			{
				case "send":
				   if(GiftDomain.getInstance().getVar("giftFriendUser").length == 0)
				   {
					    GiftDomain.getInstance().showPiaoStr(LocaleWords.getInstance().getWord("nofriend"));
				   }
				   else if (GiftDomain.getInstance().getVar("loopbackboolean"))
				   {
			           removeEventListener(MouseEvent.CLICK, clickrun);	
					   
					   var loopbackfriendArray:Array = new Array();
					   loopbackfriendArray.push(GiftDomain.getInstance().getVar("loopbackfrienduid"));	
				   
					   var sendGiftCommand:SendGiftCommand = new SendGiftCommand();
					   sendGiftCommand.setData(GiftDomain.getInstance().getVar("loopbackgiftuid"),loopbackfriendArray);
					   sendGiftCommand.addEventListener(Event.COMPLETE, sendGiftCommandcomplete);
				   }
				   else
				   {
				  	  if (sendItemdata.id != "0" && sendItemdata.type != 0)
				  	  {
			        	  var modlueVo:ModuleVo = new ModuleVo();
			         	  modlueVo.name = GiftGetActDict.ACTDICT_SELECTFRIEND;
			        	  modlueVo.className = GiftGetActDict.ACTDICT_SELECTFRIEND_CLASS;
			        	  modlueVo.algin = "center";
			         	  modlueVo.mvTime = 0.5;
			         	  modlueVo.mvType = "fromCenter";
			         	  modlueVo.single = false;
					 
				     	  var selectfriendview:SelectFriendView = GiftDomain.getInstance().addModule(modlueVo) as SelectFriendView;
					 	  var temp:Array = new Array();
					 	  temp.push(sendItemdata);
				     	  selectfriendview.setData(SelectFriendView.SENDGIFT, temp);
					 	  GiftDomain.getInstance().setBg(selectfriendview);
				  	  }
				  	  else
				 	  {
					  	  GiftDomain.getInstance().showPiaoStr(LocaleWords.getInstance().getWord("selectitem"));	
				  	  }					  
				   }
				  
				break;
				
			}
		}
		
		private function sendGiftCommandcomplete(e:Event):void 
		{
			
			addEventListener(MouseEvent.CLICK, clickrun);
			e.target.removeEventListener(Event.COMPLETE, sendGiftCommandcomplete);
			
			var i:int = 0;
			var j:int = 0;
			var str:String = "你已经向这位名叫";
			var dataManagerFriend:Array = GiftDomain.getInstance().getVar("giftFriendUser");
			str += GiftDomain.getInstance().getFriendUserVo(GiftDomain.getInstance().getVar("loopbackfrienduid")).name ;
            str += "的好友回赠了一样礼物";				   

			GiftDomain.getInstance().showSysMsg(str);
				   
		    for (i = 0; i < dataManagerFriend.length; i++ )
			{
			   if (dataManagerFriend[i].uid == GiftDomain.getInstance().getVar("loopbackfrienduid"))
			   {
				  dataManagerFriend[i].giftAble = false;							   
			   }
		    }
				   
		    GiftDomain.getInstance().friends = dataManagerFriend;
		
			if (GiftDomain.getInstance().getVar("loopbackboolean"))
			{
				var test:Boolean = false;
			    GiftDomain.getInstance().setVar("loopbackboolean", test);
				
				EventManager.getInstance().dispatchEvent(new GiftGetActEvent(GiftGetActEvent.RECEIVEGIFTLOOPBACKCOMPLETE));
			}

		}
	}

}