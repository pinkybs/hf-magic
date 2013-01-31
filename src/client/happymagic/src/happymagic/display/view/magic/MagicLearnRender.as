package happymagic.display.view.magic 
{
	import flash.events.MouseEvent;
	import happyfish.display.view.ItemRender;
	import happyfish.manager.EventManager;
	import happymagic.display.view.ModuleDict;
	import happymagic.events.MagicBookEvent;
	import happymagic.manager.DisplayManager;
	/**
	 * ...
	 * @author zc
	 */
	public class MagicLearnRender extends ItemRender
	{
		
		private var _iview:goLearnMagicItemUi;
		
		public function MagicLearnRender() 
		{
			this._view = new goLearnMagicItemUi;
			
			this._iview = this._view as goLearnMagicItemUi;
			
			this._iview.addEventListener(MouseEvent.CLICK, clickFun);
		}
		
		
		
		override public function set data($data:Object):void
		{
		   //if (this.asset) 
		   //{
				//this._iview.icon.removeChild(this.asset);
		   //}
			
			this._data = $data;
		}
		
		private function clickFun(e:MouseEvent):void
		{
			if (DisplayManager.uiSprite.getModule(ModuleDict.MODULE_USEMAGIC_LIST))
			{
				DisplayManager.uiSprite.closeModule(ModuleDict.MODULE_USEMAGIC_LIST);
			}
			DisplayManager.uiSprite.showModule(ModuleDict.MODULE_FRIENDS);
			DisplayManager.uiSprite.showModule(ModuleDict.MODULE_MAINMENU);	
			EventManager.getInstance().dispatchEvent(new MagicBookEvent(MagicBookEvent.SHOW_MAGICBOOK,MagicBookEvent.OPENTAB_TRANS));
		}
		
		
	}

}