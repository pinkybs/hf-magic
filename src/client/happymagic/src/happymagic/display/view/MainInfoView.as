package happymagic.display.view 
{
	import br.com.stimuli.loading.loadingtypes.LoadingItem;
	import com.greensock.TweenLite;
	import com.greensock.TweenMax;
	import flash.display.DisplayObject;
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.geom.Point;
	import flash.utils.Timer;
	import happyfish.display.ui.FaceView;
	import happyfish.display.view.PerBarView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.actModule.ActModuleManager;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.module.ModuleManager;
	import happyfish.utils.DateTools;
	import happymagic.display.view.levelUpgrade.LevelUpgradeView;
	import happymagic.display.view.magicBook.MixMagicView;
	import happymagic.display.view.maxMp.MaxMpView;
	import happymagic.display.view.student.StudentFruitView;
	import happymagic.display.view.student.StudentListView;
	import happymagic.events.DataManagerEvent;
	import happymagic.events.MagicClassBookEvent;
	import happymagic.events.MainInfoEvent;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.PublicDomain;
	import happymagic.model.command.LoadUserInfoCommand;
	import happymagic.model.command.TestCommand;
	import happymagic.model.MagicJSManager;
	import happymagic.model.vo.AvatarVo;
	import happymagic.model.vo.LevelInfoVo;
	import happymagic.model.vo.RoomLevelVo;
	import happymagic.model.vo.StudentStateVo;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.control.RoomUpgradeMvCommand;
	import happymagic.scene.world.MagicWorld;
	
	/**
	 * ...
	 * @author jj
	 */
	public class MainInfoView extends UISprite
	{
		private var diying:Boolean;
		private var expBar:PerBarView;
		private var magicBar:PerBarView;
		private var roomExpBar:PerBarView;
		private var diyingUserData:UserVo;
		private var mpHealTimer:Timer;
		private var mpHealTime:Number;
		
			
		private var mpHealNeedTime:Number;
		private var mpAddNum:uint = 100;
		private var iview:mainInfoUi;
		private var stateCheckTimer:Timer;
		private var userFace:FaceView;
		
		
		public function MainInfoView() 
		{
			super();
			_view = new mainInfoUi();
			_view.addEventListener(Event.ADDED_TO_STAGE, bodyAddToStage);
			
			iview = _view as mainInfoUi;
			
			iview.coinFlashMc.alpha = 
			iview.gemFlashMc.alpha = 
			iview.expFlashMc.alpha = 
			iview.magicFlashMc.alpha = 0;
			
			//iview.expFlashMc.buttonMode=
			//iview.roomExpFlashMc.buttonMode = true;
			
			
			iview.addEventListener(MouseEvent.CLICK, clickFun, true);
			iview.levelTxt.mouseEnabled = 
			//iview.roomLevelTxt.mouseEnabled =
			iview.deskNumTxt.mouseEnabled = 
			iview.studentNumTxt.mouseEnabled = false;
			expBar = new PerBarView(_view.expBar, _view.expBar.width);
			//roomExpBar = new PerBarView(iview.roomExpBar, iview.roomExpBar.width);
			magicBar = new PerBarView(_view.magicBar, _view.magicBar.width);
			
			expBar.minW = 10;
			//roomExpBar.minW = 10;
			magicBar.minW = 18;
			
			EventManager.getInstance().addEventListener(SceneEvent.START_DIY, diyStart);
			EventManager.getInstance().addEventListener(SceneEvent.DIY_FINISHED, diyFinished);
			EventManager.getInstance().addEventListener(SceneEvent.DIY_CANCELDIY, diyFinished);
			EventManager.getInstance().addEventListener(MainInfoEvent.RELOAD, reload);
			
			EventManager.getInstance().addEventListener(SceneEvent.SCENE_DATA_COMPLETE, sceneDataComplete);
			EventManager.getInstance().addEventListener(SceneEvent.SCENE_COMPLETE, sceneAllComplete);
			
			EventManager.getInstance().addEventListener(DataManagerEvent.USERINFO_CHANGE, userInfoChange);
			
			//loadData();
			if (DataManager.getInstance().currentUser) 
			{
				initInfo();
				
				startMpHealTimer();
			}
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			if (!DataManager.getInstance().isSelfScene) 
			{
				return;
			}
			switch (e.target) 
			{
				case iview.expFlashBtn:
					 var levelupgradeview:LevelUpgradeView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_LEVELINFO, ModuleDict.MODULE_LEVELINFO_CLASS, false, AlginType.CENTER, 20, -70) as LevelUpgradeView;
					 
					 DisplayManager.uiSprite.setBg(levelupgradeview);
					 
					 var ad:int = DataManager.getInstance().currentUser.level;
					 
					 levelupgradeview.setData(DataManager.getInstance().getLevelInfo(DataManager.getInstance().currentUser.level + 1), LevelUpgradeView.PlayerLevelN);					     
				break;
				
				case iview.magicFlashBtn:
					 var maxmpview:MaxMpView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_MAXMP, ModuleDict.MODULE_MAXMP_CLASS, false, AlginType.CENTER, 10, -70) as MaxMpView;
					 
					 DisplayManager.uiSprite.setBg(maxmpview);
					 
					 maxmpview.setData(DataManager.getInstance().getRoomLevel(DataManager.getInstance().currentUser.roomLevel + 1), MaxMpView.MAXMPNEXTLEVELUP);
				break;
				
				case iview.studentNumBtn:
				   // var  test:int = DataManager.getInstance().currentUser.roomLevel;			
				    var studentlistname:StudentListView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_STUDENTLISTINFO, ModuleDict.MODULE_STUDENTLISTINFO_CLASS) as StudentListView;
					
					DisplayManager.uiSprite.setBg(studentlistname);
					//var asfdsf:Array = DataManager.getInstance().studentStates;
					studentlistname.setData(DataManager.getInstance().studentStates);
					//new TestCommand().test("data/testLevelup.txt");
				break;
				
				case iview.deskNumBtn:
					//EventManager.getInstance().showSysMsg("testest",1,-1,testFun);
					//new RoomUpgradeMvCommand().sceneLevelUpMv();
				    //new TestCommand().test("data/levelUp.txt");
				    //new TestCommand().test("data/story.txt");
					//new RoomUpgradeMvCommand().sceneLevelUpMv();
					//EventManager.getInstance().dispatchEvent(new SceneEvent(SceneEvent.WALL_COMPLETE));
					
					//var giftActact:ActVo = DataManager.getInstance().getActByName("giftact");
					//var giftActVoloadingitem:LoadingItem = ActModuleManager.getInstance().addActModule(giftActact);
					//var signAwardact:ActVo = DataManager.getInstance().getActByName("signAct");
					//var signAwardVoloadingitem:LoadingItem = ActModuleManager.getInstance().addActModule(signAwardact);
				break;
				
				case iview.goPayBtn:
					MagicJSManager.getInstance().goPay();
				break;
			}
		}
		
		private function sceneAllComplete(e:SceneEvent):void 
		{
			initInfo();
		}
		
		private function sceneDataComplete(e:SceneEvent):void 
		{
			//场景数据更改,重新刷新数据
			initInfo();
		}
		
		private function startMpHealTimer():void
		{
			mpHealTimer = new Timer(1000);
			mpHealTimer.addEventListener(TimerEvent.TIMER, mpHealFun);
			
			checkMpTimer();
		}
		
		private function mpHealFun(e:TimerEvent):void 
		{
			DataManager.getInstance().curSceneUser.replyMp_time--;
			if (DataManager.getInstance().curSceneUser.replyMp_time<=0) 
			{
				//DataManager.getInstance().curSceneUser.mp += Math.ceil(DataManager.getInstance().curSceneUser.replyMpPer/100 * DataManager.getInstance().curSceneUser.max_mp);
				DataManager.getInstance().curSceneUser.mp += DataManager.getInstance().curSceneUser.replyMpPer;
				DataManager.getInstance().curSceneUser.replyMp_time = DataManager.getInstance().curSceneUser.replyMpTime;
				initInfo();
				
				
			}
			
			_view.mpHealTimeTxt.text = DateTools.getLostTime(DataManager.getInstance().curSceneUser.replyMp_time * 1000, true
				,LocaleWords.getInstance().getWord("day")
				,LocaleWords.getInstance().getWord("hour")
				,LocaleWords.getInstance().getWord("minutes")
				,LocaleWords.getInstance().getWord("second")
				) + LocaleWords.getInstance().getWord("mpHealTips", DataManager.getInstance().curSceneUser.replyMpPer.toString());
		}
		
		/**
		 * 根据TYPE返回该值在界面上的位置
		 * @param	valueType
		 * @return
		 */
		public function getValuePosition(valueType:uint):Point {
			var p:Point;
			
			switch (valueType) 
			{
				case PiaoMsgType.TYPE_COIN:
					//TODO 还没有UI,暂用RED
					p = new Point(iview.coinNumTxt.x+30, iview.coinNumTxt.y+10);
				break;
				
				case PiaoMsgType.TYPE_GEM:
					p = new Point(iview.gemNumTxt.x+30, iview.gemNumTxt.y+10);
				break;
				
				case PiaoMsgType.TYPE_EXP:
					p = new Point(iview.expTxt.x+20, iview.expTxt.y+10);
				break; 
				
				case PiaoMsgType.TYPE_MAGIC:
					p = new Point(iview.magicTxt.x+20, iview.magicTxt.y+10);
				break;
				
				case PiaoMsgType.TYPE_ROOM_EXP:
					p = new Point(iview.magicTxt.x+20, iview.magicTxt.y+10);
					//p = new Point(iview.roomExpTxt.x+20, iview.roomExpTxt.y+10);
				break;
				
				case PiaoMsgType.TYPE_MAX_MAGIC:
					p = new Point(iview.magicTxt.x+20, iview.magicTxt.y+10);
				break;
				
				default:
				return null;
				break;
			}
			
			p = iview.localToGlobal(p);
			return p;
		}
		
		
		/**
		 * 修改用户自己的信息,并可以飘屏显示
		 * @param	coin
		 * @param	gem
		 * @param	exp
		 * @param	mp
		 * @param	piao	是否要显示飘屏,位置为当前鼠标位置
		 * @param	showPoint	显示的位置,如不传就出现在当前鼠标位置
		 */
		public function changeUserInfo(coin:int,gem:int,exp:int,mp:int,maxMp:int,piao:Boolean=false,showPoint:Point=null):void {
			
			
			//如果不显示飘,直接更新数据
			//如果飘屏,就会在飘屏完成时改变数据
			if (!piao) 
			{
				changeCrystalAndGem(coin,gem);
				changeExpAndMp(exp, mp,maxMp);
			}
			
			initInfo();
			
			//通知飘屏
			if (piao) 
			{
				if (mp) 
				{
					changeExpAndMp(0, mp, 0);
					if (DataManager.getInstance().isSelfScene) 
					{
						if (diying) 
						{
							initLeft(diyingUserData);
						}else {
							initLeft(DataManager.getInstance().currentUser);
						}
						
					}else {
						initLeft(DataManager.getInstance().curSceneUser);
					}
				}
				
				var msgs:Array = [[PiaoMsgType.TYPE_COIN, coin],
				[PiaoMsgType.TYPE_GEM, gem],
				[PiaoMsgType.TYPE_EXP, exp],
				//[PiaoMsgType.TYPE_MAGIC, mp],
				[PiaoMsgType.TYPE_ROOM_EXP,maxMp]
				];
				var px:Number;
				var py:Number;
				if (showPoint) 
				{
					px = showPoint.x;
					py = showPoint.y;
				}else {
					px = _view.stage.mouseX;
					py = _view.stage.mouseY;
				}
				var event:PiaoMsgEvent = new PiaoMsgEvent(PiaoMsgEvent.SHOW_PIAO_MSG, msgs,px,py);
				EventManager.getInstance().dispatchEvent(event);
			}
		}
		
		private function changeCrystalAndGem(coin:int=0,gem:int=0):void {
			if (diying) 
			{
				diyingUserData.coin += coin;
				diyingUserData.gem += gem;
				
			}else {
				DataManager.getInstance().changeCurUserInfo(coin,gem);
			}
		}
		
		public function diyingUserLevelUp():void {
			diyingUserData.level++;
			
			var nextlevel:LevelInfoVo = DataManager.getInstance().getLevelInfo(diyingUserData.level + 1);
			var tmplevel:LevelInfoVo = DataManager.getInstance().getLevelInfo(diyingUserData.level);
			diyingUserData.max_exp = nextlevel.max_exp;
			diyingUserData.max_mp = tmplevel.magic_limit + (DataManager.getInstance().worldState.world as MagicWorld).getAddMaxMp();
			diyingUserData.mp = diyingUserData.max_mp;
		}
		
		public function diyingRoomLevelUp(upNum:uint):void {
			diyingUserData.roomLevel+=upNum;
				
			//修改当前最大学生数和最大课桌数
			diyingUserData.students_limit = DataManager.getInstance().getRoomLevel(diyingUserData.roomLevel).student_limit;
			diyingUserData.desk_limit = DataManager.getInstance().getRoomLevel(diyingUserData.roomLevel).desk_limit;
		}
		
		/**
		 * 修改当前场景主人的EXP和MP改变
		 * 只是改变数值,没有刷新显示
		 * @param	exp
		 * @param	mp
		 */
		private function changeExpAndMp(exp:int,mp:int,maxMp:int):void {
			if (DataManager.getInstance().isSelfScene) 
			{
				
				DataManager.getInstance().changeCurUserInfo(0,0,exp,mp);
				if (diying) 
				{
					diyingUserData.max_mp += maxMp;
				}else {
					DataManager.getInstance().curSceneUser.max_mp += maxMp;
				}
			}else {
				//DataManager.getInstance().changeCurSceneUserInfo(0,0,exp,mp);
				DataManager.getInstance().changeCurUserInfo(0,0,exp,mp);
				
				DataManager.getInstance().curSceneUser.max_mp += maxMp;
			}
		}
		
		/**
		 * 让指定值的表现物闪动一次
		 * @param	type
		 * @param	num
		 */
		public function flashValue(type:uint,num:int):void
		{
			switch (type) 
			{
				case PiaoMsgType.TYPE_COIN:
					
					flashIt(iview.coinFlashMc);
					changeCrystalAndGem(num);
				break;
				
				case PiaoMsgType.TYPE_GEM:
					flashIt(iview.gemFlashMc);
					changeCrystalAndGem(0,num);
				break;
				
				case PiaoMsgType.TYPE_EXP:
					if (DataManager.getInstance().isSelfScene) 
					{
						flashIt(iview.expFlashMc);
						changeExpAndMp(num,0,0);
					}
					
				break;
				
				case PiaoMsgType.TYPE_ROOM_EXP:
					if (DataManager.getInstance().isSelfScene) 
					{
						flashIt(iview.magicFlashMc);
						changeExpAndMp(0, 0, num);
					}
				break;
				
				case PiaoMsgType.TYPE_MAGIC:
					if (DataManager.getInstance().isSelfScene) 
					{
						flashIt(iview.magicFlashMc);
						changeExpAndMp(0, num, 0);
					}
				break;
			}
			
			initInfo();
		}
		
		private function flashIt(target:DisplayObject):void {
			TweenMax.killTweensOf(target,true);
			TweenMax.to(target, .2, { tint:0xffffff, yoyo:true, repeat:1,alpha:1  } );
		}
		
		/**
		 * 用户信息改变事件
		 * @param	e
		 */
		private function userInfoChange(e:DataManagerEvent):void 
		{
			if (e.userChange) 
			{
				//如果有userChange参数，说明需要飘屏显示
				//显示飘屏
				//修改用户数据,并直接飘屏显示
				if (DataManager.getInstance().isSelfScene) 
				{
					changeUserInfo(e.userChange.coin, e.userChange.gem, e.userChange.exp,
								e.userChange.mp,e.userChange.maxMp, e.userChange.piao, e.userChange.showPoint);
				}else {
					changeUserInfo(e.userChange.coin, e.userChange.gem, e.userChange.exp,
								0,0, e.userChange.piao, e.userChange.showPoint);
					changeUserInfo(0, 0, 0, e.userChange.mp, e.userChange.maxMp);
				}
				
				
				
			}else {
				//无userChange参数，说明数据已修改，无需飘屏,直接刷新数据
				initInfo();
			}
		}
		
		private function changeMaxMp(value:int):void {
			if (value!=0) 
			{
				if (diying) 
				{
					diyingUserData.max_mp += value;
				}else {
					//DataManager.getInstance().currentUser.max_mp += value;
					if (DataManager.getInstance().isSelfScene) 
					{
						DataManager.getInstance().curSceneUser.max_mp += value;
					}
				}
				
			}
		}
		
		private function reload(e:MainInfoEvent):void 
		{
			loadData();
		}
		
		private function loadData():void
		{
			var loader:LoadUserInfoCommand = new LoadUserInfoCommand();
			loader.addEventListener(Event.COMPLETE, load_complete);
			loader.load();
		}
		
		private function load_complete(e:Event):void 
		{
			DataManager.getInstance().currentUser = new UserVo().setData(e.target.data.userInfo);
			initInfo();
		}
		
		private function diyStart(e:SceneEvent):void 
		{
			//DIY开始时
			diying = true;
			diyingUserData = DataManager.getInstance().currentUser.clone();
			//ModuleManager.getInstance().closeModule(name);
		}
		
		private function diyFinished(e:SceneEvent):void 
		{
			//diy结束时
			diying = false;
			if (e.type==SceneEvent.DIY_FINISHED) 
			{
				//设置新的用户数据
				DataManager.getInstance().curSceneUser = diyingUserData;
				DataManager.getInstance().currentUser = diyingUserData;
			}else {
				//还原数据
				//清除临时用户信息
				diyingUserData = null;
			}
			initInfo();
			ModuleManager.getInstance().showModule(name);
		}
		
		private function bodyAddToStage(e:Event):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, bodyAddToStage);
			
		}
		
		public function initInfo():void {
			if (DataManager.getInstance().isSelfScene) 
			{
				if (diying) 
				{
					initLeft(diyingUserData);
				}else {
					initLeft(DataManager.getInstance().currentUser);
				}
				
			}else {
				initLeft(DataManager.getInstance().curSceneUser);
			}
			if (diying) 
			{
				initRight(diyingUserData);
			}else {
				initRight(DataManager.getInstance().currentUser);
			}
		}
		
		public function get curUserInfo():UserVo {
			if (DataManager.getInstance().isSelfScene) 
			{
				return DataManager.getInstance().currentUser;
			}else {
				return DataManager.getInstance().curSceneUser;
			}
		}
		
		private function initRight(data:UserVo):void
		{
			iview.coinNumTxt.text = data.coin.toString();
			_view.gemNumTxt.text = data.gem;
			
			if (DataManager.getInstance().curSceneUser.currentSceneId==PublicDomain.getInstance().getVar("defaultSceneId")) 
			{
				iview.studentNumBtn.visible=
				iview.deskNumBtn.visible=
				iview.studentNumTxt.visible=
				iview.deskNumTxt.visible = true;
			}else {
				iview.studentNumBtn.visible=
				iview.deskNumBtn.visible=
				iview.studentNumTxt.visible=
				iview.deskNumTxt.visible = false;
			}
		}
		
		private function initLeft(data:UserVo):void {
			
			if (!userFace) {
				userFace = new FaceView(34);
				userFace.x = 40;
				userFace.y = 21;
				iview.addChild(userFace);
			}
			
			userFace.loadFace(data.face);
			
			var nextlevel:LevelInfoVo = DataManager.getInstance().getLevelInfo(data.level + 1);
			
			_view.levelTxt.text = data.level.toString();
			_view.nameTxt.text = data.name;
			_view.expTxt.text = data.exp + "/" + nextlevel.max_exp;
			//iview.tileTxt.text=DataManager.getInstance().getLevelInfo(data.level).
			
			
			var mp:Number = Math.min(data.mp, data.max_mp);
			_view.magicTxt.text = mp + "/" + data.max_mp;
			
			//检查魔法是否满了
			checkMpTimer();
			
			iview.deskNumTxt.text = DataManager.getInstance().getDeskInRoom() +"/" + DataManager.getInstance().curSceneUser.desk_limit;
			
			//iview.popTxt.text = data.popularity.toString();
			
			expBar.maxValue = nextlevel.max_exp;
			magicBar.maxValue = data.max_mp;
			
			expBar.setData(data.exp);
			magicBar.setData(data.mp);
			
			//iview.roomLevelTxt.text = data.roomLevel.toString();
			//var roomLevelVo:RoomLevelVo = DataManager.getInstance().getRoomLevel(data.roomLevel);
			//if (roomLevelVo) 
			//{
				//roomExpBar.maxValue = roomLevelVo.needMaxMp;
				//roomExpBar.setData(data.max_mp);
				//iview.roomExpTxt.text = data.max_mp + "/" + roomLevelVo.needMaxMp;
			//}
			
			_view.studentNumTxt.text = DataManager.getInstance().getStudentsCountInRoom() + "/" + data.students_limit;
			
			startStateTimer();
			
		}
		
		private function checkMpTimer():void 
		{
			iview.mpHealTimeTxt.visible = false;
			//判断魔法是否满了,如果满了,就停止魔法回复进程,如果未满并没有进程中,就重启进程
			var tmpdata:UserVo;
			if (DataManager.getInstance().isSelfScene) 
			{
				if (diying) 
				{
					tmpdata = diyingUserData;
				}else {
					tmpdata = DataManager.getInstance().currentUser;
				}
				
			}else {
				tmpdata = DataManager.getInstance().curSceneUser;
			}
			var mp:int = tmpdata.mp;
			var max_mp:uint = tmpdata.max_mp;
			if (mp>=max_mp) 
			{
				if (mpHealTimer) 
				{
					mpHealTimer.stop();
					iview.mpHealTimeTxt.visible = false;
				}
			}else {
				if (mpHealTimer) 
				{
					if (!mpHealTimer.running) 
					{
						DataManager.getInstance().curSceneUser.replyMp_time = DataManager.getInstance().curSceneUser.replyMpTime;
						mpHealTimer.start();
						
					}
					iview.mpHealTimeTxt.visible = true;
				}
			}
		}
		
		private function startStateTimer():void
		{
			if (!stateCheckTimer) 
			{
				stateCheckTimer = new Timer(1000);
				stateCheckTimer.addEventListener(TimerEvent.TIMER, stateCheckFun);
			}
			
			if(!stateCheckTimer.running) stateCheckTimer.start();
		}
		
		private function stopStateTimer():void {
			if (stateCheckTimer) 
			{
				stateCheckTimer.stop();
			}
		}
		
		private function stateCheckFun(e:TimerEvent):void 
		{
			//变化术
			if (DataManager.getInstance().currentUser.trans_time > 0) {
				DataManager.getInstance().currentUser.trans_time--;
			}
			
			if (!DataManager.getInstance().isSelfScene) 
			{
				if (DataManager.getInstance().curSceneUser.trans_time > 0) {
					DataManager.getInstance().curSceneUser.trans_time--;
				}
			}
			
			//变化术状态
			//trace("变化术状态curSceneUser",DataManager.getInstance().curSceneUser.trans_time);
			if (DataManager.getInstance().curSceneUser.trans_time>0) 
			{
				iview.transStateIcon.visible = true;
				iview.transStateIcon.txt.text = DateTools.getLostTime(DataManager.getInstance().curSceneUser.trans_time * 1000);
				
			}else {
				iview.transStateIcon.visible = false;
				
			}
			
		}
		
		
	}

}