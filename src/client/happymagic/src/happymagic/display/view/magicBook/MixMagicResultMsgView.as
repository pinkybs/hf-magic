package happymagic.display.view.magicBook 
{
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.view.IconView;
	import happyfish.display.view.UISprite;
	import happyfish.feed.Command.FeedControlCommond;
	import happyfish.feed.FeedType;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.ModuleManager;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.UiManager;
	import happymagic.model.vo.DecorClassVo;
	import happymagic.model.vo.MagicClassVo;
	import happymagic.model.vo.MixMagicVo;
	/**
	 * ...
	 * @author jj
	 */
	public class MixMagicResultMsgView extends UISprite
	{
		private var iview:mixedMsgUi;
		private var data:MixMagicVo;
		private var icon:IconView;
		private var decor:DecorClassVo;
		
		public function MixMagicResultMsgView() 
		{
			super();
			_view = new mixedMsgUi();
			
			iview = _view as mixedMsgUi;
			
			iview.fenxiang.visible = false;
			iview.addEventListener(MouseEvent.CLICK, clickFun, true);
		}
		
		public function setData(value:MixMagicVo,num:uint):void {
			data = value;
			decor = DataManager.getInstance().getDecorClassByDid(value.d_id);
			
			iview.starBar.gotoAndStop(decor.level);
			iview.nameTxt.text = decor.name;
			iview.numTxt.text = "x " + num.toString();
			
			var feedControlCommond:FeedControlCommond = new FeedControlCommond();
			if (feedControlCommond.isExist(FeedType.MIXSUCCESS))
			{
				iview.fenxiang.visible = true;
				feedControlCommond.init(iview.fenxiang);
			}	
			
			loadIcon();
		}
		
		private function loadIcon():void
		{
			if (icon) 
			{
				if (icon.parent) 
				{
					icon.parent.removeChild(icon);
				}
			}
			icon = new IconView(80, 80, new Rectangle( 8, -46, 100, 100));
			icon.setData(decor.class_name);
			iview.addChildAt(icon, iview.getChildIndex(iview.starBar) );
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case iview.closeBtn:
				closeMe(true);
				break;
				
				case iview.closeBtn2:
				closeMe(true);				
				break;
				
				case iview.goDiyBtn:
				ModuleManager.getInstance().closeModule("magicBook", true);
				EventManager.getInstance().dispatchEvent(new SceneEvent(SceneEvent.START_DIY));
				
				closeMe(true);
				break;
				
				case iview.fenxiang:
				 closeMe(true);
				break;
			}
		}
		
	}

}