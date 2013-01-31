package happymagic.display.view.magicBook 
{
	import flash.events.Event;
	import happyfish.display.ui.Tooltips;
	import happyfish.manager.local.LocaleWords;
	import happyfish.utils.HtmlTextTools;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.MoneyType;
	/**
	 * ...
	 * @author jj
	 */
	public class CrystalNumView extends mixCrystalUi
	{
		private var showEnough:Boolean;
		public var type:uint;
		public var num:int;
		public var enough:Boolean=true;
		
		public function CrystalNumView(_type:uint,_num:int,_showEnough:Boolean=false) 
		{
			
			mouseChildren = false;
			type = _type;
			num = _num;
			showEnough = _showEnough;
			crystalIcon.gotoAndStop(type);
			initNum();
		}
		
		private function initNum():void {
			var typeVar:uint;
			switch (type) 
			{
				
				case MoneyType.COIN:
				typeVar = DataManager.getInstance().currentUser.coin;
				Tooltips.getInstance().register(this, LocaleWords.getInstance().getWord(MoneyType.COIN_NAME), Tooltips.getInstance().getBg("defaultBg"));
				break;
				
				case MoneyType.GEM:
				typeVar = DataManager.getInstance().currentUser.gem;
				Tooltips.getInstance().register(this, LocaleWords.getInstance().getWord(MoneyType.GEM_NAME), Tooltips.getInstance().getBg("defaultBg"));
				break;
			}
			
			enough = typeVar >= num;
			
			if (typeVar<num && showEnough) 
			{
				numTxt.htmlText = HtmlTextTools.redWords(num.toString());
			}else {
				numTxt.text = num.toString();
			}
			
			dispatchEvent(new Event(Event.INIT));
		}
		
		public function setNum(_num:uint):void {
			num = _num;
			initNum();
		}
		
	}

}