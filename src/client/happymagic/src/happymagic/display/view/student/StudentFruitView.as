package happymagic.display.view.student 
{
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.view.IconView;
	import happyfish.display.view.UISprite;
	import happymagic.model.vo.StudentStateVo;
	import happymagic.model.vo.StudentVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class StudentFruitView extends UISprite
	{
		private var iview:studentfruitUi;
		private var _data:StudentStateVo;
		
		public function StudentFruitView() 
		{
			_view = new studentfruitUi();
			
			iview = _view as studentfruitUi;
			iview.addEventListener(MouseEvent.CLICK, clickrun);
		}
		
		public function setData(data:StudentStateVo):void
		{
			_data = data;
			
			var icon:IconView = new IconView(93, 105, new Rectangle(-33,-50,93,105));
			iview.addChild(icon);
		    icon.setData(_data.className);
		}
		
		private function clickrun(e:MouseEvent):void
		{
			switch(e.target)
			{
				case iview.closeBn1:
				case iview.closeBn2:
				closeMe(true);
				break;
				
				case iview.fenxiang:
				closeMe(true);
				break;
			}
		}
		
	}

}