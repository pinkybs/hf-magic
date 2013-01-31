package happymagic.display.view.task 
{
	import adobe.utils.CustomActions;
	import com.greensock.TweenLite;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import happyfish.display.ui.defaultList.DefaultVLineListView;
	import happyfish.display.ui.events.GridPageEvent;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.AlginType;
	import happyfish.task.vo.TaskState;
	import happymagic.display.view.ModuleDict;
	import happymagic.events.SceneEvent;
	import happymagic.events.TaskEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.TaskVo;
	import xrope.VLineLayout;
	
	/**
	 * ...
	 * @author jj
	 */
	public class TaskListView extends UISprite
	{
		private var layouter:DefaultVLineListView;
		private var data:Array;
		private var iview:taskListUi;
		//面板是否收起	 1：打开 2：收起
		private var viewState:uint;
		
		public var currentPage:uint;
		public var pageLength:uint;
		
		public function TaskListView() 
		{
			super();
			
			viewState = 1;
			
			_view = new taskListUi();
			
			iview = _view as taskListUi;
			
			layouter = new DefaultVLineListView(new MovieClip(), _view, 4, false);
			layouter.tweenTime = 0;
			layouter.selectCallBack = selectFun;
			layouter.x = 45;
			layouter.y = -139;
			layouter.init(60, 240, 60, 50, -26, 30);
			layouter.setGridItem(TaskListItemView, MovieClip);
			
			EventManager.getInstance().addEventListener(TaskEvent.TASKS_STATE_CHANGE, taskChange);
			EventManager.getInstance().addEventListener(SceneEvent.SCENE_DATA_COMPLETE, sceneChanged);
			
			iview.addEventListener(MouseEvent.CLICK, clickFun, true);
			
			setData();
		}
		
		/**
		 * 场景更换事件,刷新任务列表
		 * @param	e
		 */
		private function sceneChanged(e:SceneEvent):void 
		{
			setData();
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target.name) 
			{
				case "closeState":
					closeView();
				break;
				
				case "openState":
					openView();
				break;
			}
		}
		
		public function closeView():void 
		{
			//viewState = 2;
			//iview.closeStateBtn.gotoAndStop(2);
			//iview.mouseChildren = false;
			//TweenLite.to ( iview, .5, { x:-80,onComplete:closeView_complete });
		}
		
		private function closeView_complete():void 
		{
			iview.mouseChildren = true;	
		}
		
		public function openView():void 
		{
			//viewState = 1;
			//iview.closeStateBtn.gotoAndStop(1);
			//iview.mouseChildren = false;
			//TweenLite.to(iview, .5, { x:10,onComplete:openView_complete });
		}
		
		private function openView_complete():void 
		{
			iview.mouseChildren = true;
		}
		
		private function taskChange(e:TaskEvent):void 
		{
			var i:int;
			for (i = 0; i < e.changeTasks.length; i++) 
			{
				changeTask(e.changeTasks[i]);
			}
			
			for (i = 0; i < e.addTasks.length; i++) 
			{
				addTask(e.addTasks[i]);
			}
			
			for (i = 0; i < e.finishTasks.length; i++) 
			{
				finishTask(e.finishTasks[i]);
			}
			
			data.sortOn("state", Array.NUMERIC | Array.DESCENDING);
			
			initPage();
		}
		
		private function finishTask(value:TaskVo):void
		{
			for (var i:int = 0; i < data.length; i++) 
			{
				if (data[i].t_id==value.t_id) 
				{
					data.splice(i, 1);
					
					return;
				}
			}
		}
		
		private function addTask(value:TaskVo):void
		{
			if (value.sceneId==DataManager.getInstance().currentUser.currentSceneId || value.sceneId==0) 
			{
				//data.push(value);
				layouter.addItem(value);
			}
		}
		
		
		
		private function changeTask(taskVo:TaskVo):void {
			var i:int;
			for (i = 0; i < data.length; i++) 
			{
				if (data[i].t_id==taskVo.t_id) 
				{
					data[i].state = taskVo.state;
				}
			}
			
			var tmp:TaskListItemView;
			for (i = 0; i < view.numChildren; i++) 
			{
				tmp = view.getChildAt(i) as TaskListItemView;
				if (tmp) 
				{
					if (tmp.data.sceneId==taskVo.sceneId) 
					{
						if (taskVo.state==TaskState.CAN_FINISH) 
						{
							(view.getChildAt(i) as TaskListItemView).finishMv();
						}else (taskVo.state == TaskState.ACTIVED)
						{
							(view.getChildAt(i) as TaskListItemView).backToActived();
						}
					}
				}
				
			}
		}
		
		public function setData():void {
			
			
			data = DataManager.getInstance().getTasksBySceneId(DataManager.getInstance().curSceneUser.currentSceneId);
			data.sortOn("state", Array.NUMERIC | Array.DESCENDING);
			layouter.setData(data);
		}
		
		private function initPage():void {
			
			layouter.setData(data, "", null, true);
		}
		
		private function selectFun(e:GridPageEvent):void 
		{
			var item:TaskListItemView = e.item as TaskListItemView;
			var tmpdata:TaskVo = item.data;
			var tmp:TaskInfoView = 
				DisplayManager.uiSprite.addModule(ModuleDict.MODULE_TASKINFO, ModuleDict.MODULE_TASKINFO_CLASS,false,AlginType.CENTER,0,0,DisplayManager.uiSprite.mouseX,DisplayManager.uiSprite.mouseY) as TaskInfoView;
			tmp.setData([tmpdata]);
		}
		
	}

}