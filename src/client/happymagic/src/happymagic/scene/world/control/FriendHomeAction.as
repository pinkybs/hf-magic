package happymagic.scene.world.control 
{
	import com.friendsofed.isometric.Point3D;
	import flash.geom.Point;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.WorldState;
	import happymagic.display.view.desk.DeskTip;
	import happymagic.display.view.door.DoorTip;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.StudentStateType;
	import happymagic.scene.world.award.AwardItemView;
	import happymagic.scene.world.bigScene.EnemyView;
	import happymagic.scene.world.grid.item.Desk;
	import happymagic.scene.world.grid.item.Door;
	import happymagic.scene.world.grid.person.Student;
	import happymagic.utils.RequestQueue;
	/**
	 * ...
	 * @author jj
	 */
	public class FriendHomeAction extends MouseMagicAction
	{
		
		public function FriendHomeAction($state:WorldState) 
		{
			super($state, false);
		}
		
		override public function onEnemyClick(e:GameMouseEvent):void {
			var enemy:EnemyView = e.item as EnemyView;
			
			enemy.showHitMv();
			enemy.changeHp( -1);
			
			//屏蔽地板点击事件
			skipBackgroundClick = true;
		}
		
		override public function onEnemyOver(event:GameMouseEvent):void 
		{
			var enemy:EnemyView = event.item as EnemyView;
			enemy.showHp();
			enemy.stopMove();
		}
		
		override public function onEnemyOut(event:GameMouseEvent):void 
		{
			var enemy:EnemyView = event.item as EnemyView;
			enemy.hideHp();
			if(!enemy.killed) enemy.fiddle();
		}
		
		override public function onAwardItemOver(event:GameMouseEvent):void 
		{
			var award:AwardItemView = event.item as AwardItemView;
			award.out();
		}
		
		override public function onBackgroundClick(event:GameMouseEvent):void 
		{
			if (skipBackgroundClick) 
			{
				skipBackgroundClick = false;
				return;
			}
			var flag:Boolean = DataManager.getInstance().isDraging;
			if (!DataManager.getInstance().isDraging) state.world.player.go();
			DataManager.getInstance().isDraging = false;
		}
		
		/**
		 * 鼠标over学生
		 * @param	event
		 */
        override public function onStudentOver(event:GameMouseEvent) : void
        {
			//学生在学习或中断状态时显示tips
			if (event.item.data.state == StudentStateType.STUDYING || event.item.data.state == StudentStateType.INTERRUPT) {
				var desk_tip:DeskTip;
				if (DisplayManager.deskTip) 
				{
					desk_tip = DisplayManager.deskTip;
				}else {
					desk_tip = new DeskTip();
				}

				desk_tip.data = event.item.data;
				this.state.view.isoView.addChild(desk_tip.view);
				desk_tip.view.x = event.item.view.container.screenX;
				desk_tip.view.y = event.item.view.container.screenY - IsoUtil.TILE_SIZE * 3.5;
				
				Student(event.item).showTipFlag = true;
			}
            return;
        }
		
		/**
		 * 学生鼠标out事件
		 * @param	event
		 */
        override public function onStudentOut(event:GameMouseEvent) : void
        {
			//隐藏tips
			if (event.item.data.state == StudentStateType.STUDYING || event.item.data.state == StudentStateType.INTERRUPT) {
				if (DisplayManager.deskTip) {
					var tmp:*= DisplayManager.deskTip.view;
					if (tmp.parent) 
					{
						this.state.view.isoView.removeChild(DisplayManager.deskTip.view);
					}
					Student(event.item).showTipFlag = false;
				}
			}
            return;
        }
		
		/**
		 * 点击课桌
		 * @param	event
		 */
        override public function onDeskClick(event:GameMouseEvent) : void
        {
			if (Desk(event.item).student.data.can_steal==0) 
			{
				return;
			}
			//如果课桌上有水晶
			if (Desk(event.item).crystal) {
				
				skipBackgroundClick = true;
				
				//已变石头的不可偷
				if (Desk(event.item).is_stone) 
				{
					//飘石头信息
					EventManager.getInstance().showPiaoStr(PiaoMsgType.TYPE_BAD_STRING,LocaleWords.getInstance().getWord("stone_error"));
					
					return;
				}
				
				//暂停课桌的鼠标响应
				Desk(event.item).loadingState = false;
				Desk(event.item).playGetMoneyMv();
				
				//课桌请求队列增加偷水晶操作
				RequestQueue.getInstance().add(RequestQueue.TYPE_PICKDECOR, event.item.data.id);
				
				var p3d:Point3D = Desk(event.item).getMaigcSpace();
				var fiddleTowards:Point3D = Desk(event.item).getWalkableSpace();
				
				//给学生增加行为,移动到课桌边上,行为完成时调用课桌的收取水晶方法
				this.state.world.player.addCommand(
						new AvatarCommand(p3d, null, fiddleTowards, 1500, 'magic', Desk(event.item).requestPick));
			}
            return;
        }
		
		/**
		 * 点击学生
		 * @param	event
		 */
        override public function onStudentClick(event:GameMouseEvent) : void
        {
			
			if (Student(event.item).data.state == StudentStateType.INTERRUPT) {
				//学生在中断状态
				
				//屏蔽地板点击事件
				skipBackgroundClick = true;
				
				//后台请求队列加入处理中断行为
				RequestQueue.getInstance().add(RequestQueue.TYPE_INTERRUPTDECOR, event.item.data.decor_id);
				
				var p3d:Point3D = Student(event.item).desk.getMaigcSpace();
				var fiddleTowards:Point3D = Student(event.item).desk.getWalkableSpace();
				
				//主角增加到学生前行为
				this.state.world.player.addCommand(
						new AvatarCommand(p3d, null, fiddleTowards, 1500, 'magic', Student(event.item).requestInterrupt));
			}
			return;
        }
		
		
		
		/**
		 * 墙上道具over事件
		 * @param	event
		 */
        override public function onWallDecorOver(event:GameMouseEvent) : void
        {
			//如果是门,显示tips
			if (event.item is Door) {
				var door_tip:DoorTip;
				if (!DisplayManager.doorTip) 
				{
					door_tip = new DoorTip();
				}
				event.item.showGlow();
				door_tip = DisplayManager.doorTip;
				door_tip.data = event.item.data as DecorVo;
				door_tip.setDoor(event.item as Door);
				
				state.view.isoView.addChild(door_tip.view);
				
				var p:Point = new Point(event.item.view.container.screenX, event.item.view.container.screenY - IsoUtil.TILE_SIZE * 3.5);
				p = event.item.view.container.parent.localToGlobal(p);
				p = door_tip.view.parent.globalToLocal(p);
				
				if (event.item.mirror) 
				{
					door_tip.view.x = p.x-IsoUtil.TILE_SIZE/2;
				}else {
					door_tip.view.x = p.x+IsoUtil.TILE_SIZE/2;
					
				}
				door_tip.view.y = p.y;
				
				Door(event.item).showTipFlag = true;
			}
            return;
        }
		
		/**
		 * 墙上道具out事件
		 * @param	event
		 */
        override public function onWallDecorOut(event:GameMouseEvent) : void
        {
			//如果是门,隐藏tips
			if (event.item is Door) {
				if (DisplayManager.doorTip) {
					event.item.hideGlow();
					Door(event.item).hideToolTips();
				}
			}
            return;
        }
	}

}