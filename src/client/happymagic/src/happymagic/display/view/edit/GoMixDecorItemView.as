package happymagic.display.view.edit 
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import happyfish.display.ui.GridItem;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.ModuleManager;
	import happymagic.display.view.magicBook.CompoundTotalView;
	import happymagic.display.view.magicBook.event.MixAndEquipmentType;
	import happymagic.display.view.magicBook.MixMagicView;
	import happymagic.display.view.ModuleDict;
	import happymagic.events.ActionStepEvent;
	import happymagic.manager.DisplayManager;
	
	/**
	 * ...
	 * @author jj
	 */
	public class GoMixDecorItemView extends GridItem
	{
		public function GoMixDecorItemView(uiview:MovieClip) 
		{
			super(uiview);
			
			view.addEventListener(MouseEvent.CLICK, clickFun);
			
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			var compoundTotalView:CompoundTotalView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_COMPOUNDTOTAL, ModuleDict.MODULE_COMPOUNDTOTAL_CLASS, false) as CompoundTotalView;
			DisplayManager.uiSprite.setBg(compoundTotalView);
			
			var tmplist:BuildingItemList = ModuleManager.getInstance().getModule(ModuleDict.MODULE_DIY_ITEMBOX) as BuildingItemList;
			compoundTotalView.setData(MixAndEquipmentType.MIX, tmplist.getCurDecorType());
			
			EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_MENUMIXCLICK));
		}
		
	}

}