package happymagic.scene.world.grid.item 
{
	import adobe.utils.CustomActions;
	import com.friendsofed.isometric.Point3D;
	import com.greensock.TweenLite;
	import com.greensock.TweenMax;
	import flash.display.PixelSnapping;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.filters.GlowFilter;
	import flash.geom.Point;
	import flash.utils.Timer;
	import happyfish.cacher.CacheSprite;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.grid.Person;
	import happyfish.utils.display.BtnStateControl;
	import happyfish.utils.display.ItemOverControl;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.mouse.MouseManager;
	import happyfish.manager.SoundEffectManager;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.WorldState;
	import happyfish.utils.SysTracer;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.events.ActionStepEvent;
	import happymagic.events.DataManagerEvent;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.events.PickCoinEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.mouse.MagicMouseIconType;
	import happymagic.model.command.PickupCommand;
	import happymagic.model.control.TakeResultVoControl;
	import happymagic.model.vo.ItemVo;
	import happymagic.model.vo.ResultVo;
	import happymagic.model.vo.StudentStateType;
	import happymagic.model.vo.StudentVo;
	import happymagic.scene.world.award.AwardItemManager;
	import happymagic.scene.world.award.AwardType;
	import happymagic.scene.world.grid.person.Student;
	import happymagic.scene.world.MagicWorld;
	import happymagic.utils.RequestQueue;
	
	/**
	 * ...
	 * @author Beck
	 */
	public class Desk extends Decor
	{
		private var waitTimer:Timer;
		private var hasPlayPickMv:Boolean;
		
		public var crystal:deskCrystalIcon;
		
		public var student:Student;
		
		public var magicMovieC:CacheSprite;
		
		public var is_stone:Boolean = false;
		
		public static const LABEL_STOP:String = "desk_stop";
		public static const LABEL_PLAY:String = "desk_play";
		
		
		public function Desk($data:Object, $worldState:WorldState,__callBack:Function=null) 
		{
			super($data, $worldState,__callBack);
			typeName = "Desk";
			stopLabel = LABEL_STOP;
			
		}
		
		/**
		 * 返回是否可以走到
		 * @return	可走为true 不可走为false
		 */
		public function checkCanWalkTo():Boolean {
			var doorList:Vector.<Door> = (_worldState.world as MagicWorld).doorList;
			var tmpdoorPoint:IsoItem;
			var startPoint:Point;
			var endPoint:Point;
			var path:Array;
			for (var i:int = 0; i < doorList.length; i++) 
			{
				tmpdoorPoint = doorList[i];
				startPoint = new Point(tmpdoorPoint.x,tmpdoorPoint.z);
				endPoint = new Point(x, z);
				path = _worldState.world.findPath(startPoint, endPoint, true);
				if (path) 
				{
					return true;
				}
			}
			
			return false;
		}
		
		//override public function move($grid_pos:Point3D):void 
		//{
			//super.move($grid_pos);
			//
			//if (!checkCanWalkTo()) {
				//showCantWalkIcon();
			//}else {
				//hideCantWalkIcon();
			//}
		//}
		
		override public function remove():void 
		{
			super.remove();
			
			if (student) 
			{
				student.remove();
			}
		}
		
		public function countDown():void
		{
			if (this._data.door_left_time != 0 && this._data.door_left_students_num <= 0) {
				waitTimer.addEventListener(TimerEvent.TIMER, this.waitTimerFunc);
				waitTimer.start();
			}
		}
		
		public function waitTimerFunc(e:TimerEvent):void
		{
			this._data.door_left_time--;
			
			if (DisplayManager.deskTip) {
				DisplayManager.deskTip.countdown = this._data.door_left_time;
			}
			
			if (this._data.door_left_time <= 0) {
				waitTimer.stop();
			}
		}
		
		/**
		 * 显示课桌上的魔法动画
		 * @param	$class_name
		 */
		public function magicMovie($class_name:String):void
		{
			if (magicMovieC) 
			{
				removeMagicMovie();
			}
			this.magicMovieC = new CacheSprite(true);
			if (mirror) {
				magicMovieC.scaleX = -1;
			}else {
				magicMovieC.scaleX = 1;
			}
			this.magicMovieC.className = $class_name;
			
			//TODO 貌似还是需要侦听动画加载完成后再开始播放,或是
			if (this.magicMovieC.bitmap_movie_mc) {
				this.magicMovieC.bitmap_movie_mc.play();
			}
			
			this._view.container.addChild(this.magicMovieC);
		}
		
		public function removeMagicMovie():void
		{
			if (magicMovieC) 
			{
				if(magicMovieC.parent) this._view.container.removeChild(this.magicMovieC);
			}
			
		}
		
		/**
		 * 课桌显示水晶
		 */
		public function showCrystal():void
		{
			if (crystal) 
			{
				return;
			}
			this.crystal = new deskCrystalIcon();
			//手型
			MouseManager.getInstance().registObjectMouseIcon(crystal, MouseManager.getInstance().getMouseIcon(MagicMouseIconType.PICK_HAND));
			//over时高亮
			ItemOverControl.getInstance().addOverItem(crystal, crystalShowGlow, crystalHideGlow,true);
			crystal.mouseChildren = false;
			crystal.buttonMode = true;
			crystal.cacheAsBitmap = true;
			crystalReady();
			
			if (student.data.can_steal==0) 
			{
				BtnStateControl.setBtnState(crystal, false);
			}
			
			_view.container.addChild(crystal);
			
			//引导事件
			EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_TEACH_COMPLETE));
		}
		
		public function crystalShowGlow(e:MouseEvent):void {
			//MouseManager.getInstance().setTmpIcon(MouseManager.getInstance().getMouseIcon(MagicMouseIconType.PICK_HAND));
			if (crystal) 
			{
				crystal.filters = [new GlowFilter(16776960, 1, 5, 5, 10, 1, false, false)];
			}
			
		}
		
		public function crystalHideGlow(e:MouseEvent):void {
			//MouseManager.getInstance().clearTmpIcon();
			if (crystal) 
			{
				crystal.filters = [];
			}
		}
		
		override public function setMirro(_mirror:int):void 
		{
			super.setMirro(_mirror);
			
			if (student) 
			{
				if (mirror==1) 
				{
					student.curDir = Person.RIGHT;
				}else {
					student.curDir = Person.DOWN;
				}
				
				if (student.data.state==StudentStateType.NOTEACH) 
				{
					student.stopAnimation(Person.MOVE);
				}else if(student.data.state == StudentStateType.STUDYING){
					student.asset.bitmap_movie_mc.gotoAndPlayLabels(student.moviePrefix + "_" + student.curDir);
					if (mirror) {
						magicMovieC.scaleX = -1;
					}else {
						magicMovieC.scaleX = 1;
					}
				}
				
				//student.asset.bitmap_movie_mc.gotoAndPlayLabels(student.moviePrefix + "_" + student.curDir);
			}
		}
		
		override protected function onClick(e:MouseEvent):void 
		{
			if (e.target is deskCrystalIcon) 
			{
				view.container.dispatchEvent(new GameMouseEvent(GameMouseEvent.CLICK, this, typeName, e));
			}
			super.onClick(e);
		}
		
		private function crystalReady():void {
			//crystal.callFunc('gotoAndStop', DataManager.getInstance().currentUser.magic_type);
			crystal.gotoAndStop(1);
		}
		
		/**
		 * 移除水晶显示对象
		 */
		public function removeCrystal():void
		{
			if (crystal) {
				this._view.container.removeChild(crystal);
				this.crystal = null;
			}
			is_stone = false;
		}
		
		public function showStone():void
		{
			if (crystal) {
				//crystal.callFunc('gotoAndStop', 4);
				crystal.gotoAndStop("stone");
				this.is_stone = true;
			}
		}
		
		/**
		 * 执行拾取队列请求操作
		 * 此方法现在是由主角对象在做完行走到课桌面前,并表现完动作动画后再调用
		 */
		public function requestPick():void
		{
			hasPlayPickMv = true;
			
			var request_queue:RequestQueue = RequestQueue.getInstance();
			
			//判断捡水晶请求队列是否有内容,如有,就一并提交
			if (request_queue.pickDecorIds.length != 0) {
				//发起请求，请求队列内所有课桌
				var pickupCommand:PickupCommand = new PickupCommand();
				pickupCommand.addEventListener(Event.COMPLETE, this.handle);
				
				//pickupCommand.load(request_queue.pickDecorIds);
				//清空捡钱队列
				//request_queue.unset(RequestQueue.TYPE_PICKDECOR);
				
				pickupCommand.load([request_queue.delOne(RequestQueue.TYPE_PICKDECOR)]);
				
				
			} else {
				this.handle();
			}
		}
		
		private function handle(e:Event = null):void
		{
			if (!hasPlayPickMv) 
			{
				return;
			}
			
			//寻找此桌子对应的返回数据
			var result:ResultVo = DataManager.getInstance().pickUpResults[_data.id];
			var changeStudent:StudentVo = DataManager.getInstance().pickUpStudentResults[_data.id];
			var items:Array = DataManager.getInstance().pickUpItems[_data.id];
			
			
			if (!result) 
			{	
				//侦听捡钱请求完成
				//EventManager.getInstance().addEventListener(PickCoinEvent.PICK_COMPLETE, pickComplete);
				return;
			}else {
				if (e) 
				{
					e.target.removeEventListener(Event.COMPLETE, this.handle);
				}
			}
			
			hasPlayPickMv = false;
			if (result.isSuccess) {
				
				//引导事件
				EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_DESKCRYSTAL_CLICK));
				
				var point:Point;
				try 
				{
					point = this._worldState.world.player.view.container.parent.localToGlobal(
					new Point(this._worldState.world.player.view.container.screenX, this._worldState.world.player.view.container.screenY));
				} catch (e:Error) {
					
				}
				
				
				
				//得到的东西掉落
				var tmpP:Point3D = getWalkableSpace();
				var awards:Array = new Array();
				
				//掉落物品
				if (items) 
				{
					var tmpitem:ItemVo;
					for (var i:int = 0; i < items.length; i++) 
					{
						
						tmpitem = items[i] as ItemVo;
						awards.push({ type:AwardType.ITEM, num:tmpitem.num, point:tmpP,id:tmpitem.i_id });
					}
					AwardItemManager.getInstance().addAwards(awards);
				}
				
				if (!this.is_stone) {
					//通知信息面板和飘屏
					//TakeResultVoControl.getInstance().take(result, false, point);
					
					AwardItemManager.getInstance().addAwardsByResultVo(result,[],tmpP);
					
				} else {
					//飘石头信息
					EventManager.getInstance().showPiaoStr(PiaoMsgType.TYPE_BAD_STRING, LocaleWords.getInstance().getWord("stone_error"),point);
				}
				
				
				
				//音效
				SoundEffectManager.getInstance().playSound(new sound_pickup());
				
				//按返回扣除水晶,如果在自己家或扣完,就清空水晶
				(student.data as StudentVo).coin -= result.coin;
				if (student.data.coin>0 && !this.is_stone && !result.coin==0 && !DataManager.getInstance().isSelfScene) 
				{
					//水晶没扣完,表现钱袋不能再捡
					student.data.can_steal = 0;
					BtnStateControl.setBtnState(crystal, false);
				}else {
					//移除水晶
					(student.data as StudentVo).coin = 0;
					this.removeCrystal();
					student.clear();
					student = null;
					
					//通知用户信息刷新
					var event:DataManagerEvent = new DataManagerEvent(DataManagerEvent.USERINFO_CHANGE);
					EventManager.getInstance().dispatchEvent(event);
					
					if (changeStudent) 
					{
						//如果有改变的学生，移动这个学生上桌
						fiddleGoToDesk(changeStudent);
					}
					
				}
			
			} else {
				
				//漂屏
				var point_error:Point = this._worldState.world.player.view.container.parent.localToGlobal(
					new Point(this._worldState.world.player.view.container.screenX, this._worldState.world.player.view.container.screenY));
					
				EventManager.getInstance().showPiaoStr(PiaoMsgType.TYPE_BAD_STRING, result.content, point_error);
			}
			//清除返回记录
			DataManager.getInstance().pickUpResults[_data.id] = null;
			//清除掉落物数据
			DataManager.getInstance().pickUpItems[_data.id] = null;
			//清除学生数据
			DataManager.getInstance().pickUpStudentResults[_data.id] = null;
			//主角执行下一步行为
			//this._worldState.world.player.shiftCommand();
			
			//打开课桌的鼠标响应
			loadingState = true;
		}
		
		
		/**
		 * 找一个空闲的学生,设置指定学生数据,走到课桌上
		 * @param	studentvo
		 */
		public function fiddleGoToDesk(studentvo:Object):void
		{
			var changeStudent:Student = (_worldState.world as MagicWorld).getStudent(studentvo.sid);
			if (changeStudent) {
				//把这个学生从闲逛学生列表中清除
				DataManager.getInstance().removeFiddleStudent(changeStudent.data as StudentVo);
				
				changeStudent.setStudentData(studentvo);
				//放入课桌上学生列表
				//DataManager.getInstance().setStudentVo(changeStudent.data as StudentVo);
				
				changeStudent.gotoDeskCommand(this.data.id);
			}else {
				SysTracer.systrace("cant find student ", studentvo.sid);
			}
		}
		
		override public function set diyState(value:Boolean):void 
		{
			super.diyState = value;
			
			if (value) 
			{
				if (crystal) crystal.visible = false;
				if (magicMovieC) magicMovieC.visible = false;
				
			}else {
				if (crystal) crystal.visible = true;
				if (magicMovieC) magicMovieC.visible = true;
			}
		}
		
		public function getWalkableSpace():Point3D
		{
			return new Point3D(this.x, this.y, this.z);
		}
		
		/**
		 * 返回课桌的学生位置边上可行的任一格
		 * @param	fromNode	从哪一位置到课桌，会自动选择路线最近的一个格子返回
		 * @return
		 */
		public function getMaigcSpace(fromNode:Point=null):Point3D
		{
			
			if (!fromNode) {
				fromNode = new Point();
			}else {
				
			}
			
			var arroundNodes:Array = new Array();
			arroundNodes.push(new Point3D(x - 1, y, z));
			arroundNodes.push(new Point3D(x + 1, y, z ));
			arroundNodes.push(new Point3D(x, y, z - 1));
			arroundNodes.push(new Point3D(x, y, z + 1));
			
			var tmpNode:Node;
			var tmpBtw:Number;
			var tmparr:Array=new Array();
			for (var i:int = 0; i < arroundNodes.length; i++) 
			{
				
				if (_worldState.checkInRoom(arroundNodes[i].x, arroundNodes[i].z)) 
				{
					tmpNode = _worldState.grid.getNode(arroundNodes[i].x, arroundNodes[i].z);
					//如果该点是墙或超出地图范围
					if (_worldState.isWallArea(tmpNode.x,tmpNode.y) || !tmpNode.walkable) 
					{
						
					}else {
						tmpBtw = Point.distance(fromNode, new Point(arroundNodes[i].x, arroundNodes[i].z));
						tmparr.push({node:tmpNode,btw:tmpBtw});
					}
				}
			}
			
			tmparr.sortOn("btw", Array.NUMERIC);
			
			if (tmparr.length>0) 
			{
				return new Point3D(tmparr[0].node.x, 0, tmparr[0].node.y);
			}else 
			{
				return null;
			}
			
		}
		
		
		public function getDeskSpace():Point3D
		{
			if (mirror==1) 
			{
				return new Point3D(this.x+1, this.y, this.z);
			}else {
				return new Point3D(this.x, this.y, this.z + 1);
				
			}
			
		}
		
		/**
		 * 播放桌上钱被点时的交互表现
		 */
		public function playGetMoneyMv():void 
		{
			if (crystal) TweenMax.to(crystal, .3, { scaleX:1.1, scaleY:1.1, tint:0xffffff, yoyo:true,repeat:1 } );
		}
		
	}

}