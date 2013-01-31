package happymagic.display.view.magicBook 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.view.IconView;
	import happyfish.display.view.UISprite;
	import happyfish.feed.Command.FeedControlCommond;
	import happyfish.feed.FeedType;
	import happymagic.display.view.ui.ItemIconView;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.MagicClassVo;
	/**
	 * ...
	 * @author jj
	 */
	public class LeanMagicClassMsgView extends UISprite
	{
		private var iview:learnedMsgUi;
		private var data:Object;
		private var icon:IconView;
		
		public function LeanMagicClassMsgView() 
		{
			super();
			_view = new learnedMsgUi();
			
			iview = _view as learnedMsgUi;	
			iview.addEventListener(MouseEvent.CLICK, clickFun, true);
			
			iview.fenxiang.visible = false;
			
		}
		
		public function setData(value:Object):void {
			data = value;

			var feedControlCommond:FeedControlCommond;		
			if (data is MagicClassVo)
			{
				feedControlCommond = new FeedControlCommond();
		   	 	if (feedControlCommond.isExist(FeedType.LEARNMAGICFEED))
				{
					iview.fenxiang.visible = true;
					feedControlCommond.init(iview.fenxiang);
				}					
			}
			else
			{
				feedControlCommond = new FeedControlCommond();
		   	 	if (feedControlCommond.isExist(FeedType.LEARNCHANGEFEED))
				{
					iview.fenxiang.visible = true;
					feedControlCommond.init(iview.fenxiang);
				}				
			}
					
			loadIcon();
		}
		
		private function loadIcon():void
		{
			icon = new IconView(80, 80, new Rectangle( -26, -40, 80, 80));
			icon.setData(data.class_name);
			iview.addChild(icon);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case iview.closeBtn:
				closeMe(true);
				break;
				
				case iview.fenxiang:
				closeMe(true);
				break;
				
				case iview.closeBtn2:
				closeMe(true);				
				break;
								
			}
		}
		
		override public function closeMe(del:Boolean=true):void {
			//隐藏自己
			DisplayManager.uiSprite.closeModule(name,del);
		}
		
	}

}