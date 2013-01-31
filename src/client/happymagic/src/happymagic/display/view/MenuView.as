package happymagic.display.view 
{
	import com.greensock.TweenMax;
	import flash.display.DisplayObject;
	import flash.display.SimpleButton;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import happyfish.display.view.UISprite;
	import happyfish.events.ModuleEvent;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.module.interfaces.IModule;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.ModuleMvType;
	import happyfish.manager.mouse.MouseManager;
	import happyfish.manager.SoundEffectManager;
	import happymagic.display.view.itembox.events.ItemBoxEvent;
	import happymagic.display.view.itembox.ItemBoxView;
	import happymagic.display.view.magicBook.CompoundTotalView;
	import happymagic.display.view.magicBook.MixMagicView;
	import happymagic.display.view.roomUp.RoomUpView;
	import happymagic.display.view.student.StudentListView;
	import happymagic.manager.PublicDomain;
	//import happymagic.display.view.switchCrystal.PutSwitchView;
	//import happymagic.display.view.switchCrystal.SwitchView;
	import happymagic.display.view.worldMap.WorldMap;
	import happymagic.events.ActionStepEvent;
	import happymagic.events.MagicBookEvent;
	import happymagic.events.MagicClassBookEvent;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.mouse.MagicMouseIconType;
	import happymagic.model.control.TakeResultVoControl;
	import xrope.HLineLayout;
	/**
	 * ...
	 * @author jj
	 */
	public class MenuView extends UISprite
	{
		public static const TYPE_HOME:uint=1;
		public static const TYPE_FRIEND_HOME:uint=2;
		private var btw:Number=5;
		private var _iview:menuBarUi;
		private var layer:HLineLayout;
		private var leftBtns_self:Array;
		private var leftBtns_selfOtherScene:Array;
		private var leftBtns_friend:Array;
		private var _hasNewMagic:Boolean;
		
		public function MenuView() 
		{
			super();
			DisplayManager.menuView = this;
			
			_view = new menuBarUi();
			_iview = _view as menuBarUi;
			_view.addEventListener(MouseEvent.CLICK, clickFun, true);
			
			layer = new HLineLayout(_view, -325, -4, 660, 55, "L",10,true);
			layer.useBounds = true;
			
			leftBtns_self = [_iview.classBtn,_iview.mixBtn, _iview.diyBtn, _iview.magicBookMc, _iview.itemBtn,_iview.shopBtn, _iview.diaryBtn];
			leftBtns_friend = [_iview.classBtn, _iview.itemBtn];
			leftBtns_selfOtherScene = [_iview.classBtn, _iview.magicBookMc, _iview.itemBtn,_iview.shopBtn, _iview.diaryBtn];
			
			//EventManager.getInstance().addEventListener(MagicClassBookEvent.SHOW_EVENT, showMagicClassBook);
			ModuleManager.getInstance().addEventListener(ModuleEvent.MODULE_CLOSE, moduleClose);
			EventManager.getInstance().addEventListener(SceneEvent.START_DIY, diyStart);
			EventManager.getInstance().addEventListener(SceneEvent.DIY_FINISHED, diyFinished);
			EventManager.getInstance().addEventListener(SceneEvent.DIY_CANCELDIY, diyFinished);
			
			setType();
		}
		
		
		
		public function setType():void {
			
			
			if (DataManager.getInstance().isSelfScene) 
			{
				if (DataManager.getInstance().curSceneUser.currentSceneId == PublicDomain.getInstance().getVar("defaultSceneId")) 
				{
					sortBtn(leftBtns_self);
				}else {
					sortBtn(leftBtns_selfOtherScene);
				}
				_iview.goHomeBtn.visible = false;
				_iview.worldMapBtn.visible = true;
				
			}else {
				sortBtn(leftBtns_friend);
				_iview.goHomeBtn.visible = true;
				_iview.worldMapBtn.visible = false;
			}
		}
		
		/**
		 * 增加自己家中时的主菜单按钮
		 * @param	btn
		 */
		public function addBtnLeftSelf(btn:DisplayObject):void {
			btn.x = 0;
			btn.y = 0;
			leftBtns_self.push(btn);
			setType();
		}
		
		/**
		 * 移除自己家中时的主菜单按钮
		 * @param	btnName	按钮名字
		 */
		public function removeBtnLeftSelf(btnName:String):void {
			for (var i:int = 0; i < leftBtns_self.length; i++) 
			{
				var item:DisplayObject = leftBtns_self[i];
				if (item.name==btnName) 
				{
					leftBtns_self.splice(i, 1);
					setType();
					return;
				}
			}
		}
		
		private function sortBtn(arr:Array):void {
			var i:int;
			for (i = 0; i < _iview.numChildren; i++) 
			{
				if (_view.getChildAt(i)===_iview.goHomeBtn || _view.getChildAt(i)===_iview.worldMapBtn) 
				{
					
				}else {
					_view.getChildAt(i).visible = false;
				}
			}
			
			layer.removeAll();
			var lastBtn:DisplayObject;
			for (i = 0; i < arr.length; i++) 
			{
				if (arr[i]) 
				{
					arr[i].visible = true;
					layer.add(arr[i]);
					layer.layout();
					TweenMax.to(arr[i], .5, { x:arr[i].x } );
				}
			}
		}
		
		/**
		 * 返回指定按钮的全局坐标
		 * @return
		 */
		public function getBtnPoint(btnName:String):Point {
			if (_iview[btnName]) 
			{
				var p:Point = new Point(_iview[btnName].x, _iview[btnName].y);
				p = _iview.localToGlobal(p);
				return p;
			}
			return null;
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case _iview.diyBtn:
				EventManager.getInstance().dispatchEvent(new SceneEvent(SceneEvent.START_DIY));
				break;
				
				case _iview.goHomeBtn:
				var event:SceneEvent = new SceneEvent(SceneEvent.CHANGE_SCENE);
				event.uid = DataManager.getInstance().currentUser.uid;
				EventManager.getInstance().dispatchEvent(event);
				break;
				
				case _iview.classBtn:
				DisplayManager.uiSprite.closeBottomModule();
				DisplayManager.uiSprite.addModule(ModuleDict.MODULE_USEMAGIC_LIST, ModuleDict.MODULE_USEMAGIC_LIST_CLASS, false, AlginType.BC, 0, 105, 0, 0, ModuleMvType.FROM_BOTTOM);
				EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_MENUCONJURE));
				break;
				
				case _iview.magicBookMc.magicBookBtn:
				EventManager.getInstance().dispatchEvent(new MagicBookEvent(MagicBookEvent.SHOW_MAGICBOOK));
				break;
				
				case _iview.magicBookMc.magicBookBtn_new:
				EventManager.getInstance().dispatchEvent(new MagicBookEvent(MagicBookEvent.SHOW_MAGICBOOK));
				break;
				
				case _iview.worldMapBtn:
					var tmp:WorldMap = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_WORLDMAP, ModuleDict.MODULE_WORLDMAP_CLASS,false, AlginType.CENTER, 0, 10) as WorldMap;
					DisplayManager.uiSprite.setBg(tmp);
				break;
				
				case _iview.itemBtn:
					EventManager.getInstance().dispatchEvent(new ItemBoxEvent(ItemBoxEvent.SHOW_ITEMBOX));
				break;
				
				case _iview.shopBtn:
					DisplayManager.uiSprite.addModule(ModuleDict.MODULE_ITEMSHOP, ModuleDict.MODULE_ITEMSHOP_CLASS);
				break;
				
				case _iview.diaryBtn:
					DisplayManager.uiSprite.addModule(ModuleDict.MODULE_DIARY, ModuleDict.MODULE_DIARY_CLASS);
				break;
				
				//case _iview.roomUpBtn:
					//var roomUpView:RoomUpView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_ROOMUP, ModuleDict.MODULE_ROOMUP_CLASS,false, AlginType.CENTER,0,-70) as RoomUpView;
					//roomUpView.setData(DataManager.getInstance().roomSizeClass);
					//DisplayManager.uiSprite.setBg(roomUpView);
				//break;
				
				case _iview.mixBtn:
					var compoundTotalView:CompoundTotalView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_COMPOUNDTOTAL, ModuleDict.MODULE_COMPOUNDTOTAL_CLASS, false, AlginType.CENTER, 0, 0) as CompoundTotalView;
					compoundTotalView.setData();
					DisplayManager.uiSprite.setBg(compoundTotalView);					
					EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_MENUMIXCLICK));
					
				break;
				
				//case _iview.studentsBtn:
					//var studentlistname:StudentListView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_STUDENTLISTINFO, ModuleDict.MODULE_STUDENTLISTINFO_CLASS) as StudentListView;
					//studentlistname.setData(DataManager.getInstance().studentStates);
					//DisplayManager.uiSprite.setBg(studentlistname);
				//break;
			}
		}
		
		private function moduleClose(e:ModuleEvent):void 
		{
			switch (e.moduleName) 
			{
				case "magicbox":
				//ModuleManager.getInstance().showModule(name);
				break;
			}
		}
		
		
		private function showMagicClassBook(e:MagicClassBookEvent):void 
		{
			//隐藏自己
			ModuleManager.getInstance().closeModule(name);
		}
		
		private function diyFinished(e:SceneEvent):void 
		{
			ModuleManager.getInstance().showModule(name);
		}
		
		private function diyStart(e:SceneEvent):void 
		{
			//隐藏自己
			ModuleManager.getInstance().closeModule(name);
		}
		
		public function set hasNewMagic(value:Boolean):void {
			_hasNewMagic = value;
			if (_hasNewMagic) {
				_iview.magicBookMc.gotoAndStop(2);
			}else {
				_iview.magicBookMc.gotoAndStop(1);
			}
		}
		
		public function get iview():menuBarUi 
		{
			return _iview;
		}
	}

}