package happymagic.display.view.magicBook 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.ui.TabelView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happymagic.display.view.magicBook.event.MixAndEquipmentType;
	import happymagic.events.SceneEvent;
	/**
	 * ...
	 * @author ... ZC
	 */
	
	 //合成系统的总页面
	public class CompoundTotalView extends UISprite 
	{
		private var topTab:TabelView;
		private var iview:MixAndEquipmentViewUi;
		private var mixMagicView:MixMagicView;
		
		private var upState:uint;
		private var LeftState:uint;
		private var pagelength:uint
		public function CompoundTotalView() 
		{
			_view = new MixAndEquipmentViewUi();
			iview = _view as MixAndEquipmentViewUi;
			
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			topTab = new TabelView();
			iview.addChild(topTab);
			topTab.btwX = 1;
			topTab.x = -30;
			topTab.y = -260;
			topTab.setTabs([iview.Mix, MixAndEquipmentType.MIX], [iview.Equipment, MixAndEquipmentType.EQUIPMENT]);
			topTab.addEventListener(Event.SELECT, tab_select);
			topTab.setAbleItem(1, false);
		}
		
		private function tab_select(e:Event):void 
		{
			clear();
			switch((e.target as TabelView).selectIndex)
			{
				case 0:
				       mixMagicView = new MixMagicView();
					   mixMagicView.x = 0;
					   mixMagicView.y = 0;
					   iview.addChild(mixMagicView);
					   mixMagicView.setData(LeftState,pagelength);
				break;
				
				case 1:
				
				break;
			}
		}
		
		private function clear():void 
		{
			if (mixMagicView)
			{
				if (iview.contains(mixMagicView))
				{
					iview.removeChild(mixMagicView)					
				}
			}			
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
			{
				case "closeBn":
				    closeMe(true);
				break;
				
				case "zhuangshiBn":
				    EventManager.getInstance().dispatchEvent(new SceneEvent(SceneEvent.START_DIY));				
				    closeMe(true);
				break;
			}
		}
		
		//pagelength去第几页
		public function setData(_state1:uint = 0,_state2:uint = 0,_pagelength:uint = 1):void
		{
			pagelength = _pagelength;
			LeftState = _state2;
			topTab.select(_state1);					
		}
		
	}

}