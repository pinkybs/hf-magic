package happymagic.display.view.itembox 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.ui.TabelView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.SoundEffectManager;
	import happymagic.display.view.itembox.events.ItemBoxEvent;
	import happymagic.display.view.itembox.ItemBoxItemView;
	import happymagic.display.view.itembox.ItemBoxListView;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.SysMsgView;
	import happymagic.events.DataManagerEvent;
	import happymagic.events.SysMsgEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.UseItemCommand;
	import happymagic.model.vo.ItemType;
	import happymagic.model.vo.ItemVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class ItemBoxView extends UISprite
	{
		private var iview:itemBoxUi;
		private var data:Array;
		private var list:ItemBoxListView;
		private var tab:TabelView;
		private var currentUseItem:ItemBoxItemView;
		
		public function ItemBoxView() 
		{
			super();
			
			_view = new itemBoxUi();
			
			iview = _view as itemBoxUi;
			iview.addEventListener(ItemBoxEvent.ITEM_CLICK, itemClickFun, true);
			iview.addEventListener(MouseEvent.CLICK, clickFun, true);
			
			EventManager.getInstance().addEventListener(DataManagerEvent.ITEMS_CHANGE, refreshFun);
			
			list = new ItemBoxListView(new itemBoxListUi(), iview);
			list.setXY(30,32,550,32);
			//初始tab
			tab = new TabelView();
			tab.addEventListener(Event.SELECT, tab_select);
			tab.btwX = 3;
			tab.x = 184;
			tab.y = 3;
			
			tab.setTabs([iview.tab_food, ItemType.FOOD], [iview.tab_solution, ItemType.SOLUTION], 
			[iview.tab_stuff, ItemType.STUFF], [iview.tab_other, ItemType.OTHER]);
			
			iview.addChild(tab);
			
			tab.select(0);
			
		}
		
		private function refreshFun(e:DataManagerEvent):void 
		{
			list.setData(DataManager.getInstance().items, "type", tab.selectValue,true);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case iview.yes_btn:
					EventManager.getInstance().dispatchEvent(new ItemBoxEvent(ItemBoxEvent.HIDE_ITEMBOX));
				break;
				
				case iview.goShopBtn:
					DisplayManager.uiSprite.addModule(ModuleDict.MODULE_ITEMSHOP, ModuleDict.MODULE_ITEMSHOP_CLASS,false,AlginType.CENTER,0,-40);
				break;
			}
		}
		
		private function tab_select(e:Event=null):void 
		{
			list.setData(DataManager.getInstance().items, "type", tab.selectValue);
		}
		
		
		private function itemClickFun(e:ItemBoxEvent):void 
		{
			
			
			currentUseItem = e.item;
			
			if (currentUseItem.data.type==ItemType.FOOD || currentUseItem.data.type==ItemType.SOLUTION ) 
			{
				iview.mouseChildren = false;
				DisplayManager.uiSprite.showSysMsg(LocaleWords.getInstance().getWord("confirmUseItem", currentUseItem.data.name), SysMsgView.TYPE_CONFIRM);
				
				EventManager.getInstance().addEventListener(SysMsgEvent.SYSMSG_CLOSED, useItemConfirm_result);
			}
			
			
		}
		
		private function useItemConfirm_result(e:SysMsgEvent):void 
		{
			iview.mouseChildren = true;
			e.target.removeEventListener(SysMsgEvent.SYSMSG_CLOSED, useItemConfirm_result);
			
			if (e.result) 
			{
				useItem(currentUseItem.data);
			}else {
				currentUseItem = null;
			}
			
		}
		
		private function useItem(item:ItemVo):void
		{
			if (item.type==ItemType.FOOD) 
			{
				//音效
				SoundEffectManager.getInstance().playSound(new sound_food());
			}else if (item.type==ItemType.SOLUTION) {
				//音效
				SoundEffectManager.getInstance().playSound(new sound_drink());
			}
			var command:UseItemCommand = new UseItemCommand();
			command.addEventListener(Event.COMPLETE, useItem_complete);
			command.useItem(item.i_id);
		}
		
		/**
		 * 刷新当前页面,不更换页数
		 */
		public function initCurPage():void {
			var datas:Array = DataManager.getInstance().items;
			list.setData(datas, "type", tab.selectValue,true);
		}
		
		private function useItem_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, useItem_complete);
			
			if (e.target.data.result.isSuccess) 
			{
				if (currentUseItem.data.num<=0) 
				{
					initCurPage();
				}else {
					currentUseItem.setData(currentUseItem.data);
				}
				
			}
			currentUseItem = null;
		}
		
		
		
	}

}