package happymagic.display.view.student 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happymagic.display.view.student.event.StudentListViewEvent;
	import happymagic.model.vo.StudentStateVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class StudentListView extends UISprite
	{
		private var list:DefaultListView;
		private var iview:levelstudentlistUI;
		private var data:StudentStateVo;
		public function StudentListView() 
		{
			super();
			_view = new levelstudentlistUI();
			
			iview = _view as levelstudentlistUI;
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			
			
			list = new DefaultListView(new stuendtnamelistui, iview, 8,false,false);
			list.setGridItem(StudentListRootView, studentUI);
			list.init(450, 450, 100, 160, -10, -330);
			list.x = -220;
			list.y = 210;
			EventManager.getInstance().addEventListener(StudentListViewEvent.STUDENTAWARD, Award);
			//
			//_defaultListUi.nextBtn.x -= 60;
			//_defaultListUi.nextBtn.y += 207;
			//_defaultListUi.prevBtn.x -= 190;
			//_defaultListUi.prevBtn.y += 203;
		}
		
		public function setData(value:Array):void
		{
			list.setData(value);
		}
		
		private function clickrun(e:MouseEvent):void
		{
			switch(e.target)
			{
				case iview.closebn:
				//关闭：
				closeMe(true);    
				break;
			}
		}
		
		//领奖后的窗口关闭命令
		private function Award(e:Event):void
		{
			closeMe(true);
		}
		
	}

}