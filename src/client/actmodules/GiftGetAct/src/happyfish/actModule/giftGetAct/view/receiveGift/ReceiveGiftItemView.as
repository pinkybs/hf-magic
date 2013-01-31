package happyfish.actModule.giftGetAct.view.receiveGift 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.events.DataEvent;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.actModule.giftGetAct.commond.IgnoreGiftCommand;
	import happyfish.actModule.giftGetAct.commond.ReceiveGiftCommand;
	import happyfish.actModule.giftGetAct.event.GiftGetActEvent;
	import happyfish.actModule.giftGetAct.GiftGetActDict;
	import happyfish.actModule.giftGetAct.manager.GiftDomain;
	import happyfish.actModule.giftGetAct.model.vo.GiftDiaryVo;
	import happyfish.actModule.giftGetAct.model.vo.GiftFriendUserVo;
	import happyfish.actModule.giftGetAct.view.current.CurrentItemView;
	import happyfish.display.ui.FaceView;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.IconView;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.vo.ModuleVo;
	/**
	 * ...
	 * @author ZC
	 */
	
	//收礼界面里中的列表所显示的物品
	public class ReceiveGiftItemView extends GridItem
	{
		private var iview:ReceiveGiftItemViewUi;
		private var type:uint;
		private var data:GiftDiaryVo;
		public function ReceiveGiftItemView(_uview:MovieClip) 
		{
			super(_uview);
			
			type = 0;
			iview = _uview as ReceiveGiftItemViewUi;
			
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			iview.mouseChildren = true;
			
			iview.receive.visible = false;
			iview.ignore.visible = false;
			iview.loopback.visible = false;
			iview.thanks.visible = false;
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
			{
				case "receive":	
			      iview.removeEventListener(MouseEvent.CLICK, clickrun);				
				  var listid:Array = new Array();
				  listid.push(data.id);
				  
				  var receivegiftcommond:ReceiveGiftCommand = new ReceiveGiftCommand();
				  receivegiftcommond.setData(listid);
				  receivegiftcommond.addEventListener(Event.COMPLETE, receivegiftcommondcomplete);
				break;
				
				case "ignore":
			      iview.removeEventListener(MouseEvent.CLICK, clickrun);				
				  var ignoregiftcommond:IgnoreGiftCommand = new IgnoreGiftCommand();
				  ignoregiftcommond.setData(data.id);
				  ignoregiftcommond.addEventListener(Event.COMPLETE, ignoregiftcommondcomplete);				
				break;
				
				case "loopback":
				  var loopback:Boolean = true;
				  GiftDomain.getInstance().setVar("loopbackboolean", loopback);
				  GiftDomain.getInstance().setVar("loopbackgiftuid",data.giftCid);
				  GiftDomain.getInstance().setVar("loopbackfrienduid",data.uid);
				  
				  EventManager.getInstance().dispatchEvent(new GiftGetActEvent(GiftGetActEvent.RECEIVEGIFTLOOPBACK));
				break;
			}
		}
		
		private function ignoregiftcommondcomplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, ignoregiftcommondcomplete);
			iview.addEventListener(MouseEvent.CLICK, clickrun);	
			
			if (e.target.data.result.isSuccess)
			{
			    updata(true);			   
				iview.dispatchEvent(new GiftGetActEvent(GiftGetActEvent.RECEIVEGIFTCOMPLETE));
			}
		}
		
		private function receivegiftcommondcomplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, receivegiftcommondcomplete);			
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			
			if (e.target.data.result.isSuccess)
			{
				updata();
                GiftDomain.getInstance().showAwardView(e.target.data);				
				iview.dispatchEvent(new GiftGetActEvent(GiftGetActEvent.RECEIVEGIFTCOMPLETE));				
			}
		}
		
		override public function setData(value:Object):void
		{
			data = value as GiftDiaryVo;
			
			if (data.hasGet)
			{
				if (GiftDomain.getInstance().isSendGift(data.uid))
				{
				    iview.loopback.visible = true;					
				}
				else
				{
					//iview.thanks.visible = true;
				}

			}
			else
			{
				iview.ignore.visible = true;
				iview.receive.visible = true;
			}
			
			type = data.giftType;
			
			
			var date:Date = new Date();
			date.time = data.date*1000;
			
			var datetemp:Date = new Date();
			var time:uint = datetemp.time - date.time;
			time = time / 86400000;
			
			if (time > 0)
			{
				iview.date.text = String(time);	
				iview.daynum.text = String("天前");
			}
			else
			{
				time = datetemp.time - date.time;
				time = time / 3600000;
				
				if (time > 0)
				{
					iview.date.text = String(time);	
					iview.daynum.text = String("小时前");					
				}
				else
				{
					time = datetemp.time - date.time;
					time = time / 600000;
					iview.date.text = String(time);	
					iview.daynum.text = String("分钟前");						
				}

			}

			iview.nametxt.text = "好友" + GiftDomain.getInstance().getFriendUserVo(data.uid).name + "赠送你这件礼物";
			
			loadicon();
		}
		
		private function loadicon():void 
		{
			var icon:IconView = new IconView(50, 50, new Rectangle(425, 15, 50, 50));
			icon.setData(data.className);			
			iview.addChild(icon);
			
			GiftDomain.getInstance().showTips(icon, data.name);
			
			var usergiftvo:GiftFriendUserVo = GiftDomain.getInstance().getFriendUserVo(data.uid);
			
			var faceview:DisplayObjectContainer = GiftDomain.getInstance().showFaceView(usergiftvo.face);
			iview.addChild(faceview);
			faceview.x = 48;
			faceview.y = 17;
		}
		
		//接收礼物后的本地数据更新
		private function updata(booldelete:Boolean = false):void
		{
			var temp:Array = GiftDomain.getInstance().getVar("giftDiarys");
		    for (var i:int = 0; i < temp.length; i++ )
		    {
				if (temp[i].id == data.id)
				{
					(temp[i] as GiftDiaryVo).hasGet = true; 
					if (booldelete)
					{
						temp.splice(i, 1);
						return;
					}	
				}			
			}			
			
			GiftDomain.getInstance().setVar("giftDiarys",temp);
		}
		
	}

}