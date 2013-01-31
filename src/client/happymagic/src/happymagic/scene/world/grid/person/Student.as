package happymagic.scene.world.grid.person 
{
	import com.friendsofed.isometric.IsoObject;
	import com.friendsofed.isometric.Point3D;
	import com.greensock.OverwriteManager;
	import flash.display.DisplayObjectContainer;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.geom.Point;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	import flash.utils.Timer;
	import happyfish.cacher.bitmapMc.display.BitmapMc;
	import happyfish.cacher.bitmapMc.events.BitmapCacherEvent;
	import happyfish.cacher.CacheSprite;
	import happyfish.manager.module.ModuleManager;
	import happyfish.utils.CustomTools;
	import happyfish.utils.display.ItemOverControl;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.EventManager;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.grid.Person;
	import happyfish.scene.world.WorldState;
	import happyfish.utils.display.McShower;
	import happyfish.utils.SysTracer;
	import happymagic.display.view.MainInfoView;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.display.view.ui.personMsg.PersonMsgManager;
	import happymagic.display.view.ui.PersonPaoView;
	import happymagic.events.ActionStepEvent;
	import happymagic.events.DataManagerEvent;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.InterruptCommand;
	import happymagic.model.command.PickupCommand;
	import happymagic.model.control.TakeResultVoControl;
	import happymagic.model.vo.DecorType;
	import happymagic.model.vo.MagicClassVo;
	import happymagic.model.vo.ResultVo;
	import happymagic.model.vo.StudentStateType;
	import happymagic.model.vo.StudentVo;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.control.AvatarCommand;
	import happymagic.scene.world.grid.item.Decor;
	import happymagic.scene.world.grid.item.Desk;
	import happymagic.scene.world.grid.item.Door;
	import happymagic.scene.world.MagicWorld;
	import happymagic.utils.RequestQueue;
	
	/**
	 * ...
	 * @author Beck
	 */
	public class Student extends Person
	{
		private var _fromDoor:Boolean;
		
		public var desk:Desk;
		
		//学习计时器
		private var waitTimer:Timer = new Timer(1000);
		//钱袋变石头计时器
		private var stoneTimer:Timer = new Timer(1000);
		//中断计时器
		private var interrputTimer:Timer = new Timer(1000);
		
		private var showMoodTimeId:Number;
		//标示是否已离开房间,离开的话不算在房间内学生数内,在goOutRoom时设置
		public var outed:Boolean;
		
		//是否显示tip
		public var showTipFlag:Boolean = false;
		
		public static const STATUS:String = 'status'; 
		
		public function Student($data:Object, $worldState:WorldState, $fromDoor:Boolean = false,__callBack:Function=null) 
		{
			super($data, $worldState, __callBack);
			typeName = "Student";
			this._fromDoor = $fromDoor;
		}
		
		public function setStudentData($data:Object):void
		{
			data = $data as Object;
		}
		
		override protected function makeView():IsoSprite
		{
			if (this._fromDoor) {
				SysTracer.systrace("student makeView state", data["state"]);
				super.makeView();
				
			} else {
				//直接显示到桌子上
				if (this.data.state != StudentStateType.FIDDLE) {
					this.desk = Desk(this._worldState.world.decorInstanceItems[this.data.decor_id]);
					if(desk) this.desk.student = this;
					
					if (this.data.state == StudentStateType.NOTEACH) {
						//如果是等待教状态
						
						if (desk) 
						{
							this.setPoint3D(this.desk.getWalkableSpace());
						}
						//实现人物形象
						super.makeView();
					} else if (this.data.state == StudentStateType.STUDYING) {
						//学习中
						
						//开始倒计时
						this.countDown();
						//设置到位子位置上
						this.setPosition();
						//课桌表现使用中动画
						if (desk) 
						{
							this.desk.playAnimation('desk_play');
							//显示学习时课桌上的魔法动画
							var tmpMagicClass:MagicClassVo = DataManager.getInstance().getMagicClass(data.magic_id);
							if ( tmpMagicClass)
							{
								this.desk.magicMovie(tmpMagicClass.actMovie);
							}else {
								SysTracer.systrace("魔法不存在");
							}
						}
						
						
						
						
						//如果有中断事件
						//if (this.data.event_time != -1) {
							//开始计时器
							//this.interrputCountDown();
							//
						//}
						this.countDown();
						
						super.makeView();
						this.playAnimation('magic');
					} else if (this.data.state == StudentStateType.TEACHOVER) {
						if (desk) 
						{
							this.desk = Desk(this._worldState.world.decorInstanceItems[this.data.decor_id]);
							//课桌显示水晶
							if(desk) this.desk.showCrystal();
						}
						
						//开始水晶失效计时器
						//this.stoneCountDown();
						countDown();
						
						//调用VIEW完成事件
						if (_bodyCompleteCallBack!=null) 
						{
							_bodyCompleteCallBack();
						}
						
						return null;
					} else if (this.data.state == StudentStateType.INTERRUPT) {
						this.setPosition();
						
						super.makeView();
						//播放学生中断动画(四种属性)
						processInterrput();
						//this.playAnimation(STATUS + DataManager.getInstance().userInfo.magic_type);
					}
					
					
				} else {
					var tmpnode:Node = _worldState.getCustomRoomWalkAbleNode();
					this.data.x = tmpnode.x;
					this.data.z = tmpnode.y;
					this.setData(this.data);
					//闲逛
					super.makeView();
				}
			}
			
			//鼠标事件
			view.container.addEventListener(MouseEvent.ROLL_OVER, this.onMouseOver);
			view.container.addEventListener(MouseEvent.ROLL_OUT, this.onMouseOut);
			view.container.addEventListener(MouseEvent.MOUSE_MOVE, this.onMouseOverMove);
			view.container.addEventListener(MouseEvent.CLICK, this.onClick);
			
			return this._view;
		}
		
		/**
		 * 设置学生到课桌位置上
		 */
		private function setPosition():void
		{
			this.desk = Desk(this._worldState.world.decorInstanceItems[this.data.decor_id]);
			if (!desk) 
			{
				return;
			}
			var point3d:Point3D = desk.getWalkableSpace();
			
			
			this.data.x = point3d.x;
			this.data.z = point3d.z;
			this.setData(this.data);
			
			setCanWalk(false);
		}
		
		/**
		 * 设置学生站立位置的可行走属性
		 * 当学生在位子上学习时让别人不要从他那里穿过
		 * @param	value
		 */
		public function setCanWalk(value:Boolean):void {
			//var p:Point3D = desk.getWalkableSpace();
			//_worldState.grid.setWalkable(p.x, p.z,value);
		}
		
		public function processInterrput():void
		{
			if (this.data.event_time == 0) {
				
				loadingState = true;
				
				
				this.playAnimation(STATUS + DataManager.getInstance().getMagicClass(data.magic_id).magic_type.toString());
				
				this.data.state = StudentStateType.INTERRUPT;
				bubbling();
				//引导事件
				EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_STUDENT_EVENT_HAPPEN));
			}
		}
		
		public function setPoint3D($3d:Point3D):void
		{
			this.data.x = $3d.x;
			this.data.y = $3d.y;
			this.data.z = $3d.z;
			
			super.setData(this.data);
			
		}
		
		public function  gotoDeskCommand($desk_id:int):void
		{
			bubbling();
			mouseEnabled = false;
			//停止闲逛进程
			stopFiddleClear();
			//记录课桌
			this.desk = Desk(this._worldState.world.decorInstanceItems[$desk_id]);
			if (!desk) 
			{
				SysTracer.systrace("erro deskid:",$desk_id);
			}
			this.desk.student = this;
			
			var point3d:Point3D = desk.getWalkableSpace();
			
			var fiddleTowards:Point3D = desk.getDeskSpace();
			
			this.addCommand( new AvatarCommand(point3d, null, fiddleTowards, 1500, MAGIC,gotoDeskCommand_complete,"",true));
		}
		
		private function gotoDeskCommand_complete():void
		{
			mouseEnabled = true;
			_fromDoor = false;
			bubbling();
			setCanWalk(false);
			
			//引导事件
			EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_STUDENT_TODESK));
		}
		
		override protected function view_complete():void 
		{
			
			//跟着桌子的方向
			if (desk) 
			{
				if (desk.mirror==1) 
				{
					curDir = Person.RIGHT;
				}
			}
			
			super.view_complete();
			view.container.buttonMode = true;
			
			//如果是从门里出来的,就走到桌子或闲逛
			if (_fromDoor) 
			{
				//显示状态泡
				bubbling();
				//从门中出来,表现动画
				//SysTracer.systrace("student view_com state", data["state"]);
				if (this.data.state != StudentStateType.FIDDLE) {
					setPoint3D(new Point3D(x, 0, z));
					//走到桌子
					setTimeout(gotoDeskCommand,100,data.decor_id);
				} else {
					//闲逛
					setPoint3D(new Point3D(x, 0, z));
					fiddle();
				}
			}else {
				if (data.state==StudentStateType.NOTEACH) 
				{
					stopAnimation(MOVE);
				}else if(data.state == StudentStateType.STUDYING){
					//引导事件
					EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_STUDENT_TODESK));
				}
				//如果不是从门出来的学生,直接表现需求icon
				if (bubbleUI) {
					//bubbleUI.y = - _view.container.getChildAt(0).height;
					bubbleUI.initPosition();
				}else {
					bubbling();
				}
				if(data.state == StudentStateType.FIDDLE) fiddle();
				
			}
			
			var maininfo:MainInfoView = ModuleManager.getInstance().getModule(ModuleDict.MODULE_MAININFO) as MainInfoView;
			maininfo.initInfo();
		}
		
		/**
		 * 显示魔法学习需求泡
		 * 或是升级领奖泡
		 */
		public function bubbling():void
		{
			if (bubbleUI) 
			{
				bubbleUI.remove();
				bubbleUI = null;
				//return;
			}
			
			//判断是否有升级领奖
			if (DataManager.getInstance().getStudentState(data.sid).needAward && DataManager.getInstance().isSelfScene) 
			{
				bubbleUI = new PersonPaoView(this, "studentNeedAwardIcon", true,63);
				bubbleUI.mouseChildren = false;
				
				mouseEnabled = true;
				return;
			}
			
			//闲逛
			if (data.state==StudentStateType.FIDDLE) 
			{
				bubbleUI = new PersonPaoView(this, "pao_needDesk", true,30);
				//mouseEnabled = true;
				return;
			}
			
			if (data.state == StudentStateType.INTERRUPT) 
			{
				bubbleUI = new PersonPaoView(this, "pao_needHelp", true,35);
				loadingState = true;
				return;
			}
			
			if (data.state == StudentStateType.STUDYING) 
			{
				mouseEnabled = true;
				return;
			}
			
			//要求学魔法
			if (DataManager.getInstance().getMagicClass(data.magic_id)) 
			{
				bubbleUI = new PersonPaoView(this, DataManager.getInstance().getMagicClass(data.magic_id).class_name, true);
				bubbleUI.mouseChildren = false;
				
				mouseEnabled = true;
				
				//bubble.className = DataManager.getInstance().getMagicClass(data.magic_id).class_name;
			}else {
				trace("找不到魔法");
				return;
			}
		}
		
		public function removeBubble():void
		{
			if (this.bubbleUI) {
				bubbleUI.remove();
				bubbleUI = null;
			}
		}
		
		/**
		 * 学生开始闲逛
		 */
		override public function fiddle():void
		{
			if (this.data.state != StudentStateType.FIDDLE) {
				return;
			}
			
			super.fiddle();
		}
		
		override protected function fiddleWaitFun():void 
		{
			if (CustomTools.customInt(0,1)) 
			{
				PersonMsgManager.getInstance().addMsg(this, "拉拉拉~",2000,fiddle);
			}else {
				fiddleId = setTimeout(fiddle, 5000);
			}
		}
		
		private function showFiddleMood():void
		{
			//showMood("pao_heart");
			if (CustomTools.customInt(0, 10) == 0) {
				PersonMsgManager.getInstance().addMsg(this, "拉拉拉~",2000,showMoodComplete);
			}else {
				fiddle();
			}
			
			//showMoodTimeId=setTimeout(showMoodComplete, 5000);
		}
		
		private function showMoodComplete():void
		{
			fiddle();
			showMoodTimeId = 0;
		}
		
		public function stopFiddleClear():void {
			showMoodTimeId = 0;
			if ( fiddleId ) clearTimeout(fiddleId);
		}
		
		/**
		 * 更新所有计时器状态
		 */
		public function countDown():void
		{
			startWaitTimer();
			interrputCountDown();
			stoneCountDown();
		}
		
		/**
		 * 更新学习计时器状态
		 */
		private function startWaitTimer():void {
			if (!waitTimer.hasEventListener(TimerEvent.TIMER)) 
			{
				waitTimer.addEventListener(TimerEvent.TIMER, waitTimerFunc);
			}
			if ((data as StudentVo).time>=0 && (data as StudentVo).state==StudentStateType.STUDYING) 
			{
				if (!waitTimer.running)
				{
					waitTimer.start();
				}
			}else {
				if (waitTimer.running)
				{
					waitTimer.stop();
				}
			}
		}
		
		/**
		 * 学生学习中timer事件
		 * @param	e
		 */
		public function waitTimerFunc(e:TimerEvent):void
		{
			if (data.time<=0) 
			{
				data.time = 0;
			}else {
				this.data.time--;
			}
			//this.data.time--;
			if (DisplayManager.deskTip && this.showTipFlag) {
				DisplayManager.deskTip.countdown = data.time;
			}

			if (this.data.time <= 0) {
				waitTimer.stop();
				
				//走出去
				this.goOutRoom();
			}
		}
		
		
		/**
		 * 更新钱袋计时器
		 */
		private function stoneCountDown():void
		{
			if (!stoneTimer.hasEventListener(TimerEvent.TIMER)) 
			{
				stoneTimer.addEventListener(TimerEvent.TIMER, this.stoneWaitTimerFunc);
			}
			if ((data as StudentVo).stone_time && (data as StudentVo).state==StudentStateType.TEACHOVER) 
			{
				if (!stoneTimer.running) 
				{
					stoneTimer.start();
				}
			}else {
				if (stoneTimer.running) 
				{
					stoneTimer.stop();
				}
			}
			
		}
		
		public function stoneWaitTimerFunc(e:TimerEvent):void
		{
			this.data.stone_time--;
			
			if (DisplayManager.deskTip && this.showTipFlag) {
				DisplayManager.deskTip.countdown = this.data.stone_time;
			}

			if (this.data.stone_time <= 0) {
				stoneTimer.stop();
				
				//走出去
				this.turnToStone();
			}
		}
		
		/**
		 * 更新中断时间计时器
		 */
		private function interrputCountDown():void
		{
			if (!interrputTimer.hasEventListener(TimerEvent.TIMER)) {
				interrputTimer.addEventListener(TimerEvent.TIMER, this.interrputWaitTimerFunc);
			}
			
			if (data.event_time>-1 && (data as StudentVo).state==StudentStateType.STUDYING) 
			{
				if (!interrputTimer.running) 
				{
					interrputTimer.start();
				}
			}else {
				if (interrputTimer.running) 
				{
					interrputTimer.stop();
				}
			}
			
		}
		/**
		 * 中断事件计时器方法
		 * @param	e
		 */
		public function interrputWaitTimerFunc(e:TimerEvent):void
		{
			//if (data.event_time<=0) 
			//{
				//data.event_time = 0;
			//}else {
				//data.event_time--;
			//}
			data.event_time--;
			
			if (this.data.event_time <= 0) {
				waitTimer.stop();
				interrputTimer.stop();
				
				//表现中断的状态,修改状态为中断状态
				this.processInterrput();
			}
		}
		
		//变石头
		public function turnToStone():void
		{
			trace('....');
			this.desk.showStone();
		}
		
		public function resetData($data:Object):void
		{
			this.data = $data;
		}
		
		public function goOutRoom():void
		{
			//停止课桌动画
			this.desk.playAnimation('desk_stop');
			//this.desk.asset.bitmap_movie_mc.gotoAndStop(1);
			
			//停止人物动画
			
			//删除魔法动画
			this.desk.removeMagicMovie();
			//关闭课桌上的tips
			if (DisplayManager.deskTip) {
				if (DisplayManager.deskTip.view.parent) 
				{
					_worldState.view.isoView.removeChild(DisplayManager.deskTip.view);
				}
				
				showTipFlag = false;
				DisplayManager.deskTip = null;
			}
			
			//设置课桌学生位置可走
			setCanWalk(true);
			
			//找一个门走出去
			var point3d:Point3D;
			var tmpDoor:Door = (_worldState.world as MagicWorld).getCustomDoor();
			if (tmpDoor) 
			{
				point3d = tmpDoor.getOutIsoPosition();
			}else {
				point3d = new Point3D(view.container.screenX, 0, view.container.screenY);
			}
			
			this.addCommand( new AvatarCommand(point3d, this.remove));
			
			outed = true;
			
			//丢下水晶
			this.desk.showCrystal();
			
			//变石头
			//this.stoneCountDown();
			countDown();
			this.data.state = StudentStateType.TEACHOVER;
			
			//走出去,数据改变
			
			//从桌上学生队列中移除
			DataManager.getInstance().removeDeskStudent(this.desk.data.id);
			DataManager.getInstance().removeFiddleStudent(data as StudentVo);
			
			//通知用户信息刷新
			var event:DataManagerEvent = new DataManagerEvent(DataManagerEvent.USERINFO_CHANGE);
			EventManager.getInstance().dispatchEvent(event);
		}
		
        
		
		public function requestInterrupt():void
		{
			var request_queue:RequestQueue = RequestQueue.getInstance();
			
			if (request_queue.interruptDecorIds.length != 0) {
				//发起请求
				var iCommand:InterruptCommand = new InterruptCommand();
				iCommand.addEventListener(Event.COMPLETE, handle);
				
				//iCommand.load(request_queue.interruptDecorIds);
				//request_queue.unset(RequestQueue.TYPE_INTERRUPTDECOR);
				
				iCommand.load([request_queue.delOne(RequestQueue.TYPE_INTERRUPTDECOR)]);
			} else {
				this.handle();
			}
		}
		
		public function awardComplete():void 
		{
			loadingState = true;
			DataManager.getInstance().getStudentState(data.sid).needAward = 0;
			bubbling();
		}
		
		/**
		 * 重设学生状态表现
		 */
		public function initStateDisplay():void 
		{
			
		}
		
		private function handle(e:Event = null):void
		{
			
			loadingState = true;
			if (e) 
			{
				e.target.removeEventListener(Event.COMPLETE, handle);
			}
			if (!alive) 
			{
				return;
			}
			//寻找此桌子对应的返回数据
			var result:ResultVo = DataManager.getInstance().interruptResults[data.decor_id];
			if (result.isSuccess) {
				
				//播放学习魔法特效
				var teachMvShower:McShower = new McShower(openDoorMv, view.container);
				
				this.playAnimation('magic');
				
				var point:Point = this._worldState.world.player.view.container.parent.localToGlobal(
				new Point(this._worldState.world.player.view.container.screenX, this._worldState.world.player.view.container.screenY));
				
				//通知信息面板和飘屏
				TakeResultVoControl.getInstance().take(result,true,point);
				
				//桌子上变施法状态
				desk.playAnimation('desk_play');
				//桌子上增加魔法课程效果动画
				desk.magicMovie(DataManager.getInstance().getMagicClass(data.magic_id).actMovie);
				
				//更改状态
				data.state = StudentStateType.STUDYING;
				bubbling();
				this.countDown();
				
				//引导事件
				EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_STUDENT_EVENT_CLICK));
			} else {
				//漂屏
				//if (this._worldState.world.player) 
				//{
					//var point_error:Point = this._worldState.world.player.view.container.parent.localToGlobal(
					//new Point(this._worldState.world.player.view.container.screenX, this._worldState.world.player.view.container.screenY));
					//
					//EventManager.getInstance().showPiaoStr(PiaoMsgType.TYPE_BAD_STRING, result.content, point_error);
				//}
			}
			
			DataManager.getInstance().pickUpResults[data.decor_id] = null;
			
		}
		
		
		public function set data(value:Object):void {
			_data = value;
		}
		
		override protected function onClick(event:MouseEvent) : void
        {
			if (event.target==bubbleUI)
			{
				view.container.dispatchEvent(new GameMouseEvent(GameMouseEvent.CLICK, this, typeName, event));
			}else {
				super.onClick(event);
			}
        }
		
		public function clearTimer():void {
			if (waitTimer) 
			{
				if (waitTimer.hasEventListener(TimerEvent.TIMER)) 
				{
					waitTimer.removeEventListener(TimerEvent.TIMER, waitTimerFunc);
				}
				waitTimer = null;
			}
			
			if (stoneTimer) 
			{
				if (stoneTimer.hasEventListener(TimerEvent.TIMER)) 
				{
					stoneTimer.removeEventListener(TimerEvent.TIMER, stoneWaitTimerFunc);
				}
				stoneTimer = null;
			}
			
			if (interrputTimer) 
			{
				if (interrputTimer.hasEventListener(TimerEvent.TIMER)) 
				{
					interrputTimer.removeEventListener(TimerEvent.TIMER, interrputWaitTimerFunc);
				}
				interrputTimer = null;
			}
			
		}
		
		override public function clear():void 
		{
			super.clear();
			
			clearTimer();
		}
		
		
	}

}