package happymagic.scene.world.control 
{
	import com.friendsofed.isometric.IsoUtils;
	import com.friendsofed.isometric.Point3D;
	import flash.display.DisplayObjectContainer;
	import flash.display.Stage;
	import flash.events.Event;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import happyfish.display.view.IconView;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.mouse.MouseManager;
	import happyfish.manager.ShareObjectManager;
	import happyfish.scene.camera.CameraControl;
	import happyfish.scene.camera.MovieMaskView;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.grid.Person;
	import happyfish.scene.world.WorldState;
	import happyfish.utils.CustomTools;
	import happyfish.utils.display.CameraSharkControl;
	import happyfish.utils.display.McShower;
	import happyfish.utils.SysTracer;
	import happymagic.display.control.MagicEnoughCheckCommand;
	import happymagic.display.view.desk.DeskTip;
	import happymagic.display.view.door.DoorTip;
	import happymagic.display.view.itembox.ItemShopView;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.display.view.roomUp.RoomUpView;
	import happymagic.display.view.task.TaskInfoView;
	import happymagic.display.view.ui.AwardResultView;
	import happymagic.display.view.ui.personMsg.PersonMsgManager;
	import happymagic.events.ActionStepEvent;
	import happymagic.events.MagicClassBookEvent;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.events.SysMsgEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.mouse.MagicMouseIconType;
	import happymagic.model.command.ClickDoorCommand;
	import happymagic.model.command.StudentAwardCommand;
	import happymagic.model.command.TestCommand;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.MagicClassVo;
	import happymagic.model.vo.NpcVo;
	import happymagic.model.vo.ResultVo;
	import happymagic.model.vo.StudentStateType;
	import happymagic.model.vo.StudentVo;
	import happymagic.scene.world.award.AwardItemManager;
	import happymagic.scene.world.award.AwardItemView;
	import happymagic.scene.world.award.AwardType;
	import happymagic.scene.world.bigScene.EnemyView;
	import happymagic.scene.world.bigScene.NpcChatsView;
	import happymagic.scene.world.bigScene.NpcView;
	import happymagic.scene.world.grid.item.Desk;
	import happymagic.scene.world.grid.item.Door;
	import happymagic.scene.world.grid.person.Student;
	import happymagic.scene.world.MagicWorld;
	import happymagic.utils.RequestQueue;
	/**
	 * ...
	 * @author Beck
	 */
	public class MouseDefaultAction extends MouseMagicAction
	{
		
		public function MouseDefaultAction($state:WorldState, $stack_flg:Boolean = false) 
		{
			super($state, $stack_flg);
		}
		
        override public function onDecorOver(event:GameMouseEvent) : void
        {
            return;
        }

        override public function onDecorClick(event:GameMouseEvent) : void
        {
            return;
        }

        override public function onDecorOut(event:GameMouseEvent) : void
        {
            return;
        }
		
        override public function onDeskOver(event:GameMouseEvent) : void
        {
            return;
        }
		
		override public function onMassesClick(event:GameMouseEvent):void 
		{
			//CameraControl.getInstance().followTarget(event.item.asset, state.view.isoView.camera);
			//CameraControl.getInstance().centerTweenTo(event.item.asset, state.view.isoView.camera);
		}
		
		override public function onRoomUpItemClick(event:GameMouseEvent):void {
			//屏蔽地板点击事件
			skipBackgroundClick = true;
				
			var roomUpView:RoomUpView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_ROOMUP, ModuleDict.MODULE_ROOMUP_CLASS,false, AlginType.CENTER) as RoomUpView;
			roomUpView.setData(DataManager.getInstance().roomSizeClass);
			DisplayManager.uiSprite.setBg(roomUpView);
		}
		
		override public function onRoomUpItemOver(event:GameMouseEvent):void {
			event.item.showGlow();
		}
		
		override public function onRoomUpItemOut(event:GameMouseEvent):void {
			event.item.hideGlow();
		}
		
		override public function onAwardItemOver(event:GameMouseEvent):void 
		{
			//skipBackgroundClick = true;
			var award:AwardItemView = event.item as AwardItemView;
			award.out();
		}
		
		override public function onAwardItemClick(event:GameMouseEvent):void {
			
			//skipBackgroundClick = true;
			//var award:AwardItemView = event.item as AwardItemView;
			//award.out();
		}
		
		//点击地板
		override public function onBackgroundClick(event:GameMouseEvent):void 
		{
			if (skipBackgroundClick) 
			{
				skipBackgroundClick = false;
				return;
			}
			var flag:Boolean = DataManager.getInstance().isDraging;
			if (!DataManager.getInstance().isDraging && state.world.player) state.world.player.go();
			//if (!DataManager.getInstance().isDraging && state.world.player) state.world.player.setPos(state.view.targetGrid());
			DataManager.getInstance().isDraging = false;
			
			
			//镜头移动测试
			//CameraControl.getInstance().centerTweenToPoint(new Point(state.view.stage.mouseX, state.view.stage.mouseY),
				//state.view.isoView.camera);
			
			//new TestCommand().test("data/award.txt");
			
			//var awards:Array = new Array();
			//awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_GEM, num:11 } ));
			//awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_COIN, num:11 } ));
			//awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_EXP, num:1341 } ));
			//awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_MP_MAX, num:131 } ));
			//awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_DESK_LIMIT, num:1 } ));
			//
			//var awardwin:AwardResultView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_AWARD_RESULT, ModuleDict.MODULE_AWARD_RESULT_CLASS,true) as AwardResultView;
				//awardwin.setData( { name:"新手引导奖励", awards:awards } );
			
			//var p:Point = new Point(state.view.stage.mouseX, state.view.stage.mouseY);
			//var mvContainer:DisplayObjectContainer = state.world.player.view.container.parent;
			//var learnMv:McShower = new McShower(learnTransMv, mvContainer);
			//learnMv.changeRate(10, 71, 83);
			//
			//p = mvContainer.globalToLocal(p);
			//learnMv.x = p.x;
			//learnMv.y = p.y;
			
			
			//PersonMsgManager.getInstance().addMsg(state.world.player, "123233sasdfasdfasdfasdfasfasdfasfdasfdasfdasd443");
			
			//if (ShareObjectManager.getInstance().soundEffect) 
			//{
				//var tmpP:Point3D = state.view.targetGrid();
			//
				//var tmptype:int;
				//var tmpArr:Array = new Array();
				//var tmpNum:int;
				//for (var i:int = 0; i < 3; i++) 
				//{
					//tmptype = (Math.floor(Math.random() * 3) + 1);
					//tmpNum = CustomTools.customInt(13, 123);
					//tmpArr.push( { type:tmptype, num:tmpNum, point:tmpP } );
					//tmpArr.push( { type:2, num:tmpNum, point:tmpP } );
				//}
				//AwardItemManager.getInstance().addAwards(tmpArr);
			//}
			
			//var tmpP:Point3D = state.view.targetGrid();
			//var tmpArr:Array = new Array();
			//tmpArr.push( { type:1, num:18, point:tmpP } );
			//AwardItemManager.getInstance().addAwards(tmpArr);
			
			//var tmpP:Point3D = state.view.targetGrid();
			//var tmpArr:Array = new Array();
			//tmpArr.push( { type:AwardType.ITEM, num:1, id:8301,point:tmpP } );
			//AwardItemManager.getInstance().addAwards(tmpArr);
			
			//var tmpP:Point3D = state.view.targetGrid();
			//var tmpArr:Array = new Array();
			//tmpArr.push( { type:AwardType.DECOR, num:1, id:191008,point:tmpP } );
			//AwardItemManager.getInstance().addAwards(tmpArr);
			
			//var icon:IconView = new IconView(50, 50,new Rectangle(DisplayManager.uiSprite.mouseX,DisplayManager.uiSprite.mouseY),true);
			//DisplayManager.uiSprite.stage.addChild(icon);
			//icon.setData("decor.1.shuiyaojin");
			
			
			//EventManager.getInstance().showPiaoStr(1, "123");
			
			//var maskmv:MovieMaskView = new MovieMaskView();
			//maskmv.showMaskMv( DisplayManager.uiSprite, DisplayManager.uiSprite.stage.stageWidth,
					//DisplayManager.uiSprite.stage.stageHeight, 100,1);
		}
		
		/**
		 * 鼠标over学生
		 * @param	event
		 */
        override public function onStudentOver(event:GameMouseEvent) : void
        {
			if ((event.item as Student).data.state == StudentStateType.FIDDLE) 
			{
				return;
			}
			event.item.showGlow();
			MouseManager.getInstance().setTmpIcon(MouseManager.getInstance().getMouseIcon(MagicMouseIconType.STUDENT_HAND));
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
				var p:Point = new Point(event.item.view.container.screenX, event.item.view.container.screenY - IsoUtil.TILE_SIZE * 3.5);
				p = event.item.view.container.parent.localToGlobal(p);
				p = desk_tip.view.parent.globalToLocal(p);
				desk_tip.view.x = p.x;
				desk_tip.view.y = p.y;
				
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
			event.item.hideGlow();
			MouseManager.getInstance().setTmpIcon(null);
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
			//SysTracer.systrace(Desk(event.item).view.position);
			//SysTracer.systrace(state.world.player.view.position);
			//SysTracer.systrace(state.world.player.view.sortPriority);
			//如果课桌上有水晶
			if (Desk(event.item).crystal) {
				
				//屏蔽地板点击事件
				skipBackgroundClick = true;
				
				//暂停课桌的鼠标响应
				Desk(event.item).loadingState = false;
				
				Desk(event.item).playGetMoneyMv();
				
				//课桌请求队列增加拣取水晶操作
				RequestQueue.getInstance().add(RequestQueue.TYPE_PICKDECOR, event.item.data.id);
				
				var p3d:Point3D = Desk(event.item).getMaigcSpace();
				var fiddleTowards:Point3D = Desk(event.item).getWalkableSpace();
				
				//给学生增加行为,移动到课桌边上,行为完成时调用课桌的收取水晶方法
				state.world.player.addCommand(
						//new AvatarCommand(p3d, null, fiddleTowards, 1500, 'magic', Desk(event.item).requestPick));
						new AvatarCommand(p3d, null , fiddleTowards, 1500, 'magic',Desk(event.item).requestPick));
			}
            return;
        }

        override public function onDeskOut(event:GameMouseEvent) : void
        {
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
				event.item.hideGlow();
				Door(event.item).hideToolTips();
			}
            return;
        }
		
		/**
		 * 点击门
		 * @param	event
		 */
        override public function onDoorClick(event:GameMouseEvent) : void
        {
			//屏蔽地板点击事件
			skipBackgroundClick = true;
			
			var door:Door = Door(event.item);
			
			door.hideToolTips();
			var pos:Point3D = IsoUtil.gridToIso(new Point3D(event.item.x, 0, event.item.z));
			
			//door.view.container.local3DToGlobal() 
			//判断场景是否还能容纳
			if (DataManager.getInstance().currentUser.students_limit <= DataManager.getInstance().getStudentsCountInRoom()) {
				//漂字
				var msgs:Array = [[PiaoMsgType.TYPE_BAD_STRING, LocaleWords.getInstance().getWord("roomStudentFull")]];
				var event_msg:PiaoMsgEvent = new PiaoMsgEvent(PiaoMsgEvent.SHOW_PIAO_MSG, msgs, event.target.stage.mouseX, event.target.stage.mouseY);
				EventManager.getInstance().dispatchEvent(event_msg);
				return;
			}
			
			//判断是否倒计时已到
			if (door.data.door_left_time > 0) {
				//漂字
				var msgs_new:Array = [[PiaoMsgType.TYPE_BAD_STRING, LocaleWords.getInstance().getWord("notReachedTime")]];
				var event_piao_msg:PiaoMsgEvent = new PiaoMsgEvent(PiaoMsgEvent.SHOW_PIAO_MSG, msgs_new, event.mouseEvent.stageX, event.mouseEvent.stageY);
				EventManager.getInstance().dispatchEvent(event_piao_msg);
				return;
			}
			
			
			door.loadingState = false;
			//倒计时到了,请求服务器端
			var command:ClickDoorCommand = new ClickDoorCommand();
			command.addEventListener(Event.COMPLETE, door.outDoorStudents);
			command.load(door);
			
			//屏蔽地板点击事件
			skipBackgroundClick = true;
			
            return;
        }
		
		/**
		 * 点击学生
		 * @param	event
		 */
        override public function onStudentClick(event:GameMouseEvent) : void
        {
			//屏蔽设置了不可交互的学生
			if (!Student(event.item).mouseEnabled) 
			{
				return;
			}
			
			if (DataManager.getInstance().getStudentState(Student(event.item).data.sid).needAward) {
				//屏蔽地板点击事件
				skipBackgroundClick = true;
				//如果学生需要领升级奖
				requestStudentAward(Student(event.item));
			}else if (Student(event.item).data.state == StudentStateType.NOTEACH) {
				//学生在等待教学状态
				
				SysTracer.systrace("student click teach",Student(event.item).data.sid);
				
				//打开魔法课程列表magic
				var tmpe:MagicClassBookEvent = new MagicClassBookEvent(MagicClassBookEvent.SHOW_EVENT);
				tmpe.student= event.item as Student;
				EventManager.getInstance().dispatchEvent(tmpe);
				
				//屏蔽地板点击事件
				skipBackgroundClick = true;
				
				//引导事件
				EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_STUDENT_NEED_CLICK));
				
			} else if (Student(event.item).data.state == StudentStateType.INTERRUPT) {
				
				//检查魔法是否够,每次处理中断消耗3点魔法
				if (!new MagicEnoughCheckCommand().check(3)) 
				{
					return;
				}
				
				Student(event.item).loadingState = false;
				
				//屏蔽地板点击事件
				skipBackgroundClick = true;
				
				//学生在中断状态
				
				//隐藏学业生tips信息
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
				
				//后台请求队列加入处理中断行为
				RequestQueue.getInstance().add(RequestQueue.TYPE_INTERRUPTDECOR, event.item.data.decor_id);
				
				var p3d:Point3D = Student(event.item).desk.getMaigcSpace(new Point(event.item.gridPos.x,event.item.gridPos.z));
				var fiddleTowards:Point3D = Student(event.item).desk.getWalkableSpace();
				
				//主角增加到学生前行为
				this.state.world.player.addCommand(
						new AvatarCommand(p3d, null, fiddleTowards, 1500, 'magic', Student(event.item).requestInterrupt));
			}
			return;
        }
		
		private function requestStudentAward(student:Student):void 
		{
			student.loadingState = false;
			
			var command:StudentAwardCommand = new StudentAwardCommand(student.awardComplete);
			command.change(student.data.sid);
		}
		
		override public function onNpcOver(e:GameMouseEvent):void {
			e.item.showGlow();
		}
		
		override public function onNpcOut(e:GameMouseEvent):void {
			e.item.hideGlow();
		}
		
		override public function onNpcClick(e:GameMouseEvent):void {
			//屏蔽地板点击事件
			skipBackgroundClick = true;
			
			if ((e.item as NpcView).state==NpcView.STATE_TASK) 
			{
				//如有任务,就显示任务
				var tmp:TaskInfoView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_TASKINFO, ModuleDict.MODULE_TASKINFO_CLASS,false,
					AlginType.CENTER, 0, 0, DisplayManager.uiSprite.mouseX, DisplayManager.uiSprite.mouseY) as TaskInfoView;
				tmp.setData((e.item as NpcView).tasks);
				
			}else if ((e.item as NpcView).state == NpcView.STATE_SHOP) {
				DisplayManager.uiSprite.addModule(ModuleDict.MODULE_ITEMSHOP, ModuleDict.MODULE_ITEMSHOP_CLASS,false) as TaskInfoView;
			}else if((e.item as NpcView).state==NpcView.STATE_CHAT){
				//如无任务就显示对话
				var tmpchat:NpcChatsView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_NPC_CHATS, ModuleDict.MODULE_NPC_CHATS_CLASS) as NpcChatsView;
				tmpchat.setData((e.item as NpcView).data as NpcVo);
				DisplayManager.uiSprite.setBg(tmpchat);
			}
			
			//屏蔽地板点击事件
			skipBackgroundClick = true;
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
		
	}

}