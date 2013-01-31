package happymagic.display.view.magicClass 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import flash.utils.setTimeout;
	import happyfish.display.view.IconView;
	import happyfish.display.view.ItemRender;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.ModuleManager;
	import happyfish.utils.DateTools;
	import happyfish.utils.display.FiltersDomain;
	import happyfish.utils.display.McShower;
	import happymagic.display.control.MagicEnoughCheckCommand;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.events.ActionStepEvent;
	import happymagic.events.MagicBookEvent;
	import happymagic.manager.DataManager;
	import happymagic.model.command.StudyMagicCommand;
	import happymagic.model.command.TestCommand;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.MagicClassVo;
	import happymagic.model.vo.StudentStateType;
	import happymagic.model.vo.StudentVo;
	import happymagic.scene.world.control.MouseStudentAction;
	import happymagic.scene.world.control.MouseTransAction;
	import happymagic.scene.world.grid.person.Student;
	/**
	 * ...
	 * @author ZC
	 */
	public class TeachMagicView extends UISprite
	{
		private var _iview:MagicClassUi; 
		private var _student:Student;
		private var _data:MagicClassVo;
		private var icon:IconView;
		public function TeachMagicView() 
		{
			super();
			this._view = new MagicClassUi;
			this._iview = this._view as MagicClassUi;
			this._iview.addEventListener(MouseEvent.CLICK, clickfun);
			
		}
		
		//设置数据
		public function setData(sdt:Student):void 
		{
            this._iview.mouseChildren = true;
		    this._iview.mouseEnabled = true;	
			
			if (icon)
			{
				_iview.removeChild(icon);
			}
			icon = new IconView(45, 40, new Rectangle(-31,-94,65,72));
			_iview.addChild(icon);
			_student = sdt;
			_data = DataManager.getInstance().getMagicClass((sdt.data as StudentVo).magic_id);
		    icon.setData(_data.class_name);

			this._iview.timeTxt.text = DateTools.getLostTime(_data.time*1000);
			this._iview.SPTxt.text = String(_data.mp);
			this._iview.expTxt.text = String(_data.exp);
		    this._iview.title.text = _data.name;
			this._iview.moneyTxt.text = String(_data.coin);

		}
		
		private function StudyFlow():void
		{
		   if (_student.data.state != StudentStateType.NOTEACH) 
			{
				//飘字
				EventManager.getInstance().showPiaoStr(PiaoMsgType.TYPE_BAD_STRING, LocaleWords.getInstance().getWord("IamBusy"));
				return;
			}
				//判断魔法值是否足够
				if (!new MagicEnoughCheckCommand().check(_data.mp)) 
				{
					//飘消息提示魔法不足
					EventManager.getInstance().showPiaoStr(PiaoMsgType.TYPE_BAD_STRING, LocaleWords.getInstance().getWord("notEnoughMp"));

					return;
				}
				_student.loadingState = false;
				requestTeach();	
		}
		
		private function requestTeach():void 
		{
			//请求后端服务器
				var magic_command:StudyMagicCommand = new StudyMagicCommand();
				magic_command.load(_student, _data.magic_id);
				
				magic_command.addEventListener(Event.COMPLETE, studentShowEvent);
		}
		
		private function studentShowEvent(e:Event):void 
		{
			
			setTimeout(studentShow, 300, e);
		}
		
		private function studentShow(event:Event):void
		{
			_student.loadingState = true;
	     	if (event.target.data.result.isSuccess) 
			{
				//音效
				//SoundEffectManager.getInstance().playSound(new playmagic());
				
				//播放学习魔法特效
				var teachMvShower:McShower = new McShower(eventTakeMv, _student.view.container);
				//teachMvShower.y = -20;
				//teachMvShower.changeRate(15,5,16);
				//_student.view.container.stage.frameRate = 5;
				
				//成功后执行动画,刷新学生数据
				
				_student.resetData(DataManager.getInstance().getStudentVoByDecorId(_student.data.decor_id));
				_student.countDown();
				
				_student.desk.playAnimation('desk_play');
				
				_student.desk.magicMovie(DataManager.getInstance().getMagicClass(_student.data.magic_id).actMovie);
				
				_student.playAnimation('magic');
				
				_student.removeBubble();
				
				//后续时间引导事件
				EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_TEACHMAGIC));
				
				//新手引导事件
				//EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_STUDENT_TEACHCLICK));
				closeMe(true);
			}else {
                this._iview.mouseChildren = true;
				this._iview.mouseEnabled = true;				
			}
		}
		
		private function clickfun(e:MouseEvent):void
		{
			switch(e.target)
			{
				//关闭按钮---------------------------
				case _iview.close:
				closeMe(true);
				break;
				
				//教魔法按钮-------------------------
				case _iview.teachmagic:
				
                this._iview.mouseChildren = false;
				this._iview.mouseEnabled = false;
			    //如果mp不足,就飘屏
			    if (!new MagicEnoughCheckCommand().check(_data.mp))
			    {
				   EventManager.getInstance().showPiaoStr(PiaoMsgType.TYPE_BAD_STRING, LocaleWords.getInstance().getWord("magicUnenough"));
				   
                   this._iview.mouseChildren = true;
				   this._iview.mouseEnabled = true;
				   
				   closeMe(true);
				   return;
			    }
			    StudyFlow();		
				break;

				//研究魔法按钮-----------------------
				case _iview.learn:
				EventManager.getInstance().dispatchEvent(new MagicBookEvent(MagicBookEvent.SHOW_MAGICBOOK));
				closeMe(true);
				break;
				
			}
		}
		
	}

}