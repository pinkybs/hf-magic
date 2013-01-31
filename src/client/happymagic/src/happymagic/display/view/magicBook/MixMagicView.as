package happymagic.display.view.magicBook 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import happyfish.display.ui.GridView;
	import happyfish.display.ui.TabelView;
	import happyfish.display.view.UISprite;
	import happyfish.events.ModuleEvent;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.SoundEffectManager;
	import happyfish.utils.display.BtnStateControl;
	import happymagic.display.view.itembox.BuyItemMsgView;
	import happymagic.display.view.magicBook.event.MixMagicEvent;
	import happymagic.display.view.ModuleDict;
	import happymagic.events.ActionStepEvent;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.DecorClassVo;
	import happymagic.model.vo.DecorType;
	import happymagic.model.vo.ItemClassVo;
	import happymagic.model.vo.MixMagicVo;
	/**
	 * ...
	 * @author jj
	 */
	public class MixMagicView extends MixMagicViewUi
	{
		private var leftTab:TabelView;
		private var list:MixMagicListView;
		private var infoView:MixMagicInfoView;
        private var MixButtonMc:Sprite;
		private var pagelength:uint;
		public function MixMagicView() 
		{
			addEventListener(MouseEvent.CLICK, clickFun,true);
			
			//初始tab
			leftTab = new TabelView();
			leftTab.addEventListener(Event.SELECT,tab_select)
			leftTab.btwY = 0;
			leftTab.x = 235;
			leftTab.y = -150;
			
			leftTab.setTabs([tab_door, DecorType.DOOR] , [tab_desk, DecorType.DESK], 
				[tab_decor, DecorType.DECOR], [tab_wall, DecorType.WALL_DECOR], 
				[tab_wallpaper, DecorType.WALL_PAPER], [tab_floor, DecorType.FLOOR]);
			addChild(leftTab);
			
			//info view
			infoView = new MixMagicInfoView(this);
			addChild(infoView);
			infoView.x -= 255;
			infoView.y -= 193;
			
			
			

			list = new MixMagicListView(new mixlist(), this, 9,false,false);
			list.init(300,300,95,95,-75,-100);
			list.x = 0;
			list.y = -100;
			list.setGridItem(MixMagicItemView, mixMagicItemUi);
			list.iview.addEventListener(MouseEvent.CLICK, listClickFun);			
			
			BtnStateControl.setBtnState(this.hechengBn, false);
			
			sixstarBg.visible = false;
			
			addEventListener(MixMagicInfoView.MIX_COMPLETE, complete, true);
			addEventListener(MixMagicEvent.BUY, buy, true);
			addEventListener(MixMagicEvent.MIX, mix,true);
		}
		
		private function mix(e:MixMagicEvent):void 
		{
			if (e.msgs is DecorClassVo)
			{
			   var temp:MixMagicVo = DataManager.getInstance().getMixMagicByDid((e.msgs as DecorClassVo).d_id);
			   infoView.setData(temp);
			}			
		}
		
		private function listClickFun(e:MouseEvent):void 
		{
			if (e.target is mixMagicItemUi) 
			{
				
				if ((e.target.control as MixMagicItemView).data.needLevel<=DataManager.getInstance().currentUser.level) 
				{
					infoView.setData((e.target.control as MixMagicItemView).data);
					this.sixstarBg.visible = true;
				}
			}
		}
		
		private function tab_select(e:Event):void 
		{
			//音效
			SoundEffectManager.getInstance().playSound(new sound_pagechange());
			
			DataManager.getInstance().mixMagics.sortOn("needLevel", Array.NUMERIC);
			
			var tmparr:Array = DataManager.getInstance().mixMagics;
			tmparr.sortOn(["needLevel", "mix_mid"], [Array.NUMERIC, Array.NUMERIC]);
			
			list.setData(tmparr, "type", (e.target as TabelView).selectValue);
			list.gopageLength(pagelength);
			BtnStateControl.setBtnState(hechengBn, false);
			
			sixstarBg.visible = false;

			infoView.clear();
			infoView.setData(null);
		}   
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{			
				case hechengBn:
				infoView.startMix();
				break;
				
			}
		}
		
		//合成流程结束以后需要完成的后续善后
		private function complete(e:Event):void
		{

			infoView.clear();
			infoView.setData(null);
			
			sixstarBg.visible = false;
			
			BtnStateControl.setBtnState(hechengBn, false);
			
			list.initPage();
			
			EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_MIX_COMPLETE));
		}
		
		private function buy(e:MixMagicEvent):void
		{					
			if (e.msgs is ItemClassVo)
			{
			   var tmp:BuyItemMsgView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_BUYITEM_MSG, ModuleDict.MODULE_BUYITEM_MSG_CLASS) as BuyItemMsgView;
			   DisplayManager.uiSprite.setBg(tmp);
			   tmp.setData(e.msgs as ItemClassVo);
			   ModuleManager.getInstance().addEventListener(ModuleEvent.MODULE_CLOSE, closebugitem);
			}
		}
						
		private function closebugitem(e:ModuleEvent):void
		{
			switch(e.moduleName)
			{
				 case ModuleDict.MODULE_BUYITEM_MSG:
				         ModuleManager.getInstance().removeEventListener(ModuleEvent.MODULE_CLOSE, closebugitem);
				         infoView.setData(infoView.data);	
				         list.initPage();
				 break; 
			
			}

		}
		
		public function setData(_state:uint = 0,_pagelength:uint = 1):void
		{
			switch(_state)
			{
				case DecorType.DOOR:
				     _state = 0;
				break;
			
				case DecorType.DESK:
				     _state = 1;				
				break;
				
				case DecorType.FLOOR:
				     _state = 5;				
				break;
				
				case DecorType.WALL_DECOR:
				     _state = 3;				
				break;
				
				case DecorType.WALL_PAPER:
				     _state = 4;				
				break;
				
				case DecorType.DECOR:
				     _state = 2;				
				break;																			
			}
				pagelength = _pagelength;
				leftTab.select(_state);				
			
		}
		
	}

}