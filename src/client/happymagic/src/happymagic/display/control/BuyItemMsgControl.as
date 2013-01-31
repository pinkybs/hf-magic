package happymagic.display.control 
{
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happymagic.display.view.SysMsgView;
	import happymagic.events.SysMsgEvent;
	import happymagic.model.vo.MoneyType;
	/**
	 * ...
	 * @author zc
	 */
	public class BuyItemMsgControl 
	{
		
		public function BuyItemMsgControl() 
		{
			
		}
		
		public function init(type:uint):void
		{
			switch(type)
			{
				case MoneyType.GEM:
			           EventManager.getInstance().showSysMsg(LocaleWords.getInstance().getWord("buyitemmsggem"), SysMsgView.TYPE_CONFIRM);
					   EventManager.getInstance().addEventListener(SysMsgEvent.SYSMSG_CLOSED, gotorecharge);
				break;
				
				case MoneyType.COIN:
			           EventManager.getInstance().showSysMsg(LocaleWords.getInstance().getWord("buyitemmsgcoin"), SysMsgView.TYPE_MSG);				    
				break;
			}

			
		}
		
		private function gotorecharge(e:SysMsgEvent):void
		{
			
			EventManager.getInstance().removeEventListener(SysMsgEvent.SYSMSG_CLOSED, gotorecharge);
			//条件满足就是去魔法袋 条件不满足就是什么事情都不做
            if (e.result)
			{
				//EventManager.getInstance().dispatchEvent(new ItemBoxEvent(ItemBoxEvent.SHOW_ITEMBOX));
			}
			else
			{
				
			}
		}		
	}

}