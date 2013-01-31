package happymagic.manager 
{
	import com.adobe.serialization.json.JSON;
	import com.adobe.utils.ArrayUtil;
	import com.brokenfunction.json.decodeJson;
	import com.brokenfunction.json.encodeJson;
	import flash.display.Stage;
	import happyfish.events.ActModuleEvent;
	import happyfish.feed.vo.FeedVo;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.ModuleManager;
	import happyfish.model.vo.GuidesClassVo;
	import happyfish.model.vo.GuidesState;
	import happyfish.model.vo.GuidesVo;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.control.IsoPhysicsControl;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.WorldState;
	import happyfish.task.events.TaskStateEvent;
	import happyfish.task.vo.ITaskVo;
	import happyfish.utils.CustomTools;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.events.DataManagerEvent;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.model.vo.AvatarType;
	import happymagic.model.vo.AvatarVo;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.DecorClassVo;
	import happymagic.model.vo.DecorType;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.EnemyClassVo;
	import happymagic.model.vo.ItemClassVo;
	import happymagic.model.vo.ItemVo;
	import happymagic.model.vo.LevelInfoVo;
	import happymagic.model.vo.MagicClassVo;
	import happymagic.model.vo.MagicType;
	import happymagic.model.vo.MixMagicVo;
	import happymagic.model.vo.MoneyType;
	import happymagic.model.vo.NpcVo;
	import happymagic.model.vo.RoomLevelVo;
	import happymagic.model.vo.RoomSizeVo;
	import happymagic.model.vo.SceneClassVo;
	import happymagic.model.vo.SceneState;
	import happymagic.model.vo.SceneVo;
	import happymagic.model.vo.StudentClassVo;
	import happymagic.model.vo.StudentLevelClassVo;
	import happymagic.model.vo.StudentStateType;
	import happymagic.model.vo.StudentStateVo;
	import happymagic.model.vo.StudentVo;
	import happymagic.model.vo.SwitchVo;
	import happymagic.model.vo.TaskClassVo;
	import happymagic.model.vo.TaskTipsVo;
	import happymagic.model.vo.TaskVo;
	import happymagic.model.vo.TransMagicVo;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.grid.item.Desk;
	import happymagic.scene.world.grid.person.Student;
	import happymagic.scene.world.MagicWorld;
	
	/**
	 * ...
	 * @author jj
	 */
	public class DataManager 
	{
		private static var instance:DataManager;
		
		/**
		 * 静态基础信息
		 */
		//道具基础信息
		private var _itemClass:Array=new Array();
		//装饰基础信息
		private var _decorClass:Array;
		//等级信息
		private var _levelInfos:Array;
		//魔法基础信息
		public var _magicClass:Array = new Array();
		//合成术基础信息
		private var _mixMagics:Array = new Array();
		//变化术基础信息
		private var _transMagicClass:Array = new Array();
		//任务类信息
		private var _taskClass:Array = new Array();
		//avatar类
		private var _avatars:Array = new Array();
		
		
		//基本设置文件,如多少时间回一次魔法
		public var basicClass:Object;
		
		/**
		 * 场景内装饰信息
		 */
		//包含场景内所有装饰道具信息,除墙,地板外,并以按类分毫
		public var decorList:Object = new Object();
		public var floorList:Array = new Array();
		public var wallList:Array = new Array();
		
		//玩家信息
		public var userInfo:UserVo;
		
		//当前岛的vo
		public var curSceneUser:UserVo;
		
		//用户自己的信息
		private var _currentUser:UserVo;
		
		//好友列表
		public var friends:Array;
		
		/**
		 * DIY时保存时需要提交的改变数据
		 */
		public var decorChangeList:Object = new Object();
		public var decorChangeBagList:Object = new Object();
		public var floorChangeList:Object = new Object();
		public var floorChangeBagList:Object = new Object();
		public var wallChangeList:Object = new Object();
		public var wallChangeBagList:Object = new Object();
		
		/**
		 * DIY时装饰列表
		 */
		private var _decorBagList:Array;
		private var customObj:Object = new Object();
		
		/**
		 * 闲逛的学生
		 */
		public var fiddleStudents:Array = new Array();
		
		/**
		 * 桌子上的学生
		 */
		public var onDeskStudents:Array = new Array();
		
		/**
		 * 点击门后出来的学生,需创建
		 */
		public var openDoorStudents:Object = new Object();
		
		/**
		 * 已学会的魔法
		 */
		public var magicList:Array=new Array();
		//已学会的变化术
		public var transMagics:Array;
		//拥有的道具列表
		public var items:Array = new Array();
		//拥有的任务
		public var tasks:Vector.<ITaskVo> = new Vector.<ITaskVo>();
		
		//拣取水晶返回结果,以decorId为keyname的object,值为ResultVo
		public var pickUpResults:Object = new Object;
		//拣取水晶时掉落的物品列表
		public var pickUpItems:Object = new Object;
		
		//中断处理返回结果,以decorId为keyname的object
		public var interruptResults:Object = new Object;
		//拣取水晶返回的学生列表,以decorId为keyname的object,值为ResultVo
		public var pickUpStudentResults:Object = new Object;
		
		//音效开关
		public var soundEffect:Boolean = true;
		
		public var worldState:WorldState;
		public var isDraging:Boolean;
		//交换背包数据
		public var switchVo:SwitchVo;
		public var curSceneSwitchVo:SwitchVo;
		public var switchLevel:Array;
		//场景类列表
		public var sceneClass:Array;
		//场景数据
		public var scenes:Array;
		//npc类
		public var npcClass:Array;
		//npc对话列表
		public var npcChats:Array;
		public var enemyClass:Array;
		private var _enemys:Array;
		public var physicsControl:IsoPhysicsControl;
		public var guidesClass:Array;
		public var guides:Array;
		public var diarys:Array;
		//是否在DIY中
		public var isDiying:Boolean;
		//房间大小升级类数据
		public var roomSizeClass:Array;
		//feed类数据
		public var feedClass:Array;
		//房间等级类数据
		public var roomLevelClass:Array;
		//学生等级类数据
		public var studentLevelClass:Array;
		//学生类数据
		public var studentClass:Array;
		//学生实例数据
		public var studentStates:Array;
		//活动数据
		public var acts:Array;
		//连续登陆的静态数据
		public var signAwardClass:Array;
		//任务说明类数据
		public var taskTips:Array;
		
		//换装静态数据
		public var rehandling:Array;
		
		public function DataManager(access:Private) 
		{
			
			if (access != null)
			{	
				if (instance == null)
				{				
					instance = this;
				}
			}
			else
			{	
				throw new Error( "DataManager"+"单例" );
			}
		}
		
		public function setVar(name:String,val:*):void {
			customObj[name] = val;
		}
		
		public function getVar(name:String):* {
			return customObj[name];
		}
		
		public static function getInstance():DataManager
		{
			if (instance == null)
			{
				instance = new DataManager( new Private() );
			}
			return instance;
		}
		
		public function getEnemyClass(enemyCid:uint):EnemyClassVo {
			for (var i:int = 0; i < enemyClass.length; i++) 
			{
				if (enemyClass[i].enemyCid==enemyCid) 
				{
					return enemyClass[i] as EnemyClassVo;
				}
			}
			return null;
		}
		
		/**
		 * 随机一个场景入口
		 * @param	except
		 * @return
		 */
		public function getCustomSceneDoor(except:Node = null):Node {
			var tmp:Node=CustomTools.customFromArray(getSceneVoByClass(curSceneUser.currentSceneId, SceneState.OPEN).getEntrancesNode(),except);
			return tmp;
		}
		
		public function getActByName(name:String):ActVo {
			for (var i:int = 0; i < acts.length; i++) 
			{
				if (acts[i].actName==name) 
				{
					return acts[i] as ActVo;
				}
			}
			return null;
		}
		
		public function setActByName(actVo:ActVo):void {
			for (var i:int = 0; i < acts.length; i++) 
			{
				if (acts[i].actName==actVo.actName) 
				{
					acts[i] = actVo;
					//广播活动数据改变
					EventManager.getInstance().dispatchEvent(new ActModuleEvent(ActModuleEvent.ACT_DATA_CHANGE, actVo));
					return;
				}
			}
		}
		
		public function hasLearnMagicClass(m_id:uint):Boolean {
			for (var i:int = 0; i < magicList.length; i++) 
			{
				if (magicList[i].magic_id==m_id) 
				{
					return true;
				}
			}
			return false;
		}
		
		public function getLearnedTrans():Array {
			var outarr:Array = new Array();
			for (var i:int = 0; i < transMagics.length; i++) 
			{
				var item:TransMagicVo = getTransMagicClassByTid(transMagics[i]);
				if (item) 
				{
					outarr.push(item);
				}
			}
			return outarr;
		}
		
		public function hasLearnTrans(t_id:uint):Boolean
		{
			for (var i:int = 0; i < transMagics.length; i++) 
			{
				if (transMagics[i]==t_id) 
				{
					return true;
				}
			}
			return false;
		}
		
		public function getTransMagicClassByTid(tid:uint):TransMagicVo
		{
			for (var i:int = 0; i < _transMagicClass.length; i++) 
			{
				if (tid==_transMagicClass[i].trans_mid) 
				{
					return _transMagicClass[i];
				}
			}
			return null;
		}
		
		public function getMixMagicByDid(did:uint):MixMagicVo {
			for (var i:int = 0; i < mixMagics.length; i++) 
			{
				if (did==(mixMagics[i] as MixMagicVo).d_id) 
				{
					return mixMagics[i];
				}
			}
			return null;
		}
		
		public function getMixMagicByMid(mid:uint):MixMagicVo {
			for (var i:int = 0; i < mixMagics.length; i++) 
			{
				if (mid==(mixMagics[i] as MixMagicVo).mix_mid) 
				{
					return mixMagics[i];
				}
			}
			return null;
		}
		
		public function getEnoughMp(value:int):Boolean {
			return _currentUser.mp >= value;
		}
		
		public function getEnoughMix(mixMagic:MixMagicVo):Boolean {
			if (!getEnoughCrystal(mixMagic.coin,mixMagic.gem)) 
			{
				return false;
			}
			if (!getEnoughItems(mixMagic.itemId)) 
			{
				return false;
			}
			if (!getEnoughDecors(mixMagic.decorId)) 
			{
				return false;
			}
			
			return true;
		}
		
		public function getDecorByDid(d_id:uint):DecorVo {
			for (var i:int = 0; i < decorBagList.length; i++) 
			{
				if (decorBagList[i].d_id==d_id) 
				{
					return decorBagList[i];
				}
			}
			return null;
		}
		
		/**
		 * 
		 * @param	... args	[i_id,num],[11,2]
		 * @return
		 */
		public function getEnoughDecors(decorArr:Array):Boolean {
			var tmp:DecorVo;
			for (var i:int = 0; i < decorArr.length; i++) 
			{
				tmp = getDecorByDid(decorArr[i][0]);
				if (tmp) 
				{
					if (tmp.num<decorArr[i][1]) 
					{
						return false;
					}
				}else {
					return false;
				}
			}
			return true;
		}
		
		public function getDecorNum(d_id:uint):int {
			var tmp:DecorVo = getDecorByDid(d_id);
			if (tmp) 
			{
				return tmp.num;
			}else {
				return 0;
			}
		}
		
		/**
		 * 
		 * @param	... args	[i_id,num],[11,2]
		 * @return
		 */
		public function getEnoughItems(itemArr:Array):Boolean {
			var tmp:ItemVo;
			for (var i:int = 0; i < itemArr.length; i++) 
			{
				tmp = getItemByIid(itemArr[i][0]);
				if (tmp) 
				{
					if (tmp.num<itemArr[i][1]) 
					{
						return false;
					}
				}else {
					return false;
				}
			}
			return true;
		}
		
		public function getItemNum(i_id:uint):int {
			var tmp:ItemVo = getItemByIid(i_id);
			if (tmp) 
			{
				return tmp.num;
			}else {
				return 0;
			}
		}
		
		public function getEnouthCrystalType(type:uint,num:uint):Boolean {
			if (type==MoneyType.COIN) 
			{
				if (num <= _currentUser.coin)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			
			if (type==MoneyType.GEM) 
			{
				return num <= _currentUser.gem;
			}
			
			return true;
		}
		
		/**
		 * 按type返回自己的相应水晶数量
		 * @param	type
		 * @return
		 */
		public function getCrystalType(type:uint):uint {
			if (type==MoneyType.COIN) 
			{
				return _currentUser.coin;
			}
			
			if (type==MoneyType.GEM) 
			{
				return _currentUser.gem;
			}
			
			return null;
		}
		
		public function getUserData(type:String):uint {
			var id:uint = ConditionType.StringToInt(type);
			if (id<2) 
			{
				return getCrystalType(id);
			}
			switch (type) 
			{
				case ConditionType.USER_EXP:
				return _currentUser.exp;
				break;
			}
			
			return null;
		}
		
		public function getEnoughUserData(type:String, num:uint):Boolean {
			var id:uint = ConditionType.StringToInt(type);
			if (id<2) 
			{
				return getEnouthCrystalType(id, num);
			}
			switch (type) 
			{
				case ConditionType.USER_EXP:
				return _currentUser.exp >= num;
				break;
				
				case ConditionType.USER_MP:
				return _currentUser.mp >= num;
				break;
			}
			
			return false;
		}
		
		public function getEnoughCrystal(coin:uint=0,gem:uint=0):Boolean {
			if (coin>_currentUser.coin) 
			{
				return false;
			}
			
			if (gem>_currentUser.gem) 
			{
				return false;
			}
			
			return true;
		}
		
		public function getTransMagicNeedCrystal(transVo:TransMagicVo):uint {
			if (transVo.gem) 
			{
				return transVo.gem;
			}
			return transVo.coin;
		}
		
		public function getItemByIid(i_id:uint):ItemVo {
			for (var i:int = 0; i < items.length; i++) 
			{
				if (i_id==items[i].i_id) 
				{
					return items[i];
				}
			}
			return null;
		}
		
		public function getItemById(id:uint):ItemVo {
			for (var i:int = 0; i < items.length; i++) 
			{
				if (id==items[i].id) 
				{
					return items[i];
				}
			}
			return null;
		}
		
		public function getItemClassByIid(i_id:uint):ItemClassVo {
			for (var i:int = 0; i < itemClass.length; i++) 
			{
				if (i_id==itemClass[i].i_id) 
				{
					return itemClass[i];
				}
			}
			return null;
		}
		
		public function getTaskByTid(t_id:uint):TaskVo {
			for (var i:int = 0; i < tasks.length; i++) 
			{
				var item:TaskVo = tasks[i] as TaskVo;
				if (item.t_id==t_id) 
				{
					return item;
				}
			}
			return null;
		}
		
		public function getTaskClass(t_id:uint):TaskClassVo {
			for (var i:int = 0; i < _taskClass.length; i++) 
			{
				if (t_id==_taskClass[i].t_id) 
				{
					return _taskClass[i];
				}
			}
			return null;
		}
		
		/**
		 * 是否所有新手引导都完成(除最后一步领取最终奖励)
		 */
		public function get isGuidesAllComplete():Boolean {
			//guides.sortOn("index",Array.NUMERIC);
			for (var i:int = 0; i < guides.length-1; i++) 
			{
				if (guides[i].state==GuidesState.UNFINISH) 
				{
					return false;
				}
			}
			
			return true;
		}
		
		//判断是不是自己家
		public function get isSelfScene():Boolean {
			if (_currentUser.uid==curSceneUser.uid) 
			{
				return true;
			}else {
				return false;
			}
		}
		
		public function getStudentsCountInRoom():int
		{
			var onDeskStudentNum:uint = 0;
			
			//onDeskStudentNum = (worldState.world as MagicWorld).getStudentOnDeskNum();
			//for (var i:int = 0; i < onDeskStudents.length; i++) 
			//{
				//if ((onDeskStudents[i] as StudentVo).state!=StudentStateType.TEACHOVER) 
				//{
					//onDeskStudentNum++;
				//}
			//}
			//return this.fiddleStudents.length + onDeskStudentNum;
			return (worldState.world as MagicWorld).getStudentNum();
		}
		
		public function getDeskInRoom():Number {
			if (!worldState) 
			{
				trace("getDeskInRoom no worldState");
				return 0;
			}
			var tmplist:Array = worldState.world.items;
			if (!tmplist) 
			{
				return 0;
			}
			var num:int;
			for (var i:int = 0; i < tmplist.length; i++) 
			{
				if (tmplist[i] is Desk) 
					{
						num++;
					}
			}
			
			return num;
		}
		
		public function getStudentsInRoom():Array
		{
			return this.fiddleStudents.concat(this.onDeskStudents);
		}
		
		public function removeFiddleStudent(student:StudentVo):void {
			for (var i:int = 0; i < fiddleStudents.length; i++) 
			{
				if (fiddleStudents[i] == student) 
				{
					fiddleStudents.splice(i, 1);
					EventManager.getInstance().dispatchEvent(new DataManagerEvent(DataManagerEvent.USERINFO_CHANGE));
					return;
				}
			}
		}
		
		public function removeDeskStudent($decor_id:int):void
		{
			for (var i:int = 0; i < onDeskStudents.length; i++) 
			{
				if ($decor_id == onDeskStudents[i].decor_id) 
				{
					onDeskStudents.splice(i, 1);
					return;
				}
			}
		}
		
		public function set decorClass(value:Array):void {
			_decorClass = value;
		}
		
		public function set magicClass(value:Array):void {
			_magicClass = value;
		}
		
		public function get magicClass():Array {
			return _magicClass;
		}
		
		/**
		 * 更新或增加学生数据到桌上学生或闲逛学生列表
		 * @param	svo
		 */
		public function setStudentVo(svo:StudentVo):void
		{
			var i:int;
			for ( i = 0; i < onDeskStudents.length; i++) 
			{
				if (svo.sid == onDeskStudents[i].sid) 
				{
					if (svo.state==StudentStateType.FIDDLE) 
					{
						onDeskStudents.splice(i, 1);
					}else {
						onDeskStudents[i] = svo;
						return;
					}
					
				}
			}
			for ( i = 0; i < fiddleStudents.length; i++) 
			{
				if (svo.sid == fiddleStudents[i].sid) 
				{
					if (svo.state!=StudentStateType.FIDDLE) 
					{
						fiddleStudents.splice(i, 1);
					}else {
						fiddleStudents[i] = svo;
						return;
					}
					
				}
			}
			
			if (svo.state==StudentStateType.FIDDLE) 
			{
				fiddleStudents.push(svo);
			}else {
				onDeskStudents.push(svo);
			}
			
		}
		
		/**
		 * 获得student_vo
		 * @param	svo
		 */
		public function getStudentVoByDecorId($decor_id:int):StudentVo
		{
			
			for (var i:int = 0; i < onDeskStudents.length; i++) 
			{
				if ($decor_id == onDeskStudents[i].decor_id) 
				{
					return onDeskStudents[i];
				}
			}
			
			return null;
		}
		
		/**
		 * 返回在场景中的指定SID的学生数据
		 * @param	sid
		 * @return
		 */
		public function getStudentVoBySid(sid:uint):StudentVo {
			var tmparr:Array = fiddleStudents.concat(onDeskStudents);
			for (var i:int = 0; i < tmparr.length; i++) 
			{
				if (sid == tmparr[i].sid) 
				{
					return tmparr[i];
				}
			}
			
			return null;
		}
		
		public function getUserMagicList():Array
		{
			return magicList;
		}
		
		public function getMagicClass(magic_id:uint):MagicClassVo {
			for (var i:int = 0; i < _magicClass.length; i++) 
			{
				if (magic_id==_magicClass[i].magic_id) 
				{
					return _magicClass[i];
				}
			}
			
			return null;
		}
		
		public function getDecorClassByDid(did:uint):DecorClassVo {
			for (var i:int = 0; i < _decorClass.length; i++) 
			{
				if (did==_decorClass[i].d_id) 
				{
					return _decorClass[i];
				}
			}
			
			return null;
		}
		
		public function set levelInfos(value:Array):void 
		{
			_levelInfos = value;
		}
		
		public function get levelInfos():Array 
		{
			return _levelInfos;
		}
		
		public function get currentUser():UserVo { return _currentUser; }
		
		public function set currentUser(value:UserVo):void 
		{
			_currentUser = value;
			
			EventManager.getInstance().dispatchEvent(new DataManagerEvent(DataManagerEvent.USERINFO_CHANGE));
		}
		
		public function set mixMagics(value:Array):void 
		{
			_mixMagics = value;
		}
		public function get mixMagics():Array { return _mixMagics; }
		
		public function get itemClass():Array { return _itemClass; }
		
		public function set itemClass(value:Array):void 
		{
			_itemClass = value;
		}
		
		public function get transMagicClass():Array { return _transMagicClass; }
		
		public function set transMagicClass(value:Array):void 
		{
			_transMagicClass = value;
		}
		
		public function get taskClass():Array { return _taskClass; }
		
		public function set taskClass(value:Array):void 
		{
			_taskClass = value;
		}
		
		public function get avatars():Array { return _avatars; }
		
		public function set avatars(value:Array):void 
		{
			_avatars = value;
		}
		
		public function get decorBagList():Array { return _decorBagList; }
		
		public function set decorBagList(value:Array):void 
		{
			_decorBagList = value;
		}
		
		public function get enemys():Array 
		{
			return _enemys;
		}
		
		public function set enemys(value:Array):void 
		{
			_enemys = value;
		}
		
		
		//获取人物当前等级
		public function getLevelInfo(level:uint):LevelInfoVo {
			for (var i:int = 0; i < _levelInfos.length; i++) 
			{
				if (_levelInfos[i].level==level) 
				{
					return _levelInfos[i];
				}
			}
			
			return null;
		}
		
		//增加道具物品
		public function addItems(value:Array):void
		{
			for (var m:int = 0; m < value.length; m++) 
			{
				changeItemNumById(value[m],false);
			}
		}
		
		//移除道具物品
		public function removeItems(value:Array):void {
			for (var m:int = 0; m < value.length; m++) 
			{
				changeItemNumById(value[m],true);
			}
		}
		
		private function changeItemNumById(itemvo:ItemVo,isDel:Boolean):void {
			var changeNum:int = isDel ? -itemvo.num : itemvo.num;
			var i_id:int = itemvo.i_id;
			var id:int = itemvo.id;
			
			var geted:Boolean;
			for (var i:int = 0; i < items.length; i++) 
			{
				if (id) 
				{
					if (id==items[i].id) 
					{
						items[i].num += changeNum;
						if (items[i].num<=0) 
						{
							ArrayUtil.removeValueFromArray(items, items[i]);
						}
						geted = true;
						break;
					}
				}else {
					if (i_id==items[i].i_id) 
					{
						items[i].num += changeNum;
						if (items[i].num<=0) 
						{
							ArrayUtil.removeValueFromArray(items, items[i]);
						}
						
						geted = true;
						break;
					}
				}
			}
			if (!geted) {
				if (changeNum > 0) items.push(itemvo);
			}
			
			EventManager.getInstance().dispatchEvent(new TaskStateEvent(TaskStateEvent.NEED_CHECK_STATE));
		}
		
		//增加装饰物
		public function addDecor(value:Array):void
		{
			var geted:Boolean;
			for (var m:int = 0; m < value.length; m++) 
			{
				geted = false;
				for (var i:int = 0; i < decorBagList.length; i++) 
				{
					if (decorBagList[i].d_id==value[m].d_id) 
					{
						(decorBagList[i] as DecorVo).add(value[m] as DecorVo);
						geted = true;
						break;
					}
				}
				if (!geted) 
				{
					decorBagList.push(value[m]);
				}
			}
			
		}
		
		//增加装饰物到自身的仓库里
		public function addDecorBag(value:Array):void
		{
			var geted:Boolean;
			for (var m:int = 0; m < value.length; m++) 
			{
				geted = false;
				for (var i:int = 0; i < decorBagList.length; i++) 
				{
					if (decorBagList[i].d_id==value[m].d_id) 
					{
						(decorBagList[i] as DecorVo).add(value[m] as DecorVo);
						geted = true;
						break;
					}
				}
				if (!geted) 
				{
					decorBagList.push(value[m]);
				}
			}
			
			EventManager.getInstance().dispatchEvent(new TaskStateEvent(TaskStateEvent.NEED_CHECK_STATE));
		}
		
		//减少装饰物到自身的仓库里
		public function removeDecorBag(value:Array):void 
		{
			for (var m:int = 0; m < value.length; m++) 
			{
				for (var i:int = 0; i < decorBagList.length; i++) 
				{
					if (decorBagList[i].d_id==value[m].d_id) 
					{
						(decorBagList[i] as DecorVo).remove(value[m] as DecorVo);
						if ((decorBagList[i] as DecorVo).num<=0) 
						{
							decorBagList.splice(i, 1);
						}
						break;
					}
				}
			}
			EventManager.getInstance().dispatchEvent(new TaskStateEvent(TaskStateEvent.NEED_CHECK_STATE));
		}
		
		/**
		 * 
		 * @param	data
		 * @return	返回这个任务是否新任务
		 */
		public function setTask(data:TaskVo):Boolean
		{
			var tmp:TaskVo;
			for (var i:int = 0; i < tasks.length; i++) 
			{
				tmp = tasks[i] as TaskVo;
				if (tmp.t_id==data.t_id) 
				{
					tmp.state = data.state;
					tmp.fc_curNums = data.fc_curNums;
					return false;
				}
			}
			
			tasks.push(data);
			return true;
		}
		
		//移除这个任务
		public function removeTask(t_id:uint):void
		{
			var tmp:TaskVo;
			for (var i:int = 0; i < tasks.length; i++) 
			{
				tmp = tasks[i] as TaskVo;
				if (tmp.t_id==t_id) 
				{
					tasks.splice(i, 1);
					return;
				}
			}
		}
		
		public function getNpcsBySceneId(sceneId:uint):Array {
			var arr:Array = new Array();
			var tmp:NpcVo;
			for (var i:int = 0; i < npcClass.length; i++) 
			{
				tmp = npcClass[i] as NpcVo;
				if (tmp.sceneId==sceneId) 
				{
					arr.push(tmp);
				}
			}
			return arr;
		}
		
		public function getTasksByNpcId(npcId:uint):Array
		{
			var arr:Array = new Array();
			var tmp:TaskVo;
			for (var i:int = 0; i < tasks.length; i++) 
			{
				tmp = tasks[i] as TaskVo;
				if (tmp.npcId==npcId || tmp.finishNpcId==npcId) 
				{
					arr.push(tmp);
				}
			}
			return arr;
		}
		
		public function getTasksBySceneId(sceneId:uint):Array {
			var arr:Array = new Array();
			var tmp:TaskVo;
			for (var i:int = 0; i < tasks.length; i++) 
			{
				tmp = tasks[i] as TaskVo;
				if (tmp.sceneId==sceneId || tmp.finishSceneId==sceneId || tmp.sceneId==0) 
				{
					arr.push(tmp);
				}
			}
			return arr;
		}
		
		public function getAvatarVo(avatarId:uint):AvatarVo
		{
			for (var i:int = 0; i < _avatars.length; i++) 
			{
				if (avatarId==_avatars[i].avatarId) 
				{
					return _avatars[i];
				}
			}
			return null;
		}
		
		public function getCustomStudentAvatarVo():AvatarVo
		{
			var tmparr:Array = avatars.filter(filterAvatarsStudent);
			
			var tmpindex:uint = Math.floor(Math.random() * tmparr.length);
			
			return tmparr[tmpindex];
		}
		
		private function filterAvatarsStudent(element:*, index:int, arr:Array):Boolean
		{
			return element.type == AvatarType.STUDENT;
		}
		
		public function getSceneClassById(id:uint):SceneClassVo {
			for (var i:int = 0; i < sceneClass.length; i++) 
			{
				if (id == sceneClass[i].sceneId) {
					return sceneClass[i];
				}
			}
			return null;
		}
		
		public function getSceneVoByClass(id:uint,state:uint):SceneVo {
			var tmp:SceneVo;
			var tmpClass:Object;
			for (var i:int = 0; i < sceneClass.length; i++) 
			{
				if (id==sceneClass[i].sceneId) 
				{
					tmpClass = decodeJson(JSON.encode(sceneClass[i]));
					tmp = new SceneVo();
					for (var name:String in tmpClass) 
					{
						if ( tmp.hasOwnProperty(name)) 
						{
							tmp[name] = tmpClass[name];
						}
					}
					tmp.state = state;
					return tmp;
				}
			}
			
			return null;
		}
		
		public function getCanBuyItemClass():Array
		{
			var arr:Array = new Array();
			for (var i:int = 0; i < itemClass.length; i++) 
			{
				if (itemClass[i].sale) 
				{
					arr.push(itemClass[i]);
				}
			}
			
			return arr;
		}
		
		public function getGuidesClass(gid:uint):GuidesClassVo
		{
			for (var i:int = 0; i < guidesClass.length; i++) 
			{
				if (gid==guidesClass[i].gid) 
				{
					return guidesClass[i];
				}
			}
			return null;
		}
		
		public function getCurGuides():GuidesVo {
			//guides.sortOn("index",Array.NUMERIC | Array.DESCENDING);
			for (var i:int = 0; i < guides.length; i++) 
			{
				if (guides[i].state==1) 
				{
					return guides[i];
				}
			}
			return null;
		}
		
		public function getUnlockMagicClass(level:uint):Array
		{
			var arr:Array = new Array();
			var tmp:MagicClassVo;
			for (var i:int = 0; i < magicClass.length; i++) 
			{
				tmp = magicClass[i] as MagicClassVo;
				if (tmp.need_level==level) 
				{
					arr.push(tmp);
				}
			}
			return arr;
		}
		
		/**
		 * 得到可以指定等级解锁的变化术
		 * @param	level
		 * @return
		 */
		public function getUnlockTrans(level:uint):Array
		{
			var arr:Array = new Array();
			var tmp:TransMagicVo;
			for (var i:int = 0; i < _transMagicClass.length; i++) 
			{
				tmp = _transMagicClass[i] as TransMagicVo;
				if (tmp.needLevel==level) 
				{
					arr.push(tmp);
				}
			}
			return arr;
		}
		
		/**
		 * 得到可以指定等级解锁的合成术
		 * @param	level
		 * @return
		 */
		public function getUnlockMixMagic(level:uint):Array
		{
			var arr:Array = new Array();
			var tmp:MixMagicVo;
			for (var i:int = 0; i < mixMagics.length; i++) 
			{
				tmp = mixMagics[i] as MixMagicVo;
				if (tmp.needLevel==level) 
				{
					arr.push(tmp);
				}
			}
			return arr;
		}
		
		/**
		 * 获取下一级房间扩建的尺寸
		 * @param	size
		 * @return
		 */
		public function getNextRoomSizeVo(size:uint):RoomSizeVo
		{
			for (var i:int = 0; i < roomSizeClass.length; i++) 
			{
				if (roomSizeClass[i].sizeX>size) 
				{
					return roomSizeClass[i] as RoomSizeVo;
				}
			}
			return null;
		}
		
		public function getRoomSizeVoBySize(sizeX:uint):RoomSizeVo
		{
			for (var i:int = 0; i < roomSizeClass.length; i++) 
			{
				if ((roomSizeClass[i] as RoomSizeVo).sizeX==sizeX) 
				{
					return roomSizeClass[i] as RoomSizeVo;
				}
			}
			return null;
		}
		
		
		/**
		 * 获取学生类数据
		 * @param	sid	
		 */
		public function getStudentClass(sid:uint):StudentClassVo 
		{
			for (var i:int = 0; i < studentClass.length; i++) 
			{
				var item:StudentClassVo = studentClass[i] as StudentClassVo;
				if (item.sid==sid) 
				{
					return item;
				}
				
			}
			return null;
		}
		
		/**
		 * 
		 * @param	level
		 * @return
		 */
		public function getStudentLevelClass(level:uint):StudentLevelClassVo 
		{
			for (var i:int = 0; i < studentLevelClass.length; i++) 
			{
				var item:StudentLevelClassVo = studentLevelClass[i] as StudentLevelClassVo;
				if (item.level==level) 
				{
					return item;
				}
			}
			
			return null;
		}
		
		//获取房间等级VO
		public function getRoomLevel(level:uint):RoomLevelVo 
		{
			for (var i:int = 0; i < roomLevelClass.length; i++) 
			{
				var item:RoomLevelVo = roomLevelClass[i];
				if (item.level==level) 
				{
					return item;
				}
			}
			return null;
		}
		
		/**
		 * 根据学生的sid获取学生的状态属性（是否领奖，是否解锁）
		 * @param	sid
		 * @return
		 */
		public function getStudentState(sid:uint):StudentStateVo 
		{
			for (var i:int = 0; i < studentStates.length; i++) 
			{
				var item:StudentStateVo = studentStates[i];
				if (item.sid==sid) 
				{
					return item;
				}
			}
			return null;
		}
		
		/**
		 * 获取指定房间等级会解锁的学生
		 * @param	level
		 * @return
		 */
		public function getUnlockStudent(level:uint):StudentStateVo {
			for (var i:int = 0; i < studentStates.length; i++) 
			{
				var item:StudentStateVo = studentStates[i];
				if (item.unLockMp==level) 
				{
					return item;
				}
				
			}
			return null;
		}
		
		
		/**
		 * 记录DIY的内容,放入要提交保存的列表
		 * @param	$isoItem
		 */
		public function recordChangeData($isoItem:IsoItem):void
		{
			var $data:Object = new Object();
			$data.x = $isoItem.x-IsoUtil.roomStart;
			$data.y = $isoItem.y;
			$data.z = $isoItem.z-IsoUtil.roomStart;
			$data.id = $isoItem.data.id;
			$data.d_id = $isoItem.data.d_id;
			$data.mirror = $isoItem.mirror;
			$data.bag_type = 0;
			
			var change_list:Object;
			if ($isoItem.type == DecorType.FLOOR) {
				change_list = floorChangeList;
			} else if ($isoItem.type == DecorType.WALL_PAPER) {
				change_list = wallChangeList;
			} else {
				
				if (decorChangeBagList[$data.id]) 
				{
					if (($isoItem.data as DecorVo).x!=$data.x && ($isoItem.data as DecorVo).z!=$data.z) 
					{
						change_list = decorChangeList;
				
						//更改数据值
						change_list[$data.id] = $data;
					}
					delete decorChangeBagList[$data.id];
				}else {
					change_list = decorChangeList;
				
					//更改数据值
					change_list[$data.id] = $data;
				}
				return;
			}
			change_list[$data.x + '_' + $data.z] = $data;
		}
		
		//获取任务说明
		public function getTaskTips(taskType:uint):TaskTipsVo {
			for (var i:int = 0; i < taskTips.length; i++) 
			{
				if (taskTips[i].taskType==taskType) 
				{
					return taskTips[i] as TaskTipsVo;
				}
			}
			return null;
		}
		//根据UID获取好友属性
		public function getFriendUserVo(_uid:String ):UserVo
		{
			for (var i :int = 0; i < friends.length; i++ )
			{
				if (friends[i].uid == _uid)
				{
					return friends[i] as UserVo;
				}
			}
			return null;
		}
		
		//根据ID获取feed类
		public function getFeedClass(_feedid:uint):FeedVo
		{
			for (var i :int = 0; i < feedClass.length; i++ )
			{
				if (feedClass[i].id == _feedid)
				{
					return feedClass[i] as FeedVo;
				}
			}
			return null;			
		}
		
		/**
		 * 改变当前用户的用户信息
		 * @param	coin
		 * @param	gem
		 * @param	exp
		 * @param	mp
		 */
		public function changeCurUserInfo(coin:int=0, gem:int=0,exp:int=0,mp:int=0):void 
		{
			_currentUser.coin += coin;
			_currentUser.gem += gem;
			_currentUser.exp += exp;
			_currentUser.mp += mp;
			
			EventManager.getInstance().dispatchEvent(new TaskStateEvent(TaskStateEvent.NEED_CHECK_STATE));
		}
		
		/**
		 * 改变当前场景用户的用户信息
		 * @param	coin
		 * @param	gem
		 * @param	exp
		 * @param	mp
		 */
		public function changeCurSceneUserInfo(coin:int=0, gem:int=0,exp:int=0,mp:int=0):void 
		{
			curSceneUser.coin += coin;
			curSceneUser.gem += gem;
			curSceneUser.exp += exp;
			curSceneUser.mp += mp;
		}
		
		public function isMaxRoomSize(tile_x_length:int):Boolean 
		{
			if (tile_x_length>=(roomSizeClass[roomSizeClass.length-1] as RoomSizeVo).sizeX) 
			{
				return true;
			}
			return false;
		}
		
		//根据Type里的数据来删选
		public function findArrayType(arr:Array,type:int):Array
		{
			var newarr:Array = new Array();
			for (var i:int = 0; i < arr.length; i++ )
			{
				if (arr[i].type == type)
				{
					newarr.push(arr[i]);
				}
			}
			
			return newarr;
		}
		
		//寻找当前装饰物在合成面板的第几页
		//pageNum：每页有几个
		public function getDecorPageLength(arr:Array, id:uint,pageNum:uint = 9):int
		{
			var page:int = 0;
			
			for (var i:int = 0; i < arr.length; i++ )
			{
				if ((arr[i] as MixMagicVo).d_id == id)
				{
					i += 1;
					if (i % pageNum == 1)
					{
						page = i / pageNum;
					}
					else
					{
						page = i / pageNum +1;
					}
				}
			}
			
			return page;
		}
		
	}
	
}
class Private {}