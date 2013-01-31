package happyfish.actModule.giftGetAct.view.receiveGift 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.actModule.giftGetAct.commond.GiftGetActInitCommond;
	import happyfish.actModule.giftGetAct.commond.ReceiveGiftCommand;
	import happyfish.actModule.giftGetAct.event.GiftGetActEvent;
	import happyfish.actModule.giftGetAct.manager.GiftDomain;
	import happyfish.actModule.giftGetAct.model.vo.GiftUserVo;
	import happyfish.actModule.giftGetAct.view.giftGetAct.GiftGetActListView;
	import happyfish.display.ui.TabelView;
	import happyfish.manager.local.LocaleWords;
	
	/**
	 * ...
	 * @author ZC
	 */
	
	//收礼界面
	public class ReceiveGiftView extends ReceiveGiftViewUi
	{
		private var list:GiftGetActListView;
		private var giftdiaryId:Array;//传送给接口的数据
		public function ReceiveGiftView() 
		{
			addEventListener(MouseEvent.CLICK, clickrun,true);

			this.wordage.visible = false;			
			
            addEventListener(GiftGetActEvent.RECEIVEGIFTCOMPLETE, complete, true);
		}
		
		private function complete(e:GiftGetActEvent):void 
		{
            list.initPage();		
		}
		
		public function init():void
		{
			if (GiftDomain.getInstance().getVar("giftDiarys").length > 0)
			{
			   list = new GiftGetActListView(new GiftGetListUi(), this, 3, false, false);
			   list.setGridItem(ReceiveGiftItemView, ReceiveGiftItemViewUi);
			   list.init(572, 310, 560, 100, -218, -300);
			   list.x = 230;
			   list.y = 317;
               list.setData(GiftDomain.getInstance().getVar("giftDiarys"));				
			}
			else
			{
			   this.wordage.visible = true;	
			   this.allreceive.visible = false;
			}		
							
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			var i:int = 0;
			switch(e.target.name)
			{
				//全部接收
				case "allreceive":				
				
				
				if (GiftDomain.getInstance().isFullReceiveGift())
				{
				    GiftDomain.getInstance().showPiaoStr(LocaleWords.getInstance().getWord("fullReceiveGift"));						
				}
				else
				{
				    giftdiaryId = new Array();
					
					var giftdiary:Array = GiftDomain.getInstance().getVar("giftDiarys");
					
					for (i = 0; i < giftdiary.length; i++ )
					{
					     giftdiaryId.push(giftdiary[i].id);	
					}
					
			        removeEventListener(MouseEvent.CLICK, clickrun,true);	
					var receivegiftcommond:ReceiveGiftCommand = new ReceiveGiftCommand();
				    receivegiftcommond.setData(giftdiaryId);
				    receivegiftcommond.addEventListener(Event.COMPLETE, receivegiftcommondcomplete);					
				}
				

					
				break;
			}

		}		
		
		private function receivegiftcommondcomplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, receivegiftcommondcomplete);			
			addEventListener(MouseEvent.CLICK, clickrun, true);
			
			if (e.target.data.result.isSuccess)
			{
			       var str:String = "你收到了来自朋友们的" + giftdiaryId.length +"件礼物所有的礼物都已放入你的魔法袋!";
				   GiftDomain.getInstance().showSysMsg(str);
				   
				   var giftGetActInitCommond:GiftGetActInitCommond = new GiftGetActInitCommond();
				   giftGetActInitCommond.setData();
				   giftGetActInitCommond.addEventListener(Event.COMPLETE, giftGetActInitCommondComplete);
			}
		}
		
		private function giftGetActInitCommondComplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, giftGetActInitCommondComplete);	
            list.setData(GiftDomain.getInstance().getVar("giftDiarys"));	
		}
	}

}