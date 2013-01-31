package happymagic.display.view.magicBook 
{
	import com.friendsofed.isometric.Point3D;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import happyfish.display.ui.RoundLayout;
	import happyfish.display.view.IconView;
	import happyfish.events.ModuleEvent;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.module.interfaces.IModule;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.SoundEffectManager;
	import happyfish.utils.display.BtnStateControl;
	import happymagic.display.view.itembox.BuyItemMsgView;
	import happymagic.display.view.magicBook.CrystalNumView;
	import happymagic.display.view.magicBook.event.MixMagicEvent;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.ui.DecorIconView;
	import happymagic.display.view.ui.ItemIconView;
	import happymagic.display.view.ui.NeedIconView;
	import happymagic.events.ActionStepEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.MixCommand;
	import happymagic.model.vo.DecorClassVo;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.ItemClassVo;
	import happymagic.model.vo.MagicType;
	import happymagic.model.vo.MixMagicVo;
	import happymagic.model.vo.MoneyType;
	import happymagic.model.vo.ResultVo;
	/**
	 * ...
	 * @author jj
	 */
	public class MixMagicInfoView extends MixMagicInfoUi
	{
		public var data:MixMagicVo;		
		private var buyNum:uint;
		private var needListLayout:RoundLayout;
		private var needListMc:Sprite;
		private var needItem_coin:NeedIconView;
		private var needItem_gem:NeedIconView;
		private var icon:IconView;
		private var decor:DecorClassVo;
		private var mc:MixMagicViewUi;
		private var mixbuttonview:MixMagicButtonView
		public static const MIX_COMPLETE:String = "mixcomplete";
		public function MixMagicInfoView(_mc:MixMagicViewUi) 
		{
			mc = _mc;
			needListLayout = new RoundLayout(68);
			needListLayout.setRotation(180);
			needListMc = new Sprite();
			needListMc.x = 85;
			needListMc.y = 120;
			addChild(needListMc);
			needListMc.addEventListener(MouseEvent.CLICK, needIconSelect, true);
			
			numInput.restrict = "0-9";
			numInput.maxChars = 4;
			numInput.addEventListener(Event.CHANGE, textInput);
			this.addEventListener(MouseEvent.CLICK, clickFun, true);
			buyNum = 1;
			
			clear();
		}
		
		private function needIconSelect(e:MouseEvent):void 
		{			
			var item:NeedIconView = e.target as NeedIconView;
			
			if (item.data is uint)
			{
				return;
			}
						
            var i:int = 0;
            var giftsVoArray:Array;
			
		    if (item) 
			{		
				 var p:Point = new Point(item.x, item.y);
				 p = item.parent.localToGlobal(p);
				 if (mixbuttonview!=null)
				 {
				     if (this.contains(mixbuttonview))
				     {
					   removeChild(mixbuttonview);
				     }					 
				 }

				 mixbuttonview = new MixMagicButtonView();
				 addChild(mixbuttonview);
				 p = globalToLocal(p);
				 mixbuttonview.x = p.x;
				 mixbuttonview.y = p.y-15;
				 var temp:Array = new Array();
				 if (item.data is ItemClassVo)
				 {
					 temp.push(MixMagicButtonView.BUY);
					 temp.push(MixMagicButtonView.CANCEL);
					 
					giftsVoArray = DataManager.getInstance().getVar("gifts");
					 
					 for (i = 0; i < giftsVoArray.length; i++ )
					 {
						 if (giftsVoArray[i].id == item.data.i_id)
						 {
					         temp.push(MixMagicButtonView.BLAG);							 
						 }
					 }
				 }
				 else if (item.data is DecorClassVo)
				 {   
					 temp.push(MixMagicButtonView.CANCEL);
					 temp.push(MixMagicButtonView.MIX);
					 
					 giftsVoArray = DataManager.getInstance().getVar("gifts");
					 
					 if (giftsVoArray) 
					 {
						 for (i = 0; i < giftsVoArray.length; i++ )
						 {
							if (giftsVoArray[i].id == item.data.d_id)
							{
								temp.push(MixMagicButtonView.BLAG);
							}
						 }
					 }
					 
					
				 }
				 
				 mixbuttonview.setData(temp,this,item.data);
			}	
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			trace("clickFun",e.target.name);
			switch (e.target) 
			{
				case addBtn:
				setBuyNum(buyNum+1);
				break;
				
				case subBtn:
				if (buyNum-1>0) 
				{
					setBuyNum(buyNum-1);
				}
				break;
			}
		}
		
		public function startMix():void
		{
			stage.mouseChildren = false;
			
			var tmpP:Point = new Point(90, 250);
			tmpP = localToGlobal(tmpP);
			tmpP = needListMc.globalToLocal(tmpP);
			var mixMvControl:MixMvControl = new MixMvControl(needListLayout, icon, tmpP, mixMv_complete,mc);
			mixMvControl.start();
		}
		
		private function mixMv_complete():void
		{
			mix();
		}
		
		private function mix():void
		{
			var command:MixCommand = new MixCommand();
			command.addEventListener(Event.COMPLETE, mix_complete);
			command.mix(data.mix_mid, buyNum);
		}
		
		private function mix_complete(e:Event):void 
		{
			stage.mouseChildren = true;
			e.target.removeEventListener(Event.COMPLETE, mix_complete);
			var rr:int = e.target.data.result;
			if (e.target.data.result.status==ResultVo.SUCCESS) 
			{
				//弹出提示窗
				var mixMagicResultMsg:MixMagicResultMsgView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_MIXMAGIC_RESULT, ModuleDict.MODULE_MIXMAGIC_RESULT_CLASS,true,
					AlginType.CENTER, 0, -80) as MixMagicResultMsgView;
				mixMagicResultMsg.setData(data, buyNum);
				DisplayManager.uiSprite.setBg(mixMagicResultMsg);
				
				//音效
				SoundEffectManager.getInstance().playSound(new sound_building());
				this.dispatchEvent(new Event(MIX_COMPLETE));
			}
			else
			{
				EventManager.getInstance().showSysMsg("网络有问题");
				setData(data);
			}

			
			
		}
		
		private function textInput(e:Event):void 
		{
			buyNum = Number(numInput.text);
			
			initNeedItems(buyNum);
		}
		
		public function setBuyNum(value:uint):void {
			if (buyNum==value) 
			{
				return;
			}
			
			buyNum = value;
			if (buyNum.toString()!=numInput.text) 
			{
				numInput.text = buyNum.toString();
			}
			
			initNeedItems(buyNum);
			
		}
		
		private function initNeedItems(num:uint):void
		{
			var tmp:NeedIconView;
			var tmpboolean:Boolean = true;
			
			var itemNumberTemp:uint = 0;
			var decorTemp:uint = 0;
			var diamondTemp:uint = 0;
			
			for (var i:int = 0; i < needListLayout.objects.length; i++) 
			{
				tmp = needListLayout.objects[i];
				tmp.setTimes(buyNum);
				if (tmp.data is ItemClassVo)
				{
				    if (tmpboolean)
				    {
					  tmpboolean = EnoughCourse(tmp, buyNum, itemNumberTemp);
					  itemNumberTemp++;
				    }
				}

				if (tmp.data is DecorClassVo)
				{
					if (tmpboolean)
				    {
					tmpboolean = EnoughCourse(tmp, buyNum, decorTemp);
					decorTemp++;
				    }
				}
				
				if (tmp.data is uint)
				{
					if (tmpboolean)
				    {
					tmpboolean = EnoughCourse(tmp, buyNum, diamondTemp);
					diamondTemp++;
				    }
				}

				
			}
			
		    if (tmpboolean)
		    {
				BtnStateControl.setBtnState(mc.hechengBn, true);
			}
			else
			{
				BtnStateControl.setBtnState(mc.hechengBn, false);
			}
			
		}
		
		private function createNeedItems():void {
			
			var enough:Boolean = false;
			
			if (data.coin!=0) 
			{
				needItem_coin = new NeedIconView();
				needItem_coin.setData(MoneyType.COIN, data.coin,buyNum);
				needListLayout.addObjects(needItem_coin);
				needListMc.addChild(needItem_coin);
			}
			
			if (data.gem!=0) 
			{
				needItem_gem = new NeedIconView();
				needItem_gem.setData(MoneyType.GEM, data.gem, buyNum);
				needListLayout.addObjects(needItem_gem);
				needListMc.addChild(needItem_gem);			
			}
			
			
			
			var i:int;
			
			var tmpDecor:DecorClassVo;
			var tmpDecorIcon:NeedIconView;
			if (data.decorId) 
			{
				for (i = 0; i < data.decorId.length; i++) 
				{
					tmpDecor = DataManager.getInstance().getDecorClassByDid(data.decorId[i][0]);
					tmpDecorIcon = new NeedIconView(35,40,true,true);
					tmpDecorIcon.setData(tmpDecor, data.decorId[i][1],buyNum);
					needListMc.addChild(tmpDecorIcon);
					needListLayout.addObjects(tmpDecorIcon);
					
				}
			}
			
			var tmpItem:ItemClassVo;
			var tmpItemIcon:NeedIconView;
			if (data.itemId) 
			{
				for (i = 0; i < data.itemId.length; i++) 
				{
					tmpItem = DataManager.getInstance().getItemClassByIid(data.itemId[i][0]);
					tmpItemIcon = new NeedIconView(35,40,true,true);
					tmpItemIcon.setData(tmpItem, data.itemId[i][1], buyNum);
					needListMc.addChild(tmpItemIcon);
					needListLayout.addObjects(tmpItemIcon);
					
				}
			}
			
			needListLayout.TweenFrom(0, 0);
			
			enough = DataManager.getInstance().getEnoughMix(data);
			
			if (enough) 
			{
				//引导事件
				EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_MIXBUTTON));
				BtnStateControl.setBtnState(mc.hechengBn, true);
			}
			else
			{
				BtnStateControl.setBtnState(mc.hechengBn, false);
			}
		}
		
		public function setData(value:MixMagicVo):void {
			
			data = value;
			
			if (data) 
			{
				visible = true;
			}else {
				visible = false;
				return;
			}
			
			decor = DataManager.getInstance().getDecorClassByDid(data.d_id);
			clear();
			
			nameTxt.text = data.name;
			numInput.text = buyNum.toString();
			
			levelStar.visible = true;
			levelStar.gotoAndStop(decor.level);
			
			createNeedItems();
			
			loadIcon();
		}
		
		private function loadIcon():void
		{
			icon = new IconView(85, 85, new Rectangle(90, 100),true);
			addChildAt(icon,getChildIndex(levelStar));
			
			icon.setData(decor.class_name);
		}
		
		public function clear():void
		{
			levelStar.visible = false;
			levelStar.gotoAndStop(1);
			nameTxt.text = "";
			numInput.text = "";
			buyNum = 1;
			while (needListMc.numChildren>0) 
			{
				needListMc.removeChildAt(0);
			}
			needListLayout.clear();
			
			if (icon) 
			{
				removeChild(icon);
				icon = null;
			}
			
		}
        
		//判断金币是否足够
        private function isEnough(_gem:uint,_buyitem:uint):Boolean
		{
			if (DataManager.getInstance().currentUser.gem >= _buyitem * _gem)
			{
				return true;
			}
			return false;
		}
		
		//判断合成术里的材料是否不够的流程
		private function EnoughCourse(needIconView:NeedIconView,_buynum:uint,i:int):Boolean
		{
			var enough:Boolean;
			// 物品道具 (包括水晶)
			if (needIconView.data is ItemClassVo)
			{
			    enough = DataManager.getInstance().getEnoughItems([[needIconView.data.i_id, data.itemId[i][1] * _buynum]]);
			    if (enough)
			    {
				return true;
			    }
			    else
			    {
				return false;
			    }
				
			}
			
			//装饰物
			if (needIconView.data is DecorClassVo)
			{

			    enough = DataManager.getInstance().getEnoughDecors([[needIconView.data.d_id, data.decorId[i][1] *_buynum]]);
			    if (enough)
			    {
				return true;
			    }
			    else
			    {
				return false;
			    }
			}
			
			//钻石
			if (needIconView.data is uint) 
			{
				enough = isEnough(needIconView.num, _buynum);
				
				if (enough)
				{
				   return true;
				}
				else
				{
				   return false;
				}	
			}
			return false; 
		}
		
	}

}