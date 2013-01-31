package happymagic.display.view.task 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.view.IconView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.SoundEffectManager;
	import happyfish.task.vo.TaskState;
	import happyfish.utils.display.BtnStateControl;
	import happymagic.display.view.ModuleDict;
	import happymagic.events.TaskEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.command.FinishTaskCommand;
	import happymagic.model.vo.ResultVo;
	import happymagic.model.vo.TaskTipsVo;
	import happymagic.model.vo.TaskVo;
	import xrope.LayoutAlign;
	
	/**
	 * ...
	 * @author jj
	 */
	public class TaskInfoView extends UISprite
	{
		private var iview:TaskAwardUI;
		private var conditionsMc:TaskNeedItemListView;
		private var awardsMc:TaskNeedItemListView;
		private var datas:Array;
		private var currentIndex:uint;
		private var npcFace:IconView;
		private var taskexplain:explainUI;
		private var tasktipsvo:TaskTipsVo;
		public function TaskInfoView() 
		{
			super();
			_view = new TaskAwardUI();
			
			conditionsMc = new TaskNeedItemListView(new taskviewlist(), _view,3,true);
			conditionsMc.x = -2;
			conditionsMc.y = -15;
			conditionsMc.init(184, 110, 60, 110, 0, -55,LayoutAlign.LEFT);
			
			awardsMc = new TaskNeedItemListView(new taskviewlist(), _view);
			awardsMc.pageLength = 5;
			awardsMc.x = -147;
			awardsMc.y = 75;
			awardsMc.init(340, 110, 60, 110, -10, -55,LayoutAlign.LEFT);
			iview = _view as TaskAwardUI;
			iview.addEventListener(MouseEvent.CLICK, clickFun, true);
			iview.nextBtn.visible=
			iview.prevBtn.visible = false;
			iview.questionButton.addEventListener(MouseEvent.MOUSE_MOVE, mousemove);
			iview.questionButton.addEventListener(MouseEvent.MOUSE_OUT, mouseout);
			taskexplain = new explainUI();
			taskexplain.mouseChildren = false;
			taskexplain.mouseEnabled = false;
		}
		
		private function mouseout(e:MouseEvent):void 
		{
			iview.removeChild(taskexplain);
		}
		
		private function mousemove(e:MouseEvent):void 
		{
			iview.addChild(taskexplain);

			if (tasktipsvo.content)
			{
				taskexplain.taskexplain.text = tasktipsvo.content;
			}
			taskexplain.x = 195;
			taskexplain.y = -180;
		}
		
		public function setData(value:Array):void {
			datas = value;
			
			currentIndex = 0;
			
			initCurrent();
			
			//音效
			SoundEffectManager.getInstance().playSound(new sound_mission());
		}
		
		private function initCurrent():void
		{
			clear();
			
			if (datas.length==0) 
			{
				return;
			}
			
			var data:TaskVo = datas[currentIndex];
			tasktipsvo = DataManager.getInstance().getTaskTips(data.taskType);
			iview.nameTxt.text = data.name;
			iview.conditionTxt.htmlText = LocaleWords.getInstance().conectWords(data.quest_str, data.fc_curNums);
			
			conditionsMc.setData(data.finish_condition);
			awardsMc.setData(data.awards);
			
			npcFace = new IconView(116, 116, new Rectangle( -170, -110, 116, 116));
			npcFace.setData(data.icon_class);
			iview.addChild(npcFace);
			if (canFinish())
			{
				iview.addChild(iview.effect);
				iview.affirm.visible = false;
				iview.getaward.visible = true;
				iview.stars.visible = true;
				iview.effect.visible = true;
			}
			else
			{
				iview.affirm.visible = true;
				iview.getaward.visible = false;
				iview.stars.visible = false;
				iview.effect.visible = false;
			}
		}
		
		private function canFinish():Boolean {
			var data:TaskVo = datas[currentIndex];
			
			return data.state == TaskState.CAN_FINISH;
		}
		
		private function nextTask():void
		{
			if (currentIndex+1<datas.length) 
			{
				currentIndex++;
				initCurrent();
			}
		}
		
		private function prevTask():void {
			if (currentIndex>0) 
			{
				currentIndex--;
				initCurrent();
			}
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case iview.closeBtn:
				closeMe();
				break;
				
				//case iview.nextBtn:
				//nextTask();
				//break;
				//
				//case iview.prevBtn:
				//prevTask();
				//break;
				
				case iview.getaward:
				finishTask(datas[currentIndex].t_id);
				break;
				
				case iview.affirm:
				closeMe(true);
				break;
			}
		}
		
		private function finishTask(t_id:uint):void
		{
			view.mouseChildren = false;
			var command:FinishTaskCommand = new FinishTaskCommand();
			command.addEventListener(Event.COMPLETE, finishTask_complete);
			command.finish(t_id);
		}
		
		private function finishTask_complete(e:Event):void 
		{
			view.mouseChildren = true;
			e.target.removeEventListener(Event.COMPLETE, finishTask_complete);
			
			if ((e.target as BaseDataCommand).data.result.isSuccess) 
			{
				//成功
				
				var completedTask:TaskVo = datas[currentIndex];
				//表现奖励
				var resultView:FinishTaskResultView =
					DisplayManager.uiSprite.addModule(ModuleDict.MODULE_FINISH_TASKRESULT, ModuleDict.MODULE_FINISH_TASKRESULT_CLASS,true) as FinishTaskResultView;
				resultView.setData(completedTask);
				DisplayManager.uiSprite.setBg(resultView);
				
				//清除数据
				DataManager.getInstance().removeTask(completedTask.t_id);
				removeTask(completedTask.t_id);
				
				//清除左侧任务列表内
				var tmpTaskChangeEvent:TaskEvent = new TaskEvent(TaskEvent.TASKS_STATE_CHANGE);
				tmpTaskChangeEvent.finishTasks.push(completedTask as TaskVo);
				EventManager.getInstance().dispatchEvent(tmpTaskChangeEvent);
				
				
				
				//通知npc清除这个任务
				
				closeMe(true);
				
				//刷新info内显示内容
				//initCurrent();
			}
		}
		
		private function removeTask(t_id:uint):void {
			for (var i:int = 0; i < datas.length; i++) 
			{
				if (datas[i].t_id==t_id) 
				{
					datas.splice(i, 1);
				}
			}
		}
		
		
		
		private function clear():void {
			
			if (iview) 
			{
				iview.nextBtn.visible=
				iview.prevBtn.visible = false;
			}
			
			
			if (conditionsMc) 
			{
				conditionsMc.clear();
				
			}
			
			if (npcFace) 
			{
				iview.removeChild(npcFace);
				npcFace = null;
			}
			
			if (awardsMc) 
			{
				awardsMc.clear();
			}
			
			
		}
		
		override public function closeMe(del:Boolean=true):void {
			//隐藏自己
			DisplayManager.uiSprite.closeModule(name,del);
		}
	}

}