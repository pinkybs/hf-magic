package happymagic.scene.world.grid.person 
{
	import com.friendsofed.isometric.IsoUtils;
	import com.friendsofed.isometric.Point3D;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.geom.Point;
	import flash.utils.Timer;
	import happyfish.display.view.PersonChatsView;
	import happyfish.scene.astar.Node;
	import happyfish.scene.camera.CameraControl;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.grid.Person;
	import happyfish.scene.world.WorldState;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.StudentVo;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.control.AvatarCommand;
	import happymagic.scene.world.MagicWorld;
	
	/**
	 * ...
	 * @author Beck
	 */
	public class Player extends Person
	{
		public var userVo:UserVo;
		private var transTimer:Timer;
		public function Player($data:Object, $worldState:WorldState,_x:uint,_y:uint,__callBack:Function=null) 
		{
			userVo = $data as UserVo;
			
			var playerObj:Object = new Object();
			
			playerObj.class_name = (userVo.trans_className && userVo.trans_time>0) ? userVo.trans_className : userVo.className;
			playerObj.x = _x;
			playerObj.z = _y;
			
			super(playerObj, $worldState,__callBack);
			typeName = "Player";
			
			//变化术倒计时
			if (userVo.trans_time) 
			{
				initTransTimer();
			}
		}
		
		
		/**
		 * 根据当前数据重设用户形象
		 */
		public function refreshView(_callBack:Function=null):void {
			var classname:String=(userVo.trans_className && userVo.trans_time>0) ? userVo.trans_className : userVo.className;
			resetView(classname, _callBack);
		}
		
		public function initTransTimer():void
		{
			if (userVo.trans_time && !transTimer) 
			{
				transTimer = new Timer(1000);
				transTimer.addEventListener(TimerEvent.TIMER, transTimerEvent);
				transTimer.start();
			}
		}
		
		private function transTimerEvent(e:TimerEvent):void 
		{
			if (DataManager.getInstance().currentUser.uid==userVo.uid) 
			{
				userVo.trans_time = DataManager.getInstance().currentUser.trans_time;
			}else if (DataManager.getInstance().curSceneUser.uid==userVo.uid) {
				userVo.trans_time = DataManager.getInstance().curSceneUser.trans_time;
			}
			//userVo.trans_time--;
			//trace("transTimerEvent",userVo.trans_time);
			if (userVo.trans_time<=0) 
			{
				transTimer.stop();
				transTimer.removeEventListener(TimerEvent.TIMER, transTimerEvent);
				transTimer = null;
				
				userVo.trans_mid = 0;
				userVo.trans_className = "";
				//更换形象
				resetView(userVo.className);
			}
		}
		
		override protected function makeView():IsoSprite 
		{
			super.makeView();
			
			
			
			view.container.addEventListener(MouseEvent.ROLL_OVER, this.onMouseOver);
			view.container.addEventListener(MouseEvent.ROLL_OUT, this.onMouseOut);
			view.container.addEventListener(MouseEvent.MOUSE_MOVE, this.onMouseOverMove);
			_view.container.addEventListener(MouseEvent.CLICK, onClick);
			return _view;
		}
		
		override protected function view_complete():void 
		{
			super.view_complete();
			
			//光环
			var halo:playerHalo = new playerHalo();
			//var halo:playerHalo_old = new playerHalo_old();
			halo.mouseChildren=
			halo.mouseEnabled = false;
			if (isSelf) {
				//如果是自己,就显示光环
				view.container.addChildAt(halo, 0);
			}else {
				//如果是好友,就显示他的名字
				showName(userVo.name);
			}
			
			hideGlow();
			view.container.mouseChildren=false
			
		}
		
		override public function go(e:MouseEvent = null):void 
		{
			//CameraControl.getInstance().followTarget(view.container, _worldState.view.isoView.camera);
			var tmpp:Point3D = _worldState.view.targetGrid();
			//(_worldState.world as MagicWorld).setPlayerFlag(tmpp);
			super.go(e);
			
			
			//var targetP:Point3D = IsoUtils.screenToIso(new Point(this.isoView.camera.mouseX-6 + IsoUtil.TILE_SIZE/2, 
							//this.isoView.camera.mouseY + IsoUtil.TILE_SIZE/2-isoView.sceneY));
			//var avatar_command:AvatarCommand = new AvatarCommand(_worldState.view.targetGrid(),null,null,0,null,null,"walk");
			//this.addCommand(avatar_command);
		}
		
		override protected function reachedGoal():void 
		{
			//(_worldState.world as MagicWorld).clearPlayerFlag();
			super.reachedGoal();
		}
		
		public function get isSelf():Boolean {
			return userVo.uid == DataManager.getInstance().currentUser.uid;
		}
	}

}