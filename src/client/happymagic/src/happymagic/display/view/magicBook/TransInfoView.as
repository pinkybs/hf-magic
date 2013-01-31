package happymagic.display.view.magicBook 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import flash.utils.setTimeout;
	import happyfish.cacher.CacheSprite;
	import happyfish.display.ui.Tooltips;
	import happyfish.display.view.IconView;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.mouse.MouseManager;
	import happyfish.utils.DateTools;
	import happyfish.utils.display.BtnStateControl;
	import happyfish.utils.display.CameraSharkControl;
	import happyfish.utils.display.FiltersDomain;
	import happyfish.utils.display.McShower;
	import happyfish.utils.display.ScaleControl;
	import happyfish.utils.HtmlTextTools;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.ui.DecorIconView;
	import happymagic.display.view.ui.NeedCrystalGridView;
	import happymagic.display.view.ui.NeedIconView;
	import happymagic.events.ActionStepEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.LearnTransCommand;
	import happymagic.model.vo.DecorClassVo;
	import happymagic.model.vo.ItemClassVo;
	import happymagic.model.vo.MoneyType;
	import happymagic.model.vo.ResultVo;
	import happymagic.model.vo.TransMagicVo;
	import happymagic.scene.world.control.MouseTransAction;
	import xrope.HLineLayout;
	/**
	 * ...
	 * @author jj
	 */
	public class TransInfoView extends transInfoUi
	{
		public var data:TransMagicVo;
		private var icon:IconView;
		private var getItemLayout:HLineLayout;
		private var needCrystalList:NeedCrystalGridView;
		//private var bubble:CacheSprite;
		
		public function TransInfoView() 
		{
			getItemLayout = new HLineLayout(canGetMc, 0, 0, -1, -1, "L", 5, true);
			
			addEventListener(MouseEvent.CLICK, clickFun, true);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case learnBtn:
				learn();
				break;
			}
		}
		
		private function learn():void
		{
			mouseChildren = false;
			var loader:LearnTransCommand = new LearnTransCommand();
			loader.addEventListener(Event.COMPLETE, learn_complete);
			loader.learn(data.trans_mid);
			
			//startLearnMv();
		}
		
		private function learn_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, learn_complete);
			
			if (e.target.data.result.status==ResultVo.SUCCESS) 
			{
				//增加魔法
				DataManager.getInstance().transMagics.push(data.trans_mid);
				
				startLearnMv();
			}else {
				mouseChildren = true;
			}
		}
		
		private function startLearnMv():void
		{
			var learnMv:McShower = new McShower("learnTransMv", this,null, null, learnMvEnd);
			learnMv.x = 36+65-5;
			learnMv.y = 41+65+5;
			
			CameraSharkControl.shark(DisplayManager.uiSprite, 3,1000,null, 2000);
		}
		
		private function learnMvEnd():void
		{
			
			var tmp:LeanMagicClassMsgView = 
				DisplayManager.uiSprite.addModule(ModuleDict.MODULE_LEARN_MAGIC, ModuleDict.MODULE_LEARN_MAGIC_CLASS,true,AlginType.CENTER,0,-100) as LeanMagicClassMsgView;
			tmp.setData(data);
			
			//更新列表表现
			(parent as TransMagicView).setData();
			
			//更新当前表现
			setData(data);
			
			mouseChildren = true;
		}
		
		public function setData(value:TransMagicVo):void {
			data = value;
			clear();
			if (!data) 
			{
				return;
			}
			
			if (data.content) {
				Tooltips.getInstance().register(contentBtn, data.content,Tooltips.getInstance().getBg("defaultBg"));
			}
			
			nameTxt.text = data.name;
			icon = loadIcon();
			
			timeTxt.text = DateTools.getLostTime(data.time * 1000);
			mpTxt.text = data.mp.toString();
			
			loadGetItemIcons();
			
			if (DataManager.getInstance().hasLearnTrans(data.trans_mid)) 
			{
				//已学会
				hasIcon.visible = true;
				
				learnBtn.visible = 
				needCrystalTitle.visible = false;
			}else {
				//未学会
				icon.filters = [FiltersDomain.grayFilter];
				
				hasIcon.visible = false;
				learnBtn.visible = 
				needCrystalTitle.visible = true;
				
				needCrystalList = new NeedCrystalGridView(new Rectangle( -16, 261, 227, 31));
				needCrystalList.add(needCrystalTitle);
				needCrystalList.setData(data.coin, data.gem);
				needCrystalList.layout();
				addChild(needCrystalList);
				

				
				if (DataManager.getInstance().getEnoughCrystal(data.coin,data.gem) && DataManager.getInstance().currentUser.level>=data.needLevel) 
				{
					BtnStateControl.setBtnState(learnBtn, true);
				}else {
					BtnStateControl.setBtnState(learnBtn, false);
				}
			}
			
		}
		
		/**
		 * 显示可以获得的奖励
		 */
		private function loadGetItemIcons():void
		{
			clearGetItemIcons();
			var i:int;
			//装饰物
			if (data.decorId) 
			{
				var tmpDecor:DecorClassVo;
				var tmpDecorIcon:NeedIconView;
				for (i = 0; i < data.decorId.length; i++) 
				{
					tmpDecor = DataManager.getInstance().getDecorClassByDid(data.decorId[i][0]);
					tmpDecorIcon = new NeedIconView(30,21,false);
					
					tmpDecorIcon.setData(tmpDecor, data.decorId[i][1]);
					canGetMc.addChild(tmpDecorIcon);
					getItemLayout.add(tmpDecorIcon);
				}
				
				getItemLayout.layout();
			}
			
			
			
			//道具
			if (data.itemId) 
			{
				var tmpItem:ItemClassVo
				var tmpItemIcon:NeedIconView;
				for (i = 0; i < data.itemId.length; i++) 
				{
					tmpItem = DataManager.getInstance().getItemClassByIid(data.itemId[i][0]);
					tmpItemIcon = new NeedIconView(30, 21, false);
					tmpItemIcon.setData(tmpItem, data.itemId[i][1]);
					getItemLayout.add(tmpItemIcon);
				}
			}
			
			
			getItemLayout.layout();
		}
		
		private function clearGetItemIcons():void
		{
			getItemLayout.removeAll();
			while (canGetMc.numChildren>0) 
			{
				canGetMc.removeChildAt(0);
			}
			
		}
		
		private function loadIcon():IconView
		{
			icon = new IconView(85, 85, new Rectangle(25.5, 0, 133, 133));
			addChild(icon);
			icon.setData(data.class_name);
			
			return icon;
		}
		
		public function clear():void {
			
			if (icon) 
			{
				removeChild(icon);
			}
			
			if (needCrystalList) 
			{
				removeChild(needCrystalList);
				needCrystalList = null;
			}
			
			clearGetItemIcons();
		}
		
	}

}