package happymagic.display.view.rehandling 
{
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.view.IconView;
	import happyfish.display.view.UISprite;
	import happyfish.feed.Command.FeedControlCommond;
	import happyfish.feed.FeedType;
	import happymagic.model.vo.RehandlingVo;
	/**
	 * ...
	 * @author zc
	 */
	public class RehandlingAwardView extends UISprite
	{
		private var iview:RehandlingAwardViewUi;
		private var data:RehandlingVo;
		
		public function RehandlingAwardView() 
		{
			_view = new RehandlingAwardViewUi();
			iview = view as RehandlingAwardViewUi;
			iview.addEventListener(MouseEvent.CLICK, clickrun);
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
			{
				case "fenxiang":
				    closeMe(true);
				break;
				
				case "closebtn":
				case "affirm":
				  iview.removeEventListener(MouseEvent.CLICK, clickrun);
				  closeMe(true);
				break;
			}
		}
		
		public function setData(_data:RehandlingVo):void
		{
			data = _data;
			loadIcon();

				var feedControlCommond:FeedControlCommond = new FeedControlCommond();
				if (feedControlCommond.isExist(FeedType.REHANDLING))
				{
					iview.fenxiang.visible = true;
					feedControlCommond.init(iview.fenxiang);
				}			
			
		}
		
		private function loadIcon():void 
		{
			var icon:IconView = new IconView(80, 90, new Rectangle(-35, -45, 80, 90));
			icon.setData(data.className);
			iview.addChild(icon);				
		}
		
		
	}

}