package happymagic.display.view.edit 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.ui.TabelView;
	import happyfish.display.view.UISprite;
	import happyfish.display.view.PageList;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.vo.ModuleStateType;
	import happyfish.scene.world.control.MouseCursorAction;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.grid.SolidObject;
	import happymagic.display.view.edit.DecorListView;
	import happymagic.events.DataManagerEvent;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.PublicDomain;
	import happymagic.model.command.enterEditCommand;
	import happymagic.model.command.leaveEditCommand;
	import happymagic.model.vo.DecorType;
	import happymagic.model.vo.DecorVo;
	import happymagic.scene.world.control.MouseDefaultAction;
	import happymagic.scene.world.control.MouseEditAction;
	import happymagic.scene.world.control.MouseMagicAction;
	import happymagic.scene.world.grid.item.Desk;
	import happymagic.scene.world.MagicWorld;

	/**
	 * 建筑道具列表
	 * @author Beck
	 */
	public class BuildingItemList extends UISprite
	{
		public var page_list:PageList;
		protected var _iview:ui_diymenu;
		private var itemTips:DecorItemTipsView;
		private var tabel:TabelView;
		public var list:DecorListView;
		private var currentTab:uint;
		
		public function BuildingItemList() 
		{
			super();
			this._view = new ui_diymenu;
			this._iview = this._view as ui_diymenu;
			
			_iview.no_btn.visible = false;
			
			//赋值
			DisplayManager.uiDiyMenu = this._iview;
			DisplayManager.buildingItemList = this;
			//this._iview.yes_btn.buttonMode = true;
			
			
			//设置默认标签
			tabel = new TabelView();
			tabel.addEventListener(Event.SELECT, tabel_select);
			tabel.x = 76;
			tabel.y = -27;
			tabel.btwX = 5;
			_iview.addChild(tabel);
			tabel.addTabs([_iview.diy_1,DecorType.DESK],[_iview.diy_2,DecorType.DOOR], [_iview.diy_5,DecorType.DECOR], [_iview.diy_4,DecorType.WALL_PAPER], [_iview.diy_7,DecorType.WALL_DECOR], [_iview.diy_3,DecorType.FLOOR]);
			
			//列表
			list = new DecorListView(new decorListUi(), view);
			list.x = 89;
			list.y = 48;
			list.init(600, 100, 89, 100, 0, -48);
			
			this._iview.addEventListener(MouseEvent.CLICK, clickFun, true);
			this._iview.addEventListener(MouseEvent.MOUSE_OVER, onBuildingItemOver, true);
			this._iview.addEventListener(MouseEvent.MOUSE_OUT, onBuildingItemOut, true);
			
			EventManager.getInstance().addEventListener(SceneEvent.DIY_FINISHED, diyFinished);
			EventManager.getInstance().addEventListener(SceneEvent.DIY_CANCELDIY, diyFinished);
			
			EventManager.getInstance().addEventListener(DataManagerEvent.DECORBAG_CHANGE, bagDataChange);
			
			tabel.select(1);
		}
		
		/**
		 * 背包数据更新事件
		 * @param	e
		 */
		private function bagDataChange(e:DataManagerEvent):void 
		{
			if (state==ModuleStateType.SHOWING) 
			{
				list.setData(DataManager.getInstance().decorBagList, "type", currentTab, true);
				//list.initPage();
			}
		}
		
		private function diyFinished(e:SceneEvent):void 
		{
			_iview.mouseChildren = true;
			closeMe(true);
		}
		
		public function addItem(value:DecorVo):void {
			DataManager.getInstance().addDecor([value]);
			list.setData(DataManager.getInstance().decorBagList, "type", currentTab, true);
		}
		
		private function tabel_select(e:Event):void 
		{
			selectTab((e.target as TabelView).selectValue);
		}
		
		private function selectTab(tab:uint):void {
			
			if (currentTab==tab) 
			{
				return;
			}
			
			currentTab = tab;
			
			loadData();
		}
		
		private function loadData():void
		{
			if (DataManager.getInstance().decorBagList) 
			{
				initData();
				return;
			}
			var enter_edit_command:enterEditCommand = new enterEditCommand();
			enter_edit_command.load();
			enter_edit_command.addEventListener(Event.COMPLETE, loadData_complete);
		}
		
		private function loadData_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, loadData_complete);
			
			initData();
		}
		
		public function initData(savePage:Boolean=false):void
		{
			
			list.setData(DataManager.getInstance().decorBagList, "type", currentTab,savePage);
		}
		
		public function onBuildingItemOver(e:MouseEvent):void {
			if (e.target is ui_decor) 
			{
				showTips(e.target.control);
			}
		}
		
		public function onBuildingItemOut(e:MouseEvent):void {
			if (e.target is ui_decor) 
			{
				hideTips();
			}
		}
		
		
		
		public function showTips(item:DecorListItemView):void {
			if (!itemTips) 
			{
				itemTips = new DecorItemTipsView();
				itemTips.y = 5;
				
			}
			
			itemTips.setData(item.data as DecorVo);
			itemTips.x = item.view.x+39+90;
			itemTips.visible = true;
			_iview.addChild(itemTips);
		}
		
		public function hideTips():void {
			if (itemTips) 
			{
				itemTips.visible = false;
			}
		}
		
		public function getCurDecorType():uint {
			return tabel.selectValue as uint;
		}
		
		public function getTableIndex():uint 
		{
			return tabel.selectIndex;
		}
		
		private function clickFun(e:MouseEvent):void
		{
			DataManager.getInstance().worldState.view.dispatchEvent(new GameMouseEvent(GameMouseEvent.CLICK, null, "Background"));
			switch (e.target) 
			{
				
				case this._iview.yes_btn:
					_iview.mouseChildren = false;
					
					//检查是否有不可SAVE的装饰物
					var world:MagicWorld = DataManager.getInstance().worldState.world as MagicWorld;
					
					var cantPut:Boolean = world.checkAllDeskCantWalk();
					
					if (cantPut) 
					{
						EventManager.getInstance().showSysMsg(LocaleWords.getInstance().getWord("cantSaveDiy"));
						_iview.mouseChildren = true;
						return;
					}
					
					var leave_edit_command:leaveEditCommand = new leaveEditCommand();
					leave_edit_command.addEventListener(Event.COMPLETE,leaveEdit_complete);
					leave_edit_command.load();
				break;
					
				case _iview.no_btn:
					//派发完成事件SceneEvent.DIY_FINISHED
					EventManager.getInstance().dispatchEvent(new SceneEvent(SceneEvent.DIY_CANCELDIY));
				break;
			}
		}
		
		private function leaveEdit_complete(e:Event):void 
		{
			_iview.mouseChildren = true;
			e.target.removeEventListener(Event.COMPLETE,leaveEdit_complete);
		}
		
	}

}