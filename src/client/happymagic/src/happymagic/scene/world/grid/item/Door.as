package happymagic.scene.world.grid.item 
{
	import com.friendsofed.isometric.Point3D;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.geom.Point;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	import flash.utils.Timer;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.EventManager;
	import happyfish.manager.SoundEffectManager;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.grid.Wall;
	import happyfish.scene.world.WorldState;
	import happyfish.scene.world.WorldView;
	import happyfish.utils.display.McShower;
	import happyfish.utils.SysTracer;
	import happymagic.events.ActionStepEvent;
	import happymagic.events.DataManagerEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.DecorVo;
	import happymagic.scene.world.grid.person.Student;
	import happymagic.scene.world.MagicWorld;
	
	/**
	 * 门的几种状态
	 * 1.倒计时中,不能点(传送中)
	 * 2.倒计时结束,场景中有空闲位置,可点
	 * 3.倒计时结束,场景中无空闲位置,不可点
	 * @author Beck
	 */
	public class Door extends WallDecor
	{
		private var waitTimer:Timer;
		private var doorMove:MovieClip;
		private var doorOpen:MovieClip;
		
		public var showTipFlag:Boolean = false;
		private var opening:Boolean;
		private var callCloseId:uint;
		public function Door($data:Object, $worldState:WorldState,__callBack:Function=null) 
		{
			super($data, $worldState,__callBack);
			view.container.sortPriority = 10;
		}
		
		override protected function makeView():IsoSprite
		{
			
			var iso:IsoSprite = super.makeView();
			
			//this.countDown();
			return iso;
		}
		
		override protected function view_complete():void 
		{
			super.view_complete();
			view.container.buttonMode = true;
		}
		
		override public function remove():void 
		{
			super.remove();
			//通知world从门队列中清除此门
			(_worldState.world as MagicWorld).removeDoorFromList(this);
		}
		
		override protected function bodyComplete():void 
		{
			super.bodyComplete();
			
			//调整汽泡的位置
			countDown();
		}
		
		/**
		 * 更新状态表现
		 */
		public function countDown():void
		{
			if (this._data.door_left_time != 0 || this._data.door_left_students_num <= 0) {
				startTimer();
				
				//传送中
				this.countDownUI();
			} else {
				//大拇指
				this.doorOpenUI();
			}
			
			diyState = _diyState;
		}
		
		private function startTimer():void {
			if (!waitTimer) 
			{
				waitTimer = new Timer(1000);
				waitTimer.addEventListener(TimerEvent.TIMER, this.waitTimerFunc);
			}
			if (!DataManager.getInstance().isDiying) 
			{
				if (!waitTimer.running) 
				{
					waitTimer.start();
				}
			}
			
		}
		
		override public function setMirror($x:int):void 
		{
			super.setMirror($x);
			
			countDown();
		}
		
		/**
		 * 传送中图标
		 */
		public function countDownUI():void
		{
			clearIcon();
			
			return;
			
			if (!this.doorMove) {
				this.doorMove = new ui_doormove;
				doorMove.stop();
				doorMove.cacheAsBitmap = true;
			}
			
			this._view.container.addChild(this.doorMove);
			
			if (mirror) 
			{
				this.doorMove.x = -IsoUtil.TILE_SIZE/2;
			}else {
				this.doorMove.x = IsoUtil.TILE_SIZE/2;
			}
			
			if (this._view.container.getChildAt(0).height == 0) {
				this.doorMove.y =  -IsoUtil.TILE_SIZE/ 2;
			} else {
				this.doorMove.y =  - this._view.container.getChildAt(0).height ;
			}
		}
		
		private function clearIcon():void
		{
			if (doorOpen) 
				{
					if (doorOpen.parent) 
					{
						_view.container.removeChild(doorOpen);
					}
				}
				if (doorMove) 
				{
					if (doorMove.parent) 
					{
						_view.container.removeChild(doorMove);
					}
					
				}
				doorOpen = null;
				doorMove = null;
		}
		
		public function resetWallView():void {
			//还原背后的墙
			var wall:Wall = _worldState.world.getWallByNode(gridPos.x,gridPos.z) as Wall;
			if(wall) wall.resetWallView();
		}
		
		override public function move($grid_pos:Point3D):void 
		{
			super.move($grid_pos);
			
			if (this.isDoorArea(this.gridPos.x, this.gridPos.z)) {
				
				this.removeIsoTile();
				
				this.setMirror($grid_pos.x);
				
				this.addIsoTile();
			}
		}
		
		
		override public function finishMove():void 
		{
			super.finishMove();
			
			(_worldState.world as MagicWorld).addDoorToList(this);
			
			//挖洞
			var wall:Wall = _worldState.world.getWallByNode(gridPos.x, gridPos.z) as Wall;
			if(wall) wall.cutDoor(asset.bitmap_movie_mc);
			
		}
		
		public function getOutIsoPosition():Point3D {
			var tmpP:Point3D = gridPos.clone();
			if (mirror) 
			{
				tmpP.z += 1;
			}else {
				tmpP.x += 1;
			}
			return tmpP;
		}
		
		public function getNode():Node {
			return new Node(gridPos.x, gridPos.z);
		}
		
		public function getInOutNode():Array {
			var tmparr:Array = new Array();
			
			if (mirror) 
			{
				tmparr.push(new Node(gridPos.x, gridPos.z+1));
				tmparr.push(new Node(gridPos.x, gridPos.z-1));
			}else {
				tmparr.push(new Node(gridPos.x+1, gridPos.z));
				tmparr.push(new Node(gridPos.x-1, gridPos.z));
			}
			return tmparr;
		}
		
		
		/**
		 * 可以进人,大拇指图标
		 */
		public function doorOpenUI():void
		{
			if (!this.doorMove) {
				//this.doorMove = new ui_doormove;
			}
			clearIcon();
			
			this.doorOpen = new ui_dooropen;
			//doorOpen.stop();
			//doorOpen.cacheAsBitmap = true;
			this._view.container.addChild(this.doorOpen);
			
			if (mirror) 
			{
				this.doorOpen.x = -IsoUtil.TILE_SIZE/2;
			}else {
				this.doorOpen.x = IsoUtil.TILE_SIZE/2;
			}
			
			if (this._view.container.getChildAt(0).height == 0) {
				this.doorOpen.y =  -IsoUtil.TILE_SIZE / 2;
			} else {
				this.doorOpen.y =  - this._view.container.getChildAt(0).height;
			}
			
			//引导事件
			EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_DOOR_READY));
		}
		
		/**
		 * 学生出来请求完成事件
		 * @param	e
		 */
		public function outDoorStudents(e:Event):void 
		{
			loadingState = true;
			e.target.removeEventListener(Event.COMPLETE, outDoorStudents);
			
			if (!e.target.data.students) 
			{
				return;
			}else {
				if (e.target.data.students.length<=0) 
				{
					return;
				}
			}
			var p:Point = new Point(view.container.screenX, view.container.screenY);
				p = view.container.parent.localToGlobal(p);
				p = DisplayManager.sceneSprite.globalToLocal(p);
				
				var openMv:McShower = new McShower(teachMv, DisplayManager.sceneSprite);
				openMv.x = p.x;
				openMv.y = p.y;
				if (mirror) {
					openMv.setMcScaleXY( -1, 1);
				}
				
			openDoor();
			
			var openDoorStudents:Array = DataManager.getInstance().openDoorStudents[(data as DecorVo).id];
			if (openDoorStudents) {
				for (var i:int = 0; i < openDoorStudents.length; i++ ) {
					//创建学生
					var tmpPoint:Point3D = getStudentFromDoorPosition();
					openDoorStudents[i].x = tmpPoint.x;
					openDoorStudents[i].z = tmpPoint.z;
					var student:Student = new Student(openDoorStudents[i], this._worldState, true);
					//加入场景
					this._worldState.world.addItem(student);
				}
			}
			DataManager.getInstance().openDoorStudents[(data as DecorVo).id] = [];
			var event:DataManagerEvent = new DataManagerEvent(DataManagerEvent.USERINFO_CHANGE);
			EventManager.getInstance().dispatchEvent(event);
			
			//引导事件
			EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_DOOR_CLICK));
		}
		
		public function hideToolTips():void {
			if (DisplayManager.doorTip && showTipFlag) 
				{
					if (DisplayManager.doorTip.view.parent) 
					{
						_worldState.view.isoView.removeChild(DisplayManager.doorTip.view);
					}
					
				}
				showTipFlag = false;
		}
		
		/**
		 * 返回门的入口
		 * @return
		 */
		private function getStudentFromDoorPosition():Point3D
		{
			var position:Point3D;
			if (mirror) {
				position = new Point3D(this.x, 0, this.z-1);
			} else {
				position = new Point3D(this.x-1, 0, this.z);
			}
			return position;
		}
		
		public function waitTimerFunc(e:TimerEvent):void
		{
			this._data.door_left_time--;
			
			if (DisplayManager.doorTip && this.showTipFlag) {
				DisplayManager.doorTip.countdown = this._data.door_left_time;
			}

			if (this._data.door_left_time <= 0) {
				waitTimer.stop();
				
				//大拇指
				this.doorOpenUI();
			}
		}
		
		/**
		 * 是否可放置判断
		 * @return
		 */
		override public function positionIsValid():Boolean 
		{
			if (!isDoorArea(this.gridPos.x, this.gridPos.z)) {
				return false;
			}
			
			var node:Node;
			var canPut:Boolean = true;
			
			var xsize:uint = grid_size_x;
			var zsize:uint = grid_size_z;
			
			for (var i:int = 0; i < xsize; i++) {
				for (var j:int = 0; j < zsize; j++) {
					//如果是建筑自己的所在位置则验证通过
					if (this.nodes[this.gridPos.x + i]) {
						if (this.nodes[this.gridPos.x + i][this.gridPos.z + j]) {
							continue;
						}
					}
					
					if (!this._worldState.checkInRoom(this.gridPos.x + i, this.gridPos.z + j)) {
						canPut=false;
					}else {
						
						var tmpitem:IsoItem = _worldState.world.getNodeItem(gridPos.x,gridPos.z);
						
						if (tmpitem) 
						{
							if (tmpitem is WallDecor) 
							{
								canPut= false;
							}
							
						}
					}
				}
			}
			
			//根据镜像判断门口的格子是否有东西
			var checkNode:IsoItem;
			if (mirror==0) 
			{
				checkNode = _worldState.world.getNodeItem(this.gridPos.x + 1, this.gridPos.z);
			}else {
				checkNode = _worldState.world.getNodeItem(this.gridPos.x, this.gridPos.z + 1);
			}
			if (checkNode) 
			{
				if (checkNode!=this) 
				{
					canPut = false;
				}
				
			}
			
			
			//如果不是所有格都是自己,就返回false
			return canPut;
		}
		
		public function openDoor():void
		{
			
			if (!opening) 
			{
				opening = true;
				//门打开
				asset.bitmap_movie_mc.gotoAndPlayLabels("open",true);
				//this.asset.bitmap_movie_mc.playToStop();
				
				//音效
				SoundEffectManager.getInstance().playSound(new sound_opendoor());
			}
			if (callCloseId) 
			{
				clearTimeout(callCloseId);
			}
			callCloseId=setTimeout(callCloseDoor, 2000);
		}
		
		private function callCloseDoor():void
		{
			callCloseId = 0;
			_worldState.world["doorControl"].closeDoor(this);
		}
		
		public function closeDoor():void {
			opening = false;
			asset.bitmap_movie_mc.gotoAndPlayLabels("close",true);
			//this.asset.bitmap_movie_mc.playToStop();
			//音效
			SoundEffectManager.getInstance().playSound(new sound_opendoor());
		}
		
		
		public function get isReady():Boolean {
			return _data.door_left_time <= 0;
		}
		
		override public function set diyState(value:Boolean):void 
		{
			super.diyState = value;
			
			if (doorMove) {
				doorMove.visible = !value;
			}
			
			if (doorOpen) 
			{
				doorOpen.visible = !value;
			}
		}
		
		public function get width():int
		{
			return this._view.container.getChildAt(0).width;
		}
		
		public function get height():int
		{
			return this._view.container.getChildAt(0).height;
		}
		
        override protected function onClick(event:MouseEvent) : void
        {
			typeName = "Door";
			if (event.target==doorMove) 
			{
				view.container.dispatchEvent(new GameMouseEvent(GameMouseEvent.CLICK, this, typeName, event));
			}else if (event.target==doorOpen) 
			{
				view.container.dispatchEvent(new GameMouseEvent(GameMouseEvent.CLICK, this, typeName, event));
			}else {
				super.onClick(event);
			}
			
			typeName = "WallDecor";
        }
	}

}