package happymagic.scene.world 
{
	import com.adobe.utils.ArrayUtil;
	import com.friendsofed.isometric.DrawnIsoTile;
	import com.friendsofed.isometric.IsoUtils;
	import com.friendsofed.isometric.Point3D;
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.display.DisplayObject;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.utils.setTimeout;
	import happyfish.cacher.CacheSprite;
	import happyfish.cacher.SwfClassCache;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.BgMusicManager;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.ModuleMvType;
	import happyfish.manager.module.vo.ModuleVo;
	import happyfish.scene.astar.Node;
	import happyfish.scene.camera.CameraControl;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.control.IsoPhysicsControl;
	import happyfish.scene.world.control.MapDrag;
	import happyfish.scene.world.control.MouseCursorAction;
	import happyfish.scene.world.GameWorld;
	import happyfish.scene.world.grid.BaseItem;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.grid.Person;
	import happyfish.scene.world.grid.SolidObject;
	import happyfish.scene.world.grid.Tile;
	import happyfish.scene.world.grid.Wall;
	import happyfish.scene.world.grid.Wall;
	import happyfish.scene.world.WorldState;
	import happyfish.scene.world.WorldView;
	import happyfish.util.queue.AbstractQueue;
	import happyfish.util.queue.driver.queue.BitmapCacherQueue;
	import happyfish.util.queue.driver.queue.ClassCacherQueue;
	import happyfish.utils.SysTracer;
	import happymagic.display.view.decorMirrro.MirroMenu;
	import happymagic.display.view.edit.BuildingItemList;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.SysMenuView;
	import happymagic.events.MagicClassBookEvent;
	import happymagic.events.SceneEvent;
	import happymagic.events.StudentEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.PublicDomain;
	import happymagic.manager.UiManager;
	import happymagic.model.command.FriendsHomeCommand;
	import happymagic.model.vo.DecorClassVo;
	import happymagic.model.vo.DecorType;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.ItemClassVo;
	import happymagic.model.vo.ItemVo;
	import happymagic.model.vo.SceneState;
	import happymagic.model.vo.SceneVo;
	import happymagic.model.vo.StudentStateType;
	import happymagic.model.vo.StudentVo;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.award.AwardItemManager;
	import happymagic.scene.world.award.AwardItemView;
	import happymagic.scene.world.award.AwardType;
	import happymagic.scene.world.bigScene.BigSceneBg;
	import happymagic.scene.world.bigScene.BigSceneView;
	import happymagic.scene.world.bigScene.NpcView;
	import happymagic.scene.world.control.AvatarCommand;
	import happymagic.scene.world.control.DoorStateControl;
	import happymagic.scene.world.control.FriendHomeAction;
	import happymagic.scene.world.control.FriendsHome;
	import happymagic.scene.world.control.MouseDefaultAction;
	import happymagic.scene.world.control.MouseEditAction;
	import happymagic.scene.world.grid.item.Decor;
	import happymagic.scene.world.grid.item.Desk;
	import happymagic.scene.world.grid.item.Door;
	import happymagic.scene.world.grid.item.RoomUpItem;
	import happymagic.scene.world.grid.item.WallDecor;
	import happymagic.scene.world.grid.person.Player;
	import happymagic.scene.world.grid.person.Student;
	/**
	 * ...
	 * @author Beck
	 */
	public class MagicWorld extends GameWorld
	{
		public var avatarsAreHidden:Boolean = false;
		public var bigSceneView:BigSceneView;
		public var sceneVo:SceneVo;
		private var _decorList:Object;
		private var _floorList:Array;
		private var _wallList:Array;
		private var _userInfo:UserVo;
		private var _studentsList:Array;
		private var _student:Student;
		private var _initFlg:Boolean = false;
		private var tileNeedLoad:uint;
		private var mouseGridIcon:gotoIcon;
		private var wallNeedLoad:uint;
		private var decorNeedLoad:uint;
		private var personNeedLoad:uint;
		private var tileList:Array;
		private var physicsControl:IsoPhysicsControl;
		private var playerFlagIcon:playerHalo;
		public var doorControl:DoorStateControl;
		public var doorList:Vector.<Door>;
		
		
		public function MagicWorld($worldState:WorldState) 
		{
			super($worldState);
			
			//初始化其他
			this.preInit();
		}
		
		public function preInit():void
		{
			//创建目标格标志icon
			if (!mouseGridIcon) 
			{
				mouseGridIcon = new gotoIcon();
				mouseGridIcon.mouseChildren=
				mouseGridIcon.mouseEnabled = false;
			}
			
			if (!playerFlagIcon) 
			{
				playerFlagIcon = new playerHalo();
				playerFlagIcon.mouseChildren=
				playerFlagIcon.mouseEnabled = false;
			}
			
		}
		
		/**
		 * 设置主角移动位置小旗位置
		 * @param	p
		 */
		public function setPlayerFlag(p:Point3D):void {
			playerFlagIcon.visible = true;
			_view.isoView.camera.addChild(playerFlagIcon);
			p = IsoUtil.gridToIso(p);
			var p2:Point = IsoUtils.isoToScreen(p);
			
			playerFlagIcon.x = p2.x;
			playerFlagIcon.y = p2.y-IsoUtil.TILE_SIZE*60/2;
			
			//playerFlagIcon.x = -(_view.isoView.camera.stage.stageWidth) / 2;
			//playerFlagIcon.y = 300;
		}
		
		/**
		 * 隐藏主角移动位置小旗
		 */
		public function clearPlayerFlag():void {
			playerFlagIcon.visible = false;
		}
		
		public function init():void
		{
			//监听点击事件
			this._view.addEventListener(GameMouseEvent.GAME_MOUSE_EVENT, this.onGameMouseEvent);
			
			//进入编辑模式
			EventManager.getInstance().addEventListener(SceneEvent.START_DIY, this.enterEditMode);
			
			//退出编辑模式
			EventManager.getInstance().addEventListener(SceneEvent.DIY_FINISHED, diyFinished);
			//取消编辑模式
			EventManager.getInstance().addEventListener(SceneEvent.DIY_CANCELDIY, diyCancel);
			
			//点击好友
			EventManager.getInstance().addEventListener(SceneEvent.CHANGE_SCENE, this.goFriendsHome);
			
			//学生状态变化
			//EventManager.getInstance().addEventListener(StudentEvent.REFRESH_INSCENE_STUDENT, studentChange);
			
			//初始化物理控制
			_worldState.physicsControl.initPhysics(_worldState);
			
			//奖励manager
			AwardItemManager.getInstance().init(_worldState);
			
			//门开关control
			doorControl = new DoorStateControl(_worldState);
			
			//侦听大小变化
			DisplayManager.sceneSprite.stage.addEventListener(Event.RESIZE, resizeFun);
		}
		
		/**
		 * 居中
		 */
		public function resizeFun(e:Event):void
		{
			_view.center();
			(ModuleManager.getInstance().getModule(ModuleDict.MODULE_SYSMENU) as SysMenuView).init();
		}
		
		override public function create($data:Object, $init_flg:Boolean = true):void
		{
			//更新主菜单的表现,自已家与别人家的区别
			if(DisplayManager.menuView) DisplayManager.menuView.setType();
			
			//更换场景交互逻辑state,也是自己家与别人家两套
			if (DataManager.getInstance().isSelfScene) 
			{
				MouseCursorAction.defaultAction = new MouseDefaultAction(_worldState);
			}else {
				MouseCursorAction.defaultAction = new FriendHomeAction(_worldState);
			}
			
			//
			tileList = new Array();
			//清空门队列
			doorList = new Vector.<Door>();
			
			//标记场景正在渲染
			sceneLoading = true;
			
			this._view = this._worldState.view;
			
			//初始化,目前只是一些事件侦听
			init();
			
			//游戏数据
			this._data = $data;
			this._decorList = this._data.decorList;
			this._wallList = this._data.wallList;
			this._floorList = this._data.floorList;
			this._userInfo = this._data.userInfo as UserVo;
			this._studentsList = this._data.studentsList;
			this._initFlg = $init_flg;
			
			//广播场景数据更换
			EventManager.getInstance().dispatchEvent(new SceneEvent(SceneEvent.SCENE_DATA_COMPLETE));
			
			//初始化grid
			if ($init_flg) {
				if (_wallList) 
				{
					if (_wallList.length>0) 
					{
						this._worldState.initGrid(this._userInfo.tile_x_length, this._userInfo.tile_z_length);
					}else {
						this._worldState.initGrid(0, 0);
					}
				}else {
					this._worldState.initGrid(0, 0);
				}
			}

			//创建背景尺寸
			this.groundRect = new Rectangle();
			this.groundRect.width = WorldView.WORLD_WIDTH;
			this.groundRect.height = WorldView.WORLD_HEIGHT;
			
			this.groundRect.x = -WorldView.WORLD_WIDTH / 2;
			//this.groundRect.y = -WorldView.WORLD_HEIGHT / 2 + IsoUtil.TILE_SIZE
			//this.groundRect.y = _groundSprite.height;
			
			//DisplayManager.uiSprite.showSceneOutMv(startCreateScene);
			
			startCreateScene();
			
		}
		
		private function startCreateScene():void {
			//加载大背景地图
			loadBigSceneBg(true);
		}
		
		public function loadBigSceneBg(isInit:Boolean=false):void {
			//创建场景背景图
			sceneVo = DataManager.getInstance().getSceneVoByClass(_userInfo.currentSceneId, SceneState.OPEN);
			
			var bigbm:BigSceneBg = new BigSceneBg();
			bigbm.loadClass(sceneVo.bg);
			_view.setBigBg(bigbm as Bitmap, -groundRect.width / 2, -groundRect.height / 2);
			
			if(isInit) createTile();
		}
		
		
		
		
		private function createTile():void {
			_groundSprite = new Sprite();
			//如果地板为空,就跳过
			if (_floorList.length>0) 
			{
				tileNeedLoad = _floorList.length * _floorList[0].length;
			
				var tmp_decor_vo:DecorVo;
				var tilemap:Tile;
				for(var i:int = 0; i < this._floorList.length; i++)
				{
					for (var j:int = 0; j < this._floorList[0].length; j++)
					{
						tmp_decor_vo = new DecorVo();
						tmp_decor_vo.createDefaultObj(this._floorList[i][j], i + 1, j + 1);
						
						tilemap = new Tile(tmp_decor_vo, this._worldState,tile_complete);
						
						//记录墙到列表内
						this.saveWallTileNodeItem(tilemap);
						
						tileList.push(tilemap);

						_groundSprite.addChild(tilemap.view.container);
					}
				}
			}else {
				//地板为空时,跳到地板渲染
				layTile();
				return;
			}
			
			
			//================================================================================
			
			//如果不是初始化场景,就直接创建后续对象(人物\物件之类)
			if (this._initFlg === false) {
				this.createOther();
			}
		}
		
		/**
		 * 某块地板完成时调用,判断全部完成时进入下一步
		 */
		private function tile_complete():void
		{
			tileNeedLoad--;
			if (tileNeedLoad<=0) 
			{
				setTimeout(layTile,100);
			}
		}
		
		public function createWall():void {
			//第一次进入场景时,关闭loading动画
			if (PublicDomain.getInstance().getVar("clearLoader")) 
			{
				PublicDomain.getInstance().getVar("clearLoader")();
				PublicDomain.getInstance().setVar("clearLoader",null);
			}
			
			//=============创建墙壁===========================================================
			
			if (_wallList.length>0) 
			{
				wallNeedLoad = _wallList[0].length + _wallList[1].length;
			
			
				var tmp_decor_vo:DecorVo;
				for (var k:int = 0; k < this._wallList[0].length; k++) {
					if (_worldState.isWallArea(k + IsoUtil.roomStart, IsoUtil.roomStart)) {
						tmp_decor_vo = new DecorVo();
						tmp_decor_vo.createDefaultObj(this._wallList[0][k], k+1, 0);
						
						this.addItem(new Wall( tmp_decor_vo, this._worldState,wallComplete ));
					}
				}
				
				for (var m:int = 0; m < this._wallList[1].length; m++) {
					if (_worldState.isWallArea(m + IsoUtil.roomStart, IsoUtil.roomStart)) {
						tmp_decor_vo = new DecorVo();
						tmp_decor_vo.createDefaultObj(this._wallList[1][m], 0, m+1);
						
						this.addItem(new Wall( tmp_decor_vo, this._worldState,wallComplete ));
					}
				}
			}else {
				createWall_complete();
			}
			
			
		}
		
		private function wallComplete():void
		{
			wallNeedLoad--;
			if (wallNeedLoad<=0) 
			{
				setTimeout(createWall_complete, 500);
			}
		}
		
		/**
		 * 墙渲染完成
		 * @param	e
		 * @param	hasRoom	当前场景中是否有房间
		 */
		private function createWall_complete(e:Event = null ):void 
		{
			if (e) e.target.removeEventListener(Event.COMPLETE, createWall_complete);
			
			if (_wallList.length>0) 
			{
				//设置房间的墙所在格不可走
				_worldState.closeRoomGrid();
				EventManager.getInstance().dispatchEvent(new SceneEvent(SceneEvent.WALL_COMPLETE));
				createDecor();
			}else {
				createSelfPlayer();
			}
			
			//关闭幕布
			DisplayManager.uiSprite.showSceneEndMv();
			
			
		}
		
		private function createDecor():void
		{
			//var bitmap_cacher_queue:BitmapCacherQueue = BitmapCacherQueue.getInstance();
			//bitmap_cacher_queue.addEventListener(Event.COMPLETE, createDecor_complete);
			
			decorNeedLoad = 0;
			if (_decorList[DecorType.WALL_DECOR]) decorNeedLoad += _decorList[DecorType.WALL_DECOR].length;
			if (_decorList[DecorType.DECOR]) decorNeedLoad += _decorList[DecorType.DECOR].length;
			if (_decorList[DecorType.DESK]) decorNeedLoad += _decorList[DecorType.DESK].length;
			if (_decorList[DecorType.DOOR]) decorNeedLoad += _decorList[DecorType.DOOR].length;
			
			//如果没有装饰物,就直接进入一下步
			if (decorNeedLoad==0) 
			{
				createDecor_complete();
				return;
			}
			
			//创建墙上装饰物
			if (_decorList[DecorType.WALL_DECOR]) {
				
				for (var m:int = 0; m < this._decorList[DecorType.WALL_DECOR].length; m++) {
					this.addItem(new WallDecor( this._decorList[DecorType.WALL_DECOR][m], this._worldState, doorComplete ));
				}
			}
			//this.addItem(new WallDecor( { class_name:'decor.1.hongseguanzi', x:0, z:5 ,size_x:1, size_z:1 }, this._worldState ));
			
			//创建普通装饰物品
			if (this._decorList[DecorType.DECOR]) {
				for (var n:int = 0; n < this._decorList[DecorType.DECOR].length; n++) {
					this.addItem(new Decor( this._decorList[DecorType.DECOR][n], this._worldState,decorComplete ));
				}
			}
			//this.addItem(new Decor( { class_name:'door.1.tiepimen', x:10, z:10 ,size_x:3, size_z:1 }, this._worldState ) );
			//this.addItem(new Decor( { class_name:'building.1.chengbao1', x:10, z:5 ,size_x:3, size_z:3 }, this._worldState ));
			
			//创建桌子
			if (this._decorList[DecorType.DESK]) {
				for (var o:int = 0; o < this._decorList[DecorType.DESK].length; o++) {
					this.addItem(new Desk( this._decorList[DecorType.DESK][o], this._worldState,decorComplete ));
				}
			}
			
			//创建门(一种墙上装饰)
			var tmpdoor:Door;
			if (this._decorList[DecorType.DOOR]) {
				for (var p:int = 0; p < this._decorList[DecorType.DOOR].length; p++) {
					tmpdoor = new Door( this._decorList[DecorType.DOOR][p], this._worldState, doorComplete);
					//放入门队列
					addDoorToList(tmpdoor);
				}
			}
			
			//创建扩地标志
			if (DataManager.getInstance().isSelfScene && !DataManager.getInstance().isMaxRoomSize(DataManager.getInstance().currentUser.tile_x_length)) 
			{
				var roomUpItem:RoomUpItem = new RoomUpItem( { x:DataManager.getInstance().currentUser.tile_x_length +IsoUtil.roomStart+ 1,
																z:IsoUtil.roomStart },_worldState);
																
				addItem(roomUpItem);
			}
			
		}
		
		public function doorComplete(target:WallDecor):void
		{
			if (target is Door) 
			{
				//用门来挖空他后面的墙
				//得到墙
				//var tmpp:Point;
				//if (target.mirror) 
				//{
					//tmpp = new Point(target.gridPos.x, target.gridPos.z-1);
				//}else {
					//tmpp = new Point(target.gridPos.x-1, target.gridPos.z);
				//}
				var wall:Wall = getWallByNode(target.gridPos.x, target.gridPos.z) as Wall;
				//if(wall) wall.cutDoor((target as Door).asset.bitmap_movie_mc);
				if (wall) 
				{
					wall.cutDoor((target as Door).asset.bitmap_movie_mc);
				}
				
				addItem(target);
			}
			
			decorComplete();
		}
		
		private function decorComplete():void
		{
			decorNeedLoad--;
			
			if (decorNeedLoad<=0) 
			{
				createDecor_complete();
			}
		}
		
		private function createDecor_complete(e:Event=null):void 
		{
			if(e) e.target.removeEventListener(Event.COMPLETE, createDecor_complete);
			doorControl.getAllDoorNodes();
			createPlayer();
		}
		
		public function createPlayer():void
		{
			
			personNeedLoad = _studentsList.length + 1;
			if (!DataManager.getInstance().isSelfScene) personNeedLoad++;
			
			var wNode:Node;
			if (_initFlg) {
				//创建主角
				wNode = _worldState.getCustomRoomWalkAbleNode();
				this._player = new Player( DataManager.getInstance().currentUser, this._worldState,wNode.x,wNode.y,playerComplete);
				this.addItem(this._player);
			}
			
			if (!DataManager.getInstance().isSelfScene) {
				wNode = _worldState.getCustomRoomWalkAbleNode();
				scenePlayer = new Player( DataManager.getInstance().curSceneUser, this._worldState, wNode.x, wNode.y, personComplete);
				this.addItem(this.scenePlayer);
			}
			
			//放置到特定位置
			for (var i:int = 0; i < this._studentsList.length; i++ ) {
				
				//创建学生
				var student:Student = new Student(this._studentsList[i], this._worldState,false,personComplete);
				this.addItem(student);
			}
		}
		
		public function createSelfPlayer():void {
			//创建主角
			personNeedLoad = 1;
			this._player = new Player( DataManager.getInstance().currentUser, this._worldState, IsoUtil.roomStart, IsoUtil.roomStart, playerComplete);
			this.addItem(this._player);
		}
		
		/**
		 * 检查所有装饰物是否要保存
		 * 如果不可，会显示半透明
		 * 如是课桌，会检查是否被堵路，并显示堵路标识
		 * @return	不可行为true,可走为false
		 */
		public function checkAllDeskCantWalk():Boolean {
			var decors:Array = items;
			var cantPut:Boolean;
			
			for (var i:int = 0; i < decors.length; i++) 
			{
				
				var item:IsoItem = decors[i];
				if (!item) 
				{
					continue;
				}
				
				//if (!(item is SolidObject) || (item is Wall)) 
				//{
					//continue;
				//}
				
				//if (!item.positionIsValid()) {
					//(item as SolidObject).showCantWalkIcon();
					//item.saveAble = false;
					//cantPut = true;
				//}else {
					//item.saveAble = true;
					//(item as SolidObject).hideCantWalkIcon();
				//}
				
				if (item is Desk) 
				{
					if (!(item as Desk).checkCanWalkTo()) {
						(item as Desk).showCantWalkIcon();
						cantPut = true;
					}else {
						(item as Desk).hideCantWalkIcon();
					}
				}
				
				if (item.saveAble == false) {
					cantPut = true;
				}
			}
			
			return cantPut;
		}
		
		private function playerComplete():void 
		{
			sceneLoading = false;
			personComplete();
		}
		
		private function personComplete():void
		{
			personNeedLoad--;
			if (personNeedLoad<=0) 
			{
				createPlayer_complete();
			}
		}
		
		private function createPlayer_complete(e:Event=null):void 
		{
			//doorControl.startCheckDoor();
			
			if (_initFlg) 
			{
				//播放背景音乐
				BgMusicManager.getInstance().setSound(InterfaceURLManager.getInstance().staticHost+PublicDomain.getInstance().getVar("bgMusic"));
			}
			
			//标记场景初始化完成
			_initFlg = false;
			
			if(e) e.target.removeEventListener(Event.COMPLETE, createPlayer_complete);
			
			DisplayManager.uiSprite.closeLoading();
			
			
			EventManager.getInstance().dispatchEvent(new SceneEvent(SceneEvent.SCENE_COMPLETE));
			
			
			
			//创建大场景容器
			createBigScene();
			
			
			//BgMusicManager.getInstance().setSound(sceneVo.bgSound);
			//BgMusicManager.getInstance().setSound(PublicDomain.getInstance().getVar("bgMusic"));
			
		}
		
		/**
		 * 创建大场景容器
		 */
		private function createBigScene():void {
			//创建大场景内的物件
			if (!bigSceneView) 
			{
				bigSceneView = new BigSceneView(_worldState);
			}
				bigSceneView.setData(sceneVo,
					DataManager.getInstance().getNpcsBySceneId(_userInfo.currentSceneId),
					DataManager.getInstance().enemys
				);
			
			
		}
		
		/**
		 * 增加一个掉落物
		 * 目前会把按1\10\100来分割成几个水晶来表现
		 * @param	_type
		 * @param	_num
		 * @param	startP
		 */
		public function createAwardItem(_type:uint, _num:uint, startP:Point3D, id:uint = 0):void {
			
			var awardItem:AwardItemView;
			var tmpItem:ItemClassVo;
			var tmpDecor:DecorClassVo;
			var i:int;
			if (_type==AwardType.ITEM && id) 
			{
				//道具
				tmpItem = DataManager.getInstance().getItemClassByIid(id);
				for ( i = 0; i < _num; i++) 
				{
					awardItem = new AwardItemView( { type:_type, num:_num, class_name:tmpItem.class_name, x:startP.x, y:0, z:startP.z }, _worldState);
					setTimeout(addItem,100*i,awardItem);
				}
			}else if (_type==AwardType.DECOR && id) {
				//装饰物
				tmpDecor = DataManager.getInstance().getDecorClassByDid(id);
				for ( i = 0; i < _num; i++) 
				{
					awardItem = new AwardItemView( { type:_type, num:_num, class_name:tmpDecor.class_name, x:startP.x, y:0, z:startP.z }, _worldState);
					setTimeout(addItem,100*i,awardItem);
				}
			}else if (_type!=AwardType.OTHER ) {
				//获得奖品类名
				var tmpclassstr:String = "awardIcon_";
				if (_type == AwardType.COIN) 
				{
					//coin按多少分类
					if (_num<10) 
					{
						tmpclassstr += _type.toString()+ "_2";
					}else if (_num<100) 
					{
						tmpclassstr += _type.toString()+ "_2";
					}else  
					{
						tmpclassstr += _type.toString()+ "_3";
					}
				}else {
					//经验和乐币
					tmpclassstr += _type.toString();
				}
				
				awardItem = new AwardItemView( { type:_type, num:_num, class_name:tmpclassstr, x:startP.x, y: -1, z:startP.z }, _worldState);
				addItem(awardItem);
			}
		}
		
		/**
		 * 后创建
		 * @param	$init_flg
		 */
		public function createOther():void {
			
			createWall();
			
			return;
			
		}
		
		/**
		 * 渲染地板
		 * @param	e
		 */
		public function layTile(e:Event = null):void
		{
			if (e) 
			{
				e.target.removeEventListener(Event.COMPLETE, layTile);
			}
			
			
			this.groundRect.y = _groundSprite.height;
			
			this.updateGroundBitmapData();
			//浸染大背景图
			this._view.setBackground(this.groundData, this.groundRect.x, this.groundRect.y);
			//开始目标格标示对象跟随鼠标
			//startDragMouseGrid();
			
			//开始侦听拖动地图
			MapDrag.getInstance(_view.isoView.camera);
			
			this._view.center();
			
			if (this._initFlg) {
				createOther();
			}
		}
		
		private function startDragMouseGrid():void
		{
			_worldState.view.isoView.addChild(mouseGridIcon);
			_view.addEventListener(Event.ENTER_FRAME, mouseGridFun);
		}
		
		private function mouseGridFun(e:Event):void 
		{
			var p:Point3D = _worldState.view.targetGrid();
			p = IsoUtil.gridToIso(p);
			var p2:Point = IsoUtils.isoToScreen(p);
			mouseGridIcon.x = p2.x;
			mouseGridIcon.y = p2.y;
		}
		
		public function getStudent(sid:uint):Student {
			for (var i:int = 0; i < items.length; i++) 
			{
				if (items[i] is Student ) 
				{
					if (Student(items[i]).data.sid==sid) 
					{
						return items[i];
					}
					
				}
			}
			return null;
		}
		
		public function getCustomDoor():Door {
			for (var i:int = 0; i < items.length; i++) 
			{
				if (items[i] is Door ) 
				{
					return items[i];
				}
			}
			return null;
		}
		
		/**
		 * 增加物件
		 * @param	$baseItem
		 * @param	tmpAdd	临时增加入，不放入物件列表
		 */
        override public function addItem($baseItem:BaseItem,tmpAdd:Boolean=false) : void
        {
			//放入item列表
			var tmpitem:IsoItem = IsoItem($baseItem);
			if (tmpitem.view) {
				super.addItem($baseItem);
				_view.addIsoChild(tmpitem.view);
			}
			
			//设置可行走和可DIY属性
			//墙加入列表
            //if ($baseItem is SolidObject && !($baseItem is Wall))
            if ($baseItem is SolidObject)
            {
				//加入格子
                this.addToGrid($baseItem);
				//把墙放入墙队列
				if ($baseItem is Wall && !tmpAdd){
					this.saveWallTileNodeItem($baseItem);
				}
            } 
			
            return;
        }
		
		public function clearRoom():void {
			super.clear();
			//清除item
			destroyItems();
			
			cleaerTile();
			
			//去除已有tips
			if (DisplayManager.doorTip) 
			{
				if (DisplayManager.doorTip.view.parent) 
				{
					_view.isoView.removeChild(DisplayManager.doorTip.view);
				}
			}
			
			//清除场景内可行走数据
			_worldState.clearRoomWalkAbles();
		}
		
		override public function clear():void 
		{
			super.clear();
			
			//清除房间的数据
			//DataManager.getInstance().decorList = [];
			//DataManager.getInstance().floorList = [];
			//DataManager.getInstance().wallList = [];
			//DataManager.getInstance().curSceneUser = null;
			
			//清除item
			destroyItems();
			
			cleaerTile();
			
			doorList = new Vector.<Door>();
			
			//去除已有tips
			if (DisplayManager.doorTip) 
			{
				if (DisplayManager.doorTip.view.parent) 
				{
					_view.isoView.removeChild(DisplayManager.doorTip.view);
				}
			}
			
			//清除场景内可行走数据
			_worldState.clearRoomWalkAbles();
			
			if (bigSceneView) bigSceneView.clear();
			
			EventManager.getInstance().dispatchEvent(new SceneEvent(SceneEvent.SCENE_CLEARED));
			
		}
		
		/**
		 * 清除地板和墙
		 */
		private function cleaerTile():void {
			if (_groundSprite.parent) _groundSprite.parent.removeChild(_groundSprite);
			_floorList = [];
			tileList = [];
			//nodeWallTileItems = new Object();
		}
		
		override public function destroyItems():void
		{
			//清除item
			while (_items.length>0) 
			{
				_items[0].remove();
			}
		}
		
		public function addDoorToList(door:Door):void {
			doorList.push(door);
		}
		
		public function removeDoorFromList(door:Door):void {
			for (var i:int = 0; i < doorList.length; i++) 
			{
				if (doorList[i] == door) {
					doorList.splice(i, 1);
					return;
				}
			}
		}
		
		/**
		 * 进入DIY模式
		 * @param	e
		 */
        public function enterEditMode(e:Event = null) : void
        {
			//标记DIY状态
			DataManager.getInstance().isDiying = true;
			
			//建立打开装饰物列表模块
			DisplayManager.uiSprite.addModule(ModuleDict.MODULE_DIY_ITEMBOX,ModuleDict.MODULE_DIY_ITEMBOX_CLASS,false,AlginType.BC,0,125,0,0,ModuleMvType.FROM_BOTTOM);
			ModuleManager.getInstance().showModule(ModuleDict.MODULE_DIY_ITEMBOX);
			
            new MouseEditAction(this._worldState);
			
			//隐藏人物
            if (!this.avatarsAreHidden)
            {
                allItemToDiyState();
            }
			
			//暂停门开关控制
			//doorControl.stopCheckDoor();
			
            //this.view.showLayer(this.view.editContainer);
            //this._worldState.whichDisplay = "edit";
            return;
        }
		
		/**
		 * 退出DIY时
		 * @param	e
		 */
		private function diyFinished(e:SceneEvent):void 
		{
			//标记DIY状态
			DataManager.getInstance().isDiying = false;
			
			DataManager.getInstance().decorList = _decorList;
			DataManager.getInstance().floorList = ArrayUtil.copyArray(_floorList);
			DataManager.getInstance().wallList = _wallList;
			
			leaveEditMode();
		}
		
		private function diyCancel(e:SceneEvent):void 
		{
			//清除此场景
			DataManager.getInstance().worldState.world.clear();
			
			var world_data:Object = new Object();
			
			world_data['decorList'] = DataManager.getInstance().decorList;
			world_data['floorList'] = DataManager.getInstance().floorList;
			world_data['wallList'] = DataManager.getInstance().wallList;
			world_data['userInfo'] = DataManager.getInstance().curSceneUser;
			world_data['studentsList'] = DataManager.getInstance().getStudentsInRoom();
			create(world_data, true);
		}
		
		/**
		 * 离开编辑模式
		 */
		override public function leaveEditMode():void
		{
			var i:int;
			var tmparr:Array;
			//设置所有场景和背包内道具bag_type
			tmparr = DataManager.getInstance().decorBagList;
			for (i = 0; i < tmparr.length; i++) 
			{
				tmparr[i].bag_type = 1;
			}
			
			var tmpObj:Object = DataManager.getInstance().decorList;
			for (var name:String in tmpObj) 
			{
				tmpObj[name].bag_type = 0;
			}
			
			//设置所有场景上道具对象的DATA
			tmparr = _items;
			for (i = 0; i < tmparr.length; i++) 
			{
				if(tmparr[i].data is DecorVo) tmparr[i].hideGlow();
				if (tmparr[i].data is DecorVo) 
				{
					tmparr[i].data.bag_type = 0;
				}
				if (tmparr[i] is Door) 
				{
					(tmparr[i] as Door).countDown();
				}
			}
			
			//关闭镜像按钮
			(DataManager.getInstance().getVar("mirroMenu") as MirroMenu).hideMenu();
			DataManager.getInstance().setVar("mirroMenu",null);
			
			
			new MouseDefaultAction(this._worldState);
			
			//==========重新创建地板=============================================================
			//清除原有地板
			cleaerTile();
			
			var tmp:*;
			_groundSprite = new Sprite();
			for (var x:String in this.nodeWallTileItems) 
			{
				for (var y:String in nodeWallTileItems[x]) 
				{
					//如果不是墙,并且不是在0,0位置的,就是地板
					tmp= nodeWallTileItems[x][y];
					//if (!_worldState.isWallArea(int(x), int(y)) && !(x == '0' && y == '0')) {
					if (tmp is Tile && !(x == '0' && y == '0')) {
						this._groundSprite.addChild(tmp.view.container);
						tileList.push(tmp);
					}
				}
			}
			
			this.layTile();
			
			//显示所有人形
			allItemStopDiyState();
			
			ModuleManager.getInstance().closeModule('itembox', true);
			
			//doorControl.startCheckDoor();
		}
		
		/**
		 * 隐藏主角和npc
		 */
		public function hidePlayer():void {
			if (player) 
			{
				player.visible = false;
			}
			
			if(bigSceneView) bigSceneView.hideAllNpc();
		}
		
		public function showPlayer():void {
			player.visible = true;
			bigSceneView.showAllNpc();
		}
		
		/**
		 * 隐藏所有人形
		 * 所有物件进入diy状态
		 */
		public function allItemToDiyState():void
		{
			this.avatarsAreHidden = true;
			
			for (var i:int = 0; i < this._items.length; i++ ) {
				if ((this._items[i] is Person)) {
					_items[i].visible = false;
				}else if (_items[i] is Door || _items[i] is Desk || _items[i] is RoomUpItem) 
				{
					_items[i].diyState = true;
				} else if (_items[i] is AwardItemView) {
					_items[i].visible = false;
				}
			}
		}
		
		/**
		 * 所有物品停止DIY中状态
		 */
		public function allItemStopDiyState():void {
			this.avatarsAreHidden = false;
			
			for (var i:int = 0; i < this._items.length; i++ ) {
				if ((this._items[i] is Person)) {
					_items[i].visible = true;
					
					//如果是主角，如果主角所在位置变成了不可行走区域，就随机一个位置放置主角
					if (_items[i] is Player) 
					{
						if (!_worldState.grid.getNode((_items[i] as Player).x,(_items[i] as Player).z).walkable) 
						{
							var tmpnode:Node = _worldState.getCustomRoomWalkAbleNode();
							(_items[i] as Player).setPos(new Point3D(tmpnode.x, 0, tmpnode.y));
						}
					}
					
					//设置学生到课桌位置上
					if (_items[i] is Student) 
					{
						
						if ((_items[i] as Student).desk) 
						{
							(_items[i] as Student).view.setPos((_items[i] as Student).desk.getWalkableSpace());
						}
					}
					
				}else if (_items[i] is Door || _items[i] is Desk || _items[i] is RoomUpItem) 
				{
					_items[i].diyState = false;
				} else if (_items[i] is AwardItemView) {
					_items[i].visible = true;
				}
			}
		}
		
		/**
		 * 获取一个可以放人的门
		 * @return
		 */
		public function getReadyDoor():Door {
			for (var i:int = 0; i < items.length; i++) 
			{
				if (items[i] is Door) 
				{
					if ((items[i] as Door).isReady) 
					{
						return items[i] as Door;
					}
				}
			}
			
			return null;
		}
		
		/**
		 * 获取一个可以教的学生
		 * @return
		 */
		public function getNeedTeachStudent():Student {
			for (var i:int = 0; i < items.length; i++) 
			{
				if (items[i] is Student) 
				{
					if ((items[i].data as StudentVo).state == StudentStateType.NOTEACH) 
					{
						return items[i] as Student;
					}
				}
			}
			
			return null;
		}
		
		/**
		 * 获取一个中断中的学生
		 * @return
		 */
		public function getEventStudent():Student {
			for (var i:int = 0; i < items.length; i++) 
			{
				if (items[i] is Student) 
				{
					if ((items[i].data as StudentVo).state == StudentStateType.INTERRUPT) 
					{
						return items[i] as Student;
					}
				}
			}
			
			return null;
		}
		
		/**
		 * 获取一个可以收水晶的课桌
		 * @return
		 */
		public function getHaveCrystalDesk():Desk {
			for (var i:int = 0; i < items.length; i++) 
			{
				if (items[i] is Desk) 
				{
					if ((items[i] as Desk).crystal) 
					{
						return items[i] as Desk;
					}
				}
			}
			
			return null;
		}
		
		/**
		 * 场景内所有点击事件统一处理接口
		 * @param	event
		 */
        private function onGameMouseEvent(event:GameMouseEvent) : void
        {
			if (sceneLoading) 
			{
				return;
			}
            var event_type:String = '';
            if (this._worldState.mouseAction != null)
            {
                event_type = "on" + event.itemType + event.mouseEventType;
				//trace(event_type);
                if (this._worldState.mouseAction[event_type] != null)
                {
					this._worldState.mouseAction[event_type](event);
                }
            }
            return;
        }
		
		override public function get userInfo():Object
		{
			return this._userInfo;
		}
		
		public function get decorList():Object { return _decorList; }
		
		public function goFriendsHome(e:SceneEvent):void
		{
			new FriendsHome(e.uid);
		}
		
		/**
		 * 
		 * @return
		 */
		public function getAddMaxMp():int {
			
			var addMaxMp:int=0;
			var i:int;
			for ( i= 0; i < this._items.length; i++ ) {
				if (isDecorView(_items[i])) {
					addMaxMp += (_items[i].data as DecorVo).max_magic;
				}
			}
			
			for (i = 0; i < tileList.length; i++) 
			{
				addMaxMp += tileList[i].data.max_magic;
			}
			
			return addMaxMp;
		}
		
		public function getStudentNum():uint {
			//var num:uint=0;
			//for (var i:int = 0; i < items.length; i++) 
			//{
				//var item:Student = items[i] as Student;
				//if (item) 
				//{
					//if ((item.data as StudentVo).state != StudentStateType.TEACHOVER) 
					//{
						//num++;
					//}
				//}
			//}
			//return num;
			
			var arr:Array = new Array();
			arr = concatStudentArr(arr,DataManager.getInstance().fiddleStudents);
			arr = concatStudentArr(arr, DataManager.getInstance().onDeskStudents);
			for (var name:String in DataManager.getInstance().openDoorStudents) 
			{
				arr = concatStudentArr(arr,DataManager.getInstance().openDoorStudents[name]);
			}
			
			SysTracer.systrace("@@@@@@学生数@@@@@@@@@");
			var num:uint;
			var tmpstudent:StudentVo;
			//var tmpStudentView:Student;
			for (var i:int = 0; i < arr.length; i++) 
			{
				tmpstudent = arr[i] as StudentVo;
				//tmpStudentView = getStudentBySid(tmpstudent.sid);
				//if (tmpStudentView) 
				//{
					//if (tmpstudent.state != StudentStateType.TEACHOVER && !tmpStudentView.outed) 
					//{
						//SysTracer.systrace(tmpstudent.sid,tmpstudent.state);
						//num++;
					//}
				//}
				
				if (tmpstudent.state != StudentStateType.TEACHOVER) 
					{
						SysTracer.systrace(tmpstudent.sid,tmpstudent.state);
						num++;
					}
				
			}
			
			return num;
		}
		
		/**
		 * 合并学生数据为一个数组,排除重复的学生数据,主要是为了得到正确的学生数
		 */
		private function concatStudentArr(arr1:Array, arr2:Array):Array {
			
			var tmparr:Array = new Array();
			tmparr = tmparr.concat(arr1);
			
			var tmpvo:StudentVo;
			var hasSame:Boolean;
			for (var i:int = 0; i < arr2.length; i++) 
			{
				tmpvo = arr2[i] as StudentVo;
				hasSame = false;
				for (var j:int = 0; j < tmparr.length; j++) 
				{
					if ((tmparr[j] as StudentVo).sid==tmpvo.sid)
					{
						hasSame = true;
						break;
					}
				}
				if (!hasSame) 
				{
					tmparr.push(tmpvo);
				}
				
			}
			
			return tmparr;
		}
		
		public function getStudentOnDeskNum():uint 
		{
			var num:uint=0;
			for (var i:int = 0; i < items.length; i++) 
			{
				var item:Student = items[i] as Student;
				if (item) 
				{
					if ((item.data as StudentVo).state == StudentStateType.INTERRUPT
						|| (item.data as StudentVo).state == StudentStateType.NOTEACH
						|| (item.data as StudentVo).state==StudentStateType.STUDYING
					) 
					{
						num++;
					}
				}
			}
			return num;
		}
		
		private function isDecorView(value:IsoItem):Boolean {
			if ((value is Wall || value is WallDecor || value is Door || value is Decor) && !(value is RoomUpItem) ) 
			{
				return true;
			}
			return false;
		}
		
	}

}