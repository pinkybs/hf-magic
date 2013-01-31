package happymagic.display.view.itembox 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.ui.TabelView;
	import happyfish.display.ui.Tooltips;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happymagic.display.view.itembox.events.ItemBoxEvent;
	import happymagic.display.view.itembox.events.ItemShopEvent;
	import happymagic.display.view.itembox.ItemShopListView;
	import happymagic.display.view.ModuleDict;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.ItemClassVo;
	import happymagic.model.vo.ItemType;
	
	/**
	 * ...
	 * @author jj
	 */
	public class ItemShopView extends UISprite
	{
		private var iview:ShopUi;
		private var list:ItemShopListView;
		private var tab:TabelView;
		
		public function ItemShopView() 
		{
			super();
			
			_view = new ShopUi();
			
			iview = _view as ShopUi;
			iview.addEventListener(ItemShopEvent.ITEM_CLICK, itemClickFun, true);
			iview.addEventListener(MouseEvent.CLICK, clickFun, true);
			
			
			list = new ItemShopListView(new ShopListView(), iview);
			
			//初始tab
			tab = new TabelView();
			tab.addEventListener(Event.SELECT, tab_select);
			tab.btwX = 3;
			tab.x = -126;
			tab.y = -143;
			
			tab.setTabs([iview.tab_food, ItemType.FOOD], [iview.tab_solution, ItemType.SOLUTION], 
			[iview.tab_stuff, ItemType.STUFF], [iview.tab_other, ItemType.OTHER]);
			
			iview.addChild(tab);
			
			tab.select(0);
			iview.mouseChildren = true;
			
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case iview.closeBtn:
					closeMe(true);
				break;
				
				case iview.goItemBoxBtn:
				    EventManager.getInstance().dispatchEvent(new ItemBoxEvent(ItemBoxEvent.SHOW_ITEMBOX));
				break;
				
				case iview.giftbtn:
				     //if(DisplayManager.uiSprite.)
				break;
				
			}
		}
		
		private function tab_select(e:Event):void 
		{
			list.setData(DataManager.getInstance().getCanBuyItemClass(), "type", (e.target as TabelView).selectValue);
		}
		
		
		private function itemClickFun(e:ItemShopEvent):void 
		{
			showBuyMsg(e.item);
		}
		
		private function showBuyMsg(itemClass:ItemClassVo):void
		{
			var tmp:BuyItemMsgView = 
				DisplayManager.uiSprite.addModule(ModuleDict.MODULE_BUYITEM_MSG, ModuleDict.MODULE_BUYITEM_MSG_CLASS) as BuyItemMsgView;
			tmp.setData(itemClass);
		}
		
	}

}