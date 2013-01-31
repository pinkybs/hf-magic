package happymagic.display.view.itembox 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.ui.NumSelecterView;
	import happyfish.display.view.IconView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.interfaces.IModule;
	import happyfish.utils.HtmlTextTools;
	import happymagic.display.control.BuyItemMsgControl;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.UiManager;
	import happymagic.model.command.BuyItemCommand;
	import happymagic.model.vo.ItemClassVo;
	import happymagic.model.vo.MoneyType;
	/**
	 * ...
	 * @author jj
	 */
	public class BuyItemMsgView extends UISprite
	{
		public var data:ItemClassVo;
		private var iview:buyItemMessageUi;
		private var numSelecter:NumSelecterView;
		private var icon:IconView;
		private	var type:uint;
		private	var num:uint;
		
		public function BuyItemMsgView() 
		{
			super();
			_view = new buyItemMessageUi();
			
			iview = _view as buyItemMessageUi;
			iview.addEventListener(MouseEvent.CLICK, clickFun, true);
			
			numSelecter = new NumSelecterView(new bugListButton(),1,7);
			numSelecter.x = 0;
			numSelecter.y = 0;
			iview.addChild(numSelecter.view);
			numSelecter.addEventListener(Event.CHANGE, numSelecterclickfun);
		}
		
		private function numSelecterclickfun(e:Event):void 
		{
			if (data.coin)
			{
			 num = data.coin;				
			}
			else if (data.gem)
			{
			 num = data.gem;	
			}

			num = num * e.target.num;
		    refresh();
		}
		
		public function setData(value:ItemClassVo):void {
			data = value;
			
			iview.itemnumber.text = String(DataManager.getInstance().getItemNum(data.i_id));
			iview.nameTxt.text = data.name;
			
			if (data.coin) 
			{
				type = MoneyType.COIN;
				num = data.coin;
			}else {
				type = MoneyType.getPriceType(data);
				num = MoneyType.getPriceNum(data);
			}
			
            refresh();
			
			
			
			iview.priceTypeIcon.gotoAndStop(type);
			iview.priceTypeIcon.scaleX = 0.8;
			iview.priceTypeIcon.scaleY = 0.8;
			loadIcon();
			
		}
		
		private function loadIcon():void
		{
			if (icon) 
			{
				iview.removeChild(icon);
				icon = null;
			}
			icon = new IconView(93, 93, new Rectangle(-115, -50, 93, 93));
			icon.setData(data.class_name);
			iview.addChild(icon);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target.name) 
			{
				case "closeBtn":
					closeMe(true);
				break;
				
				case "bugButton":
				    if (DataManager.getInstance().getEnouthCrystalType(type, num))
					{
			            iview.removeEventListener(MouseEvent.CLICK, clickFun, true);
			            numSelecter.removeEventListener(Event.CHANGE, numSelecterclickfun);					
					    buyItem();						
                    }
					else
				    {
						new BuyItemMsgControl().init(type);
					}
				

				break;
			}
		}
		
		private function buyItem():void
		{
			var command:BuyItemCommand = new BuyItemCommand();
			command.addEventListener(Event.COMPLETE, buyItem_complete);
			command.buyItem(data.i_id, numSelecter.num);
		}
		
		private function buyItem_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, buyItem_complete);
			iview.addEventListener(MouseEvent.CLICK, clickFun, true);
			numSelecter.addEventListener(Event.CHANGE, numSelecterclickfun);
			
			if (e.target.data.result.isSuccess) 
			{
				var itembox:IModule = DisplayManager.uiSprite.getModule(UiManager.MODULE_ITEMBOX);
				if (itembox) 
				{
					(itembox as ItemBoxView).initCurPage();
				}
				
				EventManager.getInstance().showSysMsg(LocaleWords.getInstance().getWord("buySuccess"));
				closeMe(true);
			}	
		}
		
		//刷新
		private function refresh():void
		{
			if (DataManager.getInstance().getEnouthCrystalType(type,num)) 
				{
					
                    iview.priceTxt.text = num.toString();
				}else {
					iview.priceTxt.htmlText = HtmlTextTools.redWords(num.toString());
				}			
		}
		
	}

}