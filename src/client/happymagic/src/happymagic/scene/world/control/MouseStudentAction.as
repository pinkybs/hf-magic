package happymagic.scene.world.control 
{
	import com.friendsofed.isometric.Point3D;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import happyfish.display.view.ItemRender;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.mouse.MouseManager;
	import happyfish.manager.SoundEffectManager;
	import happyfish.scene.world.WorldState;
	import happyfish.utils.display.McShower;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.events.ActionStepEvent;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.PublicDomain;
	import happymagic.model.command.StudyMagicCommand;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.MagicClassVo;
	import happymagic.model.vo.StudentStateType;
	import happymagic.scene.world.grid.item.Desk;
	import happymagic.scene.world.grid.person.Student;
	
	/**
	 * 学生魔法学习时的逻辑
	 * @author Beck
	 */
	public class MouseStudentAction extends MouseMagicAction
	{
		protected var itemRender:ItemRender;
		private var student:Student;
		private var remove_flg:Boolean = false;
		public function MouseStudentAction($state:WorldState, $item_render:ItemRender, $stack_flg:Boolean = false) 
		{
			super($state, $stack_flg);
			this.itemRender = $item_render;
		}
		
		public function setMagic(mIcon:Sprite):void {
			MouseManager.getInstance().setLiuChenIcon(mIcon,liuchenComplete);
		}
		
		private function liuchenComplete():void
		{
			remove_flg = true;
			
		}
		
		override public function onStudentOver(event:GameMouseEvent):void 
		{
			event.item.showGlow();
		}
		
		override public function onStudentOut(event:GameMouseEvent):void 
		{
			event.item.hideGlow();
			remove();
		}
		
		/**
		 * 点击学生
		 * @param	event
		 */
        override public function onStudentClick(event:GameMouseEvent) : void
        {
			student = event.item as Student;
			
			if (Student(event.item).data.state != StudentStateType.NOTEACH) 
			{
				//飘字
				EventManager.getInstance().showPiaoStr(PiaoMsgType.TYPE_BAD_STRING, LocaleWords.getInstance().getWord("IamBusy"));
				return;
			}
			
			//判断魔法是否符合需求
			if (student.data.magic_id==DataManager.getInstance().getVar("selectedMagicClass").magic_id) 
			{
				var magic:MagicClassVo = DataManager.getInstance().getMagicClass(student.data.magic_id);
				//判断魔法值是否足够
				if (!new MagicEnoughCheckCommand(_data.mp)) 
				{
					//飘消息提示魔法不足
					//EventManager.getInstance().showPiaoStr(PiaoMsgType.TYPE_BAD_STRING, LocaleWords.getInstance().getWord("notEnoughMp"));
					return;
				}
				var p3d:Point3D = student.desk.getMaigcSpace();
				var fiddleTowards:Point3D = student.desk.getWalkableSpace();
				
				//给学生增加行为,移动到课桌边上,行为完成时调用课桌的收取水晶方法
				state.world.player.addCommand(new AvatarCommand(p3d, null, fiddleTowards, 1500, 'magic', requestTeach));
				
				this.remove_flg = true;
			}else {
				//飘字
				EventManager.getInstance().showPiaoStr(PiaoMsgType.TYPE_BAD_STRING, LocaleWords.getInstance().getWord("magicClassError"));
			}
			
			//this.remove_flg = true;
			//remove();
        }
		
		private function requestTeach():void {
			//请求后端服务器
				var magic_command:StudyMagicCommand = new StudyMagicCommand();
				magic_command.load(student, this.itemRender.data.magic_id);
				
				magic_command.addEventListener(Event.COMPLETE, studentShow);
		}
		
		private function studentShow(event:Event):void
		{
			
			if (event.target.data.result.isSuccess) 
			{
				//音效
				//SoundEffectManager.getInstance().playSound(new playmagic());
				
				//播放学习魔法特效
				var teachMvShower:McShower = new McShower(teachMv, student.view.container);
				teachMvShower.y = -20;
				
				
				//成功后执行动画,刷新学生数据
				student.resetData(DataManager.getInstance().getStudentVoById(student.data.decor_id));
				student.countDown();
				
				student.desk.playAnimation('desk_play');
				
				student.desk.magicMovie(DataManager.getInstance().getMagicClass(student.data.magic_id).actMovie);
				
				student.playAnimation('magic');
				
				if (student.data.event_time != -1) {
					student.countDown();
				}
				
				student.removeBubble();
				
				//引导事件
				//EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_STUDENT_TEACHCLICK));
				
			}
			
			//主角执行下一步行为
			//state.world.player.shiftCommand();
			
		}
		
		
		override public function remove($stack_flg:Boolean = true):void
		{
			if (remove_flg) {
				super.remove();
			}
		}
		
	}

}