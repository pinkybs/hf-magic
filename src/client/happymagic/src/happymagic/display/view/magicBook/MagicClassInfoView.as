package happymagic.display.view.magicBook 
{
	import adobe.utils.CustomActions;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.ui.Tooltips;
	import happyfish.display.view.IconView;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.SoundEffectManager;
	import happyfish.utils.DateTools;
	import happyfish.utils.display.BtnStateControl;
	import happyfish.utils.display.CameraSharkControl;
	import happyfish.utils.display.FiltersDomain;
	import happyfish.utils.display.McShower;
	import happyfish.utils.HtmlTextTools;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.ui.NeedCrystalGridView;
	import happymagic.events.MagicBookEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.LearnMagicClassCommand;
	import happymagic.model.vo.MagicClassVo;
	import happymagic.model.vo.MagicType;
	import happymagic.model.vo.MoneyType;
	import happymagic.model.vo.ResultVo;
	/**
	 * ...
	 * @author jj
	 */
	public class MagicClassInfoView extends magicClassInfoUi
	{
		private var data:MagicClassVo;
		private var icon:IconView;
		private var needCrystalList:NeedCrystalGridView;
		
		public function MagicClassInfoView() 
		{
			clear();
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
			stage.mouseChildren = false;	
			mouseChildren = false;
			var command:LearnMagicClassCommand = new LearnMagicClassCommand();
			command.addEventListener(Event.COMPLETE, learn_complete);
			command.learn(data.magic_id);
			
			//startLearnMv();
		}
		
		private function learn_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, learn_complete);
		
			if (e.target.data.result.status==ResultVo.SUCCESS) 
			{
				//增加魔法
				DataManager.getInstance().magicList.push(data);
				
				//表现学习动画
				startLearnMv();
				
				//音效
				SoundEffectManager.getInstance().playSound(new sound_magiclvup());
			}else {
				mouseChildren = true;
			}
		}
		
		private function startLearnMv():void
		{
			var learnClass:Class;
			
			switch (data.magic_type) 
			{
				case MagicType.RED:
					learnClass = learnMagicClass_red;
				break;
				
				case MagicType.BLUE:
					learnClass = learnMagicClass_blue;
				break;
				
				case MagicType.GREEN:
					learnClass = learnMagicClass_green;
				break;
			}
			var learnMv:McShower = new McShower(learnClass, this,null, null, learnMvEnd);
			learnMv.x = icon.x;
			learnMv.y = icon.y;
			
			//CameraSharkControl.shark(DisplayManager.sceneSprite, 3,1000,null, 2000);
			CameraSharkControl.shark(DisplayManager.uiSprite, 3,1000,null, 2000);
		}
		
		private function learnMvEnd():void
		{
			
			var tmp:LeanMagicClassMsgView = DisplayManager.uiSprite.addModule(
				ModuleDict.MODULE_LEARN_MAGIC, ModuleDict.MODULE_LEARN_MAGIC_CLASS,false,
				AlginType.CENTER,0,-100) as LeanMagicClassMsgView;
			tmp.setData(data);
				
			
			//更新列表
			(parent as MagicClassBookView).setData((parent as MagicClassBookView).type);
			
			setData(data);
			
			mouseChildren = true;
			stage.mouseChildren = true;			
		}
		
		
		
		public function setData(value:MagicClassVo):void {
			clear();
			data = value;
			if (!data) 
			{
				return;
			}
			nameTxt.text = data.name;
			timeTxt.text = DateTools.getLostTime(data.time * 1000);
			
			mpTxt.text = data.mp.toString();
			
			if (data.content) {
				Tooltips.getInstance().register(contentBtn, data.content,Tooltips.getInstance().getBg("defaultBg"));
			}
			
			
			getCrystalIcon.gotoAndStop(MoneyType.COIN);
			getCrystalTxt.text = data.coin.toString();
			
			
			icon=loadIcon();
			
			//是否已学
			if (DataManager.getInstance().hasLearnMagicClass(data.magic_id)) 
			{
				//已学
				hasIcon.visible = true;
				learnBtn.visible = false;
				needCrystalTitle.visible = false;
				
			}else {
				//未学
				icon.filters = [FiltersDomain.grayFilter];
				
				var hasLevel:Boolean;
				//是否等级到能学
				if (data.need_level>DataManager.getInstance().currentUser.level) 
				{
					hasLevel = false;
				}else {
					hasLevel = true;
				}
				learnBtn.visible = 
				needCrystalTitle.visible = true;
				
				needCrystalList = new NeedCrystalGridView(new Rectangle( -16, 261, 227, 31));
				needCrystalList.add(needCrystalTitle);
				needCrystalList.setData(data.learn_coin, data.learn_gem);
				needCrystalList.layout();
				addChild(needCrystalList);
				
				if (hasLevel && DataManager.getInstance().getEnoughCrystal(data.learn_coin, data.learn_gem)) {
					BtnStateControl.setBtnState(learnBtn, true);
				}else {
					BtnStateControl.setBtnState(learnBtn, false);
				}
			}
			
		}
		
		private function loadIcon():IconView
		{
			icon = new IconView(85, 85, new Rectangle(25.5, 0, 133, 133));
			icon.setData(data.class_name);
			addChild(icon);
			return icon;
		}
		
		private function clear():void {
			getCrystalTxt.text = "";
			timeTxt.text = "";
			
			if (icon) 
			{
				removeChild(icon);
			}
			
			BtnStateControl.setBtnState(learnBtn, false);
			
			getCrystalIcon.gotoAndStop(1);
			
			if (needCrystalList) 
			{
				removeChild(needCrystalList);
				needCrystalList = null;
			}
			
			
			learnBtn.visible = 
			needCrystalTitle.visible = 
			hasIcon.visible = false;
		}
		
	}

}