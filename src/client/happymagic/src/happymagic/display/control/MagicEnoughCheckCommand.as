package happymagic.display.control 
{
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.AlginType;
	import happymagic.display.view.itembox.events.ItemBoxEvent;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.SysMsgView;
	import happymagic.events.SysMsgEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	/**
	 * ...
	 * @author ZC
	 */
	public class MagicEnoughCheckCommand 
	{
		
		public function MagicEnoughCheckCommand() 
		{
			
		}
		
		/**
		 * 返回魔法是否足够
		 * @param	mp
		 * @return	够为true
		 */
		public function check(mp:int):Boolean {
			if (DataManager.getInstance().getEnoughMp(mp)) 
			{
				return true;
			}else {
				init();
				return false;
			}
		}
		
		public function init():void
		{
			EventManager.getInstance().showSysMsg(LocaleWords.getInstance().getWord("teachMagicNoEnough"), SysMsgView.TYPE_CONFIRM);
			EventManager.getInstance().addEventListener(SysMsgEvent.SYSMSG_CLOSED, newsreaction);
		}
		
		
		private function newsreaction(e:SysMsgEvent):void
		{
			
			EventManager.getInstance().removeEventListener(SysMsgEvent.SYSMSG_CLOSED, newsreaction);
			//条件满足就是去魔法袋 条件不满足就是什么事情都不做
            if (e.result)
			{
				EventManager.getInstance().dispatchEvent(new ItemBoxEvent(ItemBoxEvent.SHOW_ITEMBOX));
			}
			else
			{
				
			}
		}
		
	}

}