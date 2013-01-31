package happymagic.display.view.magicBook 
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.utils.HtmlTextTools;
	import happymagic.events.MagicBookEvent;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.MagicType;
	/**
	 * ...
	 * @author jj
	 */
	public class ChangeMagicTypeConfirmView extends UISprite
	{
		private var iview:changeMagicTypeConfirmUi;
		private var callBack:Function;
		public var result:Boolean;
		
		public function ChangeMagicTypeConfirmView() 
		{
			super();
			_view = new changeMagicTypeConfirmUi() as MovieClip;
			
			iview = _view as changeMagicTypeConfirmUi;
			
			view.addEventListener(MouseEvent.CLICK, clickFun, true);
		}
		
		public function setData(type:uint):void {
			
			var typestr:String;
			switch (type) 
			{
				case MagicType.RED:
				typestr = LocaleWords.getInstance().getWord("m_type_red");
				break;
				
				case MagicType.BLUE:
				typestr = LocaleWords.getInstance().getWord("m_type_blue");
				break;
				
				case MagicType.GREEN:
				typestr = LocaleWords.getInstance().getWord("m_type_green");
				break;
			}
			var tmpstr:String = LocaleWords.getInstance().getWord("changeMagicConfirm", typestr);
			tmpstr = HtmlTextTools.fontWord(tmpstr, "#FFCC00", 14, "");
			view.txt.htmlText = tmpstr;
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			var event:MagicBookEvent;
			switch (e.target) 
			{
				case iview.closeBtn:
				event= new MagicBookEvent(MagicBookEvent.CHANGE_MAGIC_TYPE);
				event.data = false;
				EventManager.getInstance().dispatchEvent(event);
				break;
				
				case iview.yesBtn:
				event= new MagicBookEvent(MagicBookEvent.CHANGE_MAGIC_TYPE);
				event.data = true;
				EventManager.getInstance().dispatchEvent(event);
				break;
			}
			
			DisplayManager.uiSprite.closeModule(name, true);
			
		}
		
	}

}