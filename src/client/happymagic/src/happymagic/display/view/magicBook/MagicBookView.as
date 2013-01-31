package happymagic.display.view.magicBook 
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.ui.TabelView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.SoundEffectManager;
	import happymagic.display.view.magicBook.MagicClassBookView;
	import happymagic.display.view.magicBook.TransMagicView;
	import happymagic.events.ActionStepEvent;
	import happymagic.events.MagicBookEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.MagicType;
	
	/**
	 * ...
	 * @author jj
	 */
	public class MagicBookView extends UISprite
	{
		private var iview:magicBookUi;
		private var rightTab:TabelView;
		
		public const Tab_red:String = "tab_magic_red";
		public const Tab_blue:String = "tab_magic_blue";
		public const Tab_green:String = "tab_magic_green";
		public const Tab_trans:String = "tab_trans";
		private var body:Sprite;
		private var currentTab:String;
		private var magicClassView:MagicClassBookView;
		private var transView:TransMagicView;
		//private var magicTypeSelecter:MagicTypeSelectView;
		
		public function MagicBookView() 
		{
			super();
			_view = new magicBookUi();
			
			body = new Sprite();
			_view.addChild(body);
			
			iview = _view as magicBookUi;
			
			iview.tab_magic_red.visible=
			iview.tab_magic_blue.visible=
			iview.tab_magic_green.visible=
			iview.tab_trans.visible = false;
			
			//初始tab
			rightTab = new TabelView();
			rightTab.addEventListener(Event.SELECT, rightTab_select);
			rightTab.btwY = 10;
			rightTab.x = -287;
			rightTab.y = -111;
			iview.addChild(rightTab);
			
			iview.addEventListener(MouseEvent.CLICK, clickFun, true);
			
			//设置TAB
			rightTab.setTabs([iview.tab_magic_red,Tab_red],[iview.tab_magic_blue,Tab_blue],[iview.tab_magic_green,Tab_green]);
			rightTab.addTabs( [iview.tab_trans,Tab_trans]);
		}
		
		/**
		 * 选择标签
		 * @param	openTab
		 */
		public function selectTabIndex(openTab:uint):void 
		{
			rightTab.select(openTab);
		}
		
		private function rightTab_select(e:Event):void 
		{
			//音效
			SoundEffectManager.getInstance().playSound(new sound_pagechange());
			
			selectTab((e.target as TabelView).selectValue);
		}
		
		private function selectTab(tab:String):void {
			
			if (currentTab==tab) 
			{
				return;
			}
			
			currentTab = tab;
			
			clear();
			
			switch (tab) 
			{
				
				case Tab_red:
				if (!magicClassView) 
				{
					magicClassView = new MagicClassBookView();
				}
				body.addChild(magicClassView);
				magicClassView.setData(MagicType.RED);
				break;
				
				case Tab_blue:
				if (!magicClassView) 
				{
					magicClassView = new MagicClassBookView();
				}
				body.addChild(magicClassView);
				magicClassView.setData(MagicType.BLUE);
				break;
				
				case Tab_green:
				if (!magicClassView) 
				{
					magicClassView = new MagicClassBookView();
				}
				body.addChild(magicClassView);
				magicClassView.setData(MagicType.GREEN);
				break;
				
				case Tab_trans:
				if (!transView) 
				{
					transView = new TransMagicView();
				}
				body.addChild(transView);
				//引导事件
				//EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_TRANSTAB_CLICK));
				break;
			}
		}
		
		
		
		private function clear():void {
			while (body.numChildren>0) 
			{
				body.removeChildAt(0);
			}
			
			magicClassView = null;
			transView = null;
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case iview.closeBtn:
				closeMe();
				break;
			}
		}
		
	}

}