package happymagic.display.view.diary 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.ui.TabelView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happymagic.display.view.diary.DiaryListView;
	import happymagic.events.DiaryEvent;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.DiaryType;
	
	/**
	 * ...
	 * @author jj
	 */
	public class DiaryView extends UISprite
	{
		private var tab:TabelView;
		private var iview:DailyRecordUi;
		private var list:DiaryListView;
		
		public function DiaryView() 
		{
			super();
			_view = new DailyRecordUi();
			
			iview = _view as DailyRecordUi;
			iview.addEventListener(MouseEvent.CLICK, clickFun, true);
			
			list = new DiaryListView(new DailyRecordListUI(), iview);
			
			//初始tab
			tab = new TabelView();
			tab.addEventListener(Event.SELECT, tab_select);
			tab.btwX = 3;
			tab.x = -160;
			tab.y = -146;
			
			tab.setTabs([iview.tab_all, null], [iview.tab_friend, DiaryType.FRIEND], 
			[iview.tab_system, DiaryType.SYSTEM]);
			
			iview.addChild(tab);
			
			tab.select(0);
			
			EventManager.getInstance().addEventListener(DiaryEvent.DIARY_ADDED, diaryAdded);
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
			
		private function diaryAdded(e:DiaryEvent):void 
		{
			list.setData(DataManager.getInstance().diarys, "type", tab.selectValue,true);
		}
		
		private function tab_select(e:Event=null):void 
		{
			var arr:Array = DataManager.getInstance().diarys;
			list.setData(DataManager.getInstance().diarys, "type", tab.selectValue);
		}
		
	}

}