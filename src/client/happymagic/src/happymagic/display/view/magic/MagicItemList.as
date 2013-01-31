package happymagic.display.view.magic 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.view.PageList;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.ModuleManager;
	import happymagic.display.view.edit.BuildingItemRender;
	import happymagic.display.view.ModuleDict;
	import happymagic.events.MagicClassBookEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.MagicType;
	import happymagic.model.vo.TransMagicVo;
	
	/**
	 * ...
	 * @author Beck
	 */
	public class MagicItemList extends UISprite
	{
		private var _iview:ui_allmagicmenu;
		public var page_list:PageList;
		
		public function MagicItemList() 
		{
			super();
			
			this._view = new ui_allmagicmenu;
			this._iview = this._view as ui_allmagicmenu;
			
			//this._iview.magic_icon.gotoAndStop(DataManager.getInstance().userInfo.magic_type);
			this._iview.addEventListener(MouseEvent.CLICK, clickFun, true);
			this._view.addEventListener(Event.ADDED_TO_STAGE, addToStage);
		}
		
		private function addToStage(e:Event):void
		{
			this._view.removeEventListener(Event.ADDED_TO_STAGE, addToStage);
			
			
			var item_array:Array = DataManager.getInstance().getLearnedTrans();

			calculate(item_array);
			//list
			this.page_list = new PageList();
			page_list.startX = 90;
			page_list.create(item_array, MagicItemRender);

			this._view.addChild(this.page_list);
		}
		
		public function getItemByMid(mid:uint):MagicItemRender {
			for (var i:int = 0; i <page_list.items.length ; i++) 
			{
				if ((page_list.items[i] as MagicItemRender).data.magic_id==mid) 
				{
					return page_list.items[i] as MagicItemRender;
				}
			}
			return null;
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case this._view.r_btn:
					//下一页
					this.page_list.nextPage();
					break;
				case this._view.l_btn:
					//上一页
					this.page_list.prevPage();
					break;
				case this._iview.yes_btn:
					DisplayManager.uiSprite.showModule(ModuleDict.MODULE_FRIENDS);
					DisplayManager.uiSprite.showModule(ModuleDict.MODULE_MAINMENU);
					closeMe(true);
					break;
			}
		}
		
		//计算数据处理
		private function calculate(temparray:Array):void
		{
			var num:int = 6;
			var temp:int;
			temp = temparray.length / num + 1;
			//缺少的总数
			var sum:int = num * temp - temparray.length;
			trace(temp);
            for (var i:int = 0; i < sum;i++ )
			{	
			    var trantemp:TransMagicVo = new TransMagicVo();
				trantemp.class_name = PageList.FALSEDATA;
				temparray.push(trantemp);
			}
		}
		
	}

}