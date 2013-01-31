package 
{
	import br.com.stimuli.loading.loadingtypes.LoadingItem;
	import com.brokenfunction.json.decodeJson;
	import com.brokenfunction.json.encodeJson;
	import com.greensock.easing.Bounce;
	import flash.display.DisplayObjectContainer;
	import flash.display.Sprite;
	import flash.display.StageAlign;
	import flash.display.StageDisplayState;
	import flash.display.StageScaleMode;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.media.Sound;
	import flash.net.registerClassAlias;
	import flash.system.Security;
	import flash.utils.setTimeout;
	import happyfish.cacher.ClassCache;
	import happyfish.display.ui.Tooltips;
	import happyfish.display.view.UISprite;
	import happyfish.manager.actModule.ActModuleManager;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.ActTipsManager;
	import happyfish.manager.BgMusicManager;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.mouse.MouseManager;
	import happyfish.manager.ShareObjectManager;
	import happyfish.manager.SoundEffectManager;
	import happyfish.manager.SwfURLManager;
	import happyfish.model.UrlConnecter;
	import happyfish.model.vo.GuidesVo;
	import happyfish.scene.camera.CameraControl;
	import happyfish.scene.world.control.IsoPhysicsControl;
	import happyfish.utils.display.TextFieldTools;
	import happymagic.display.control.PiaoMsgControl;
	import happymagic.display.view.diary.DiaryView;
	import happymagic.display.view.friends.FriendsView;
	import happymagic.display.view.itembox.ItemBoxView;
	import happymagic.display.view.itembox.ItemShopView;
	import happymagic.display.view.magic.MagicItemList;
	import happymagic.display.view.magicBook.CompoundTotalView;
	import happymagic.display.view.magicBook.MagicBookView;
	import happymagic.display.view.magicBook.MixMagicView;
	import happymagic.display.view.maxMp.MaxMpView;
	import happymagic.display.view.student.StudentListView;
	import happymagic.model.command.TestCommand;
	import happymagic.model.MagicUrlLoader;
	import happymagic.task.manager.MagicTaskStateManager;
	//import happymagic.display.view.magicBook.MagicTypeSelectView;
	import happymagic.display.view.magicBook.MixMagicResultMsgView;
	import happymagic.display.view.magicClass.TeachMagicView;
	import happymagic.display.view.MainInfoView;
	import happymagic.display.view.MenuView;
	import happymagic.display.view.roomUp.RoomUpView;
	//import happymagic.display.view.switchCrystal.PutSwitchView;
	//import happymagic.display.view.switchCrystal.SwitchHistoryView;
	//import happymagic.display.view.switchCrystal.SwitchView;
	import happymagic.display.view.SysMenuView;
	import happymagic.display.view.task.TaskListView;
	import happymagic.display.view.ui.AwardResultView;
	import happymagic.display.view.worldMap.WorldMap;
	import happymagic.display.view.worldMap.WorldMapSceneIconView;
	import happymagic.display.view.worldMap.WorldMapSceneInfoView;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.mouse.MagicMouseIconType;
	import happymagic.manager.PublicDomain;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.WorldView;
	import happymagic.display.view.edit.BuildingItemList;
	import happymagic.manager.UiManager;
	import happymagic.model.command.enterEditCommand;
	import happymagic.model.command.initCommand;
	import happymagic.model.command.initStaticCommand;
	import happymagic.scene.world.bigScene.NpcChatsView;
	import happymagic.scene.world.MagicState;
	import happymagic.scene.world.MagicView;
	import happymagic.scene.world.MagicWorld;

	
	/**
	 * ...
	 * @author Beck
	 */
	public class HappyMagicMain extends Sprite 
	{
		private var sceneSprite:Sprite;
		private var uiSprite:UiManager;
		private var mouseIconSprite:Sprite;
		private var storyUiSprite:Sprite;
		public function HappyMagicMain():void 
		{
			//Security.allowDomain("*");   
			
			registerClassAlias("TextFieldTools2", TextFieldTools);
			registerClassAlias("ActTipsManager2", ActTipsManager);
			
			registerClassAlias("BuildingItemList2", BuildingItemList);
			registerClassAlias("MagicItemList", MagicItemList);
			
			registerClassAlias("MainInfoView2", MainInfoView);
			registerClassAlias("FriendsView2", FriendsView);
			registerClassAlias("MenuView2", MenuView);
			registerClassAlias("MagicBookView2", MagicBookView);
			registerClassAlias("MixMagicResultMsgView2", MixMagicResultMsgView);
			registerClassAlias("TaskListView2", TaskListView);
			registerClassAlias("WorldMap2", WorldMap);
			registerClassAlias("WorldMapSceneInfoView2", WorldMapSceneInfoView);
			registerClassAlias("ItemBoxView2", ItemBoxView);
			registerClassAlias("ItemShopView2", ItemShopView);
			registerClassAlias("SysMenuView2", SysMenuView);
			registerClassAlias("DiaryView2", DiaryView);
			registerClassAlias("NpcChatsView2", NpcChatsView);
			registerClassAlias("AwardResultView2", AwardResultView);
			registerClassAlias("RoomUpView2", RoomUpView);
			registerClassAlias("TeachMagicView2", TeachMagicView);
			registerClassAlias("StudentListView2", StudentListView);
			registerClassAlias("CompoundTotalView2", CompoundTotalView);
			registerClassAlias("MaxMpView2", MaxMpView);
			encodeJson(new Object());
			
			
			InterfaceURLManager.getInstance().tmpUrls = new Array();
			if (stage) this.ready_startInit();
			else addEventListener(Event.ADDED_TO_STAGE, ready_startInit);
		}
		
		private function ready_startInit(e:Event=null):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, ready_startInit);
			
			//parent.addChild(new Fps());
			
			//默认场景ID
			PublicDomain.getInstance().setVar("defaultSceneId", 1000001);
			
			//ui层容器与管理器
			uiSprite = new UiManager();
			//场景容器
			sceneSprite = new Sprite();
			//手型容器
			mouseIconSprite = new Sprite();
			//幕布容器
			storyUiSprite = new Sprite();
			
			//初始化容器
			addChild(sceneSprite);
			addChild(uiSprite);
			addChild(storyUiSprite);
			addChild(mouseIconSprite);
			
			//uiSprite.showSceneOutMv(startInit);
			
			startInit();
		}
		
		private function startInit(e:Event = null):void {
			
			//初始化UI
			uiSprite.init();
			
			//放入显示管理类
			DisplayManager.uiSprite = this.uiSprite;
			DisplayManager.sceneSprite = this.sceneSprite;
			DisplayManager.storyUiSprite = storyUiSprite;
			DisplayManager.mouseIconSprite = mouseIconSprite;
			
			
			
			//设置全局对齐方式与不缩放
			stage.align = StageAlign.TOP_LEFT;
			stage.scaleMode = StageScaleMode.NO_SCALE;
			
			//镜头控制
			CameraControl.getInstance().init(WorldView.WORLD_WIDTH, WorldView.WORLD_HEIGHT);
			
			//初始化鼠标手型控制器
			MouseManager.getInstance().initManager(stage);
			//MouseManager.getInstance().addMouseIcon(MagicMouseIconType.DEFAULT_HAND, new mouse_default());
			//MouseManager.getInstance().defaultMouseIcon= MouseManager.getInstance().getMouseIcon(MagicMouseIconType.DEFAULT_HAND);
			//设置默认手型
			MouseManager.getInstance().addMouseIcon(MagicMouseIconType.DOWN_HAND,new mouse_down_icon());
			MouseManager.getInstance().addMouseIcon(MagicMouseIconType.OVER_HAND, new mouse_over_icon());
			MouseManager.getInstance().addMouseIcon(MagicMouseIconType.STUDENT_HAND, new mouse_student());
			MouseManager.getInstance().addMouseIcon(MagicMouseIconType.PICK_HAND, new mouse_pick());
			//更新当前手型
			MouseManager.getInstance().setIcon();
			
			//读取静态数据
			loadStatic();
			
		}
		
		/**
		 * 静态数据读取
		 */
		private function loadStatic():void
		{
			//uiSprite.showLoading();
			
			//初始化urlLoader的事件派发实例
			UrlConnecter.eventManager = EventManager.getInstance();
			
			//请求
			var init_static_command:initStaticCommand = new initStaticCommand();
			init_static_command.addEventListener(Event.COMPLETE, loadInit);
			init_static_command.load();
			
		}
		
		/**
		 * 静态数据读取完毕与动态数据加载
		 * @param	e
		 */
		private function loadInit(e:Event):void
		{
			//读取玩家初始信息,包括场景\屋中的学生\玩家魔法等
			var init_command:initCommand = new initCommand();
			init_command.addEventListener(Event.COMPLETE, init);
			init_command.load();
		}
		
		/**
		 * 动态数据加载完毕,开始初始化场景
		 * @param	e
		 */
		private function init(e:Event = null):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, init);
			// entry point
			
			//场景容器位置
			this.sceneSprite.x = 0;
			this.sceneSprite.y = IsoUtil.TILE_SIZE / 2;
			
			//创建存储世界对象的类
			var magicState:MagicState = new MagicState();
			
			//世界view
			var magicView:MagicView = new MagicView(magicState);
			
			//准备创建世界
			var world:MagicWorld = new MagicWorld(magicState);
			DataManager.getInstance().setVar("magicWorld", world);
			
			//侦听世界创建完成事件
			EventManager.getInstance().addEventListener(SceneEvent.SCENE_COMPLETE, worldCreate_complete);
			
			//物理
			var physicsControl:IsoPhysicsControl = new IsoPhysicsControl();
			DataManager.getInstance().physicsControl = physicsControl;
			
			//本地缓存数据
			ShareObjectManager.getInstance().init("happyMagic",
				{ bgSound:true, soundEffect:true } //默认数据
			);
			
			//音效
			SoundEffectManager.getInstance();
			
			//初始化worldState
			magicState.init(magicView, world, physicsControl);
			
			DataManager.getInstance().worldState = magicState;
			
			//鼠标移动
            addEventListener(MouseEvent.MOUSE_MOVE, magicView.onMouseMove);
			
			//任务状态监听
			var taskStateManager:MagicTaskStateManager = new MagicTaskStateManager();
			DataManager.getInstance().setVar("taskStateManager", taskStateManager);
			
			//判断是否加载引导模块,如果有就先加载引导，再创建场景
			if (DataManager.getInstance().guides.length > 0)
			{
				var guidesActVo:ActVo = new ActVo();
				guidesActVo.actName = "guides";
				guidesActVo.moduleUrl = SwfURLManager.getInstance().getOtherSWfUrl("guides");
				var loadingitem:LoadingItem = ActModuleManager.getInstance().addActModule(guidesActVo);
			}
				
			createWorld();
		}
		
		private function guidesLoad_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, guidesLoad_complete);
			createWorld();
			
		}
		
		/**
		 * 场景开始创建
		 */
		private function createWorld():void {
			var world_data:Object = new Object();
			if (DataManager.getInstance().currentUser.currentSceneId==PublicDomain.getInstance().getVar("defaultSceneId")) 
			{
				//有房间的
				
				world_data['decorList'] = DataManager.getInstance().decorList;
				world_data['floorList'] = DataManager.getInstance().floorList;
				world_data['wallList'] = DataManager.getInstance().wallList;
				world_data['userInfo'] = DataManager.getInstance().curSceneUser;
				world_data['studentsList'] = DataManager.getInstance().getStudentsInRoom();
			}else {
				//没有房间的
				world_data['decorList'] = new Object();
				world_data['floorList'] = [];
				world_data['wallList'] = [];
				world_data['userInfo'] = DataManager.getInstance().curSceneUser;
				world_data['studentsList'] = [];
			}
			
			//将世界加入显示列表
			this.sceneSprite.addChild(DataManager.getInstance().worldState.view);
			
			var world:MagicWorld = DataManager.getInstance().getVar("magicWorld");
			
			world.create(world_data);
			
			//背景音乐
			BgMusicManager.getInstance().soundFlag=ShareObjectManager.getInstance().bgSound;
			
			//初始化模块
			uiSprite.initModules();
		}
		
		private function worldCreate_complete(e:SceneEvent):void 
		{
			
		}
		
		
	}
	
}