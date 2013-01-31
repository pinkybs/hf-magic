package happymagic.display.view.student 
{
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.Tooltips;
	import happyfish.display.view.IconView;
	import happyfish.display.view.PerBarView;
	import happyfish.manager.EventManager;
	import happyfish.utils.display.FiltersDomain;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.student.event.StudentListViewEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.StudentAwardCommand;
	import happymagic.model.vo.StudentStateVo;
	import happymagic.scene.world.grid.person.Student;
	import happymagic.scene.world.MagicWorld;
	/**
	 * ...
	 * @author zc
	 */
	public class StudentListRootView extends GridItem
	{
		private var iview:studentUI;
		private var _data:StudentStateVo;
		private var expBar:PerBarView;
		private var movieclip:studentTips;
		public function StudentListRootView(uiview:MovieClip) 
		{
			super(uiview);
			
			view.mouseChildren = true;
			
			iview = uiview as studentUI;
			iview.addEventListener(MouseEvent.CLICK, clickFun, true);
			iview.addEventListener(MouseEvent.MOUSE_OVER, clickOver, true);
			iview.addEventListener(MouseEvent.MOUSE_OUT, clickOut, true);
			
			movieclip = new studentTips();
			movieclip.mouseEnabled = false;
			movieclip.mouseChildren = false;
		}
		override public function setData(value:Object):void 
		{
			_data = value as StudentStateVo;
			var icon:IconView = new IconView(68, 90, new Rectangle(13,6,68,91));
			iview.addChildAt(icon,iview.getChildIndex(iview.lock));
			icon.setData(_data.className);		
			
			//判断解锁否：
            if (_data.unLock)
			{
				iview.lock.visible = false;
				iview.locktxt.visible = false;
				iview.locktxt1.visible = false;
				iview.lockleveltxt.visible = false;
			}
			else
			{
				iview.lockleveltxt.text =  String(_data.unLockMp);
              	iview.LVEXP.visible = false;
				iview.expX.visible = false;
				iview.exptback.visible = false;
				iview.magicBar.visible = false;			
				icon.filters = [FiltersDomain._contrastFilter];
			}
			
			//判断是否有领奖
			if (_data.needAward)
			{
				iview.expX.visible = false;
				iview.magicBar.visible = false;
				iview.exptback.visible = false;
			}
			else
			{
				iview.lingjiang.visible = false;
				iview.di.visible = false;
				
				expBar = new PerBarView(iview.magicBar, iview.magicBar.width);			
				expBar.minW = 0;
				expBar.maxValue = _data.maxpExp;
				expBar.setData(_data.exp);
			}
		
			iview.LVEXP.text = "LV : " + _data.level;
			iview.expX.text =  _data.exp + "/" + _data.maxpExp;
			iview.studentname.text = _data.name;
			
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch(e.target)
			{
				case iview.lingjiang:
				
				getAward();
				
				break;
				
			}
		}
		
		private function clickOver(e:MouseEvent):void 
		{
			  DisplayManager.uiSprite.getModule(ModuleDict.MODULE_STUDENTLISTINFO).view.addChild(movieclip);
			  movieclip.x = iview.x -135;
			  movieclip.y = iview.y -130;
			  movieclip.studentname.text = _data.name;
			  if (_data.content)
			  {
				movieclip.studentContent.text = _data.content;  
			  }
			  
		}
		
		private function clickOut(e:MouseEvent):void 
		{
			  DisplayManager.uiSprite.getModule(ModuleDict.MODULE_STUDENTLISTINFO).view.removeChild(movieclip);
		}
		
		private function getAward():void 
		{
			iview.mouseChildren = false;
			var command:StudentAwardCommand = new StudentAwardCommand();
			command.addEventListener(Event.COMPLETE, getAward_complete);
			command.change(_data.sid);
		}
		
		private function getAward_complete(e:Event):void 
		{
			iview.mouseChildren = true;
			e.target.removeEventListener(Event.COMPLETE, getAward_complete);
			
			if (e.target.data.result.isSuccess) 
			{
				_data.needAward = 0;
				setData(_data);
				iview.di.visible = false;
				
				var tmpstudent:Student = (DataManager.getInstance().worldState.world as MagicWorld).getStudent(_data.sid);
				if (tmpstudent)
				{
				  tmpstudent.awardComplete();					
				}

			   EventManager.getInstance().dispatchEvent(new StudentListViewEvent(StudentListViewEvent.STUDENTAWARD));				
			}
		}
		
	}

}