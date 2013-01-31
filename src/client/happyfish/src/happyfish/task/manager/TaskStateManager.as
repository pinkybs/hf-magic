package happyfish.task.manager 
{
	import flash.events.EventDispatcher;
	import happyfish.task.events.TaskStateEvent;
	import happyfish.task.IConditionVo;
	import happyfish.task.vo.ITaskVo;
	import happyfish.task.vo.TaskState;
	import happymagic.model.vo.ConditionType;
	/**
	 * ...
	 * @author slamjj
	 */
	public class TaskStateManager 
	{
		protected var _eventDispatcher:EventDispatcher;
		
		public function TaskStateManager() 
		{
			
		}
		
		protected function initManager(__eventDispatcher:EventDispatcher):void {
			_eventDispatcher = __eventDispatcher;
			
			_eventDispatcher.addEventListener(TaskStateEvent.NEED_CHECK_STATE, needCheckState);
		}
		
		private function needCheckState(e:TaskStateEvent):void 
		{
			checkAllTask();
		}
		
		protected function get taskList():Vector.<ITaskVo> {
			//需继承
			throw("需继承");
			return null;
		}
		
		protected function checkAllTask():void {
			var tasks:Array = new Array();
			for (var i:int = 0; i < taskList.length; i++) 
			{
				var item:ITaskVo = taskList[i];
				if (item.state==TaskState.ACTIVED) 
				{
					if (checkTask(item)) {
						tasks.push(item);
					}
				}
			}
			
			if(tasks.length>0) dispatchChangeEvent(tasks);
		}
		
		protected function dispatchChangeEvent(tasks:Array):void {
			throw("需要继承实现");
		}
		
		/**
		 * 
		 * @param	task
		 * @return	任务状态是否改变
		 */
		protected function checkTask(task:ITaskVo):Boolean {
			//没有条件的任务直接跳过
			if (task.finish_condition.length==0) 
			{
				return false;
			}
			
			var stateChanged:Boolean = false;
			var numChanged:Boolean = false;
			var curNum:uint;
			//起始任务状态为2=已完成,可领奖
			var state:uint = TaskState.CAN_FINISH;
			
			for (var i:int = 0; i < task.finish_condition.length; i++) 
			{
				var item:IConditionVo = task.finish_condition[i];
				
				//如果条件中有不是道具的类型,就跳过这条任务,不做判断了
				if (item.type!=ConditionType.ITEM) 
				{
					return false;
				}
				
				curNum = getConditionCurNum(item);
				if (curNum != item.currentNum) {
					numChanged = true;
					item.currentNum = curNum;
					task.fc_curNums[i] = curNum;
				}
				
				if (item.currentNum<item.num) 
				{
					state = TaskState.ACTIVED;
				}
			}
			if (task.state != state) {
				//任务状态变化了
				stateChanged = true;
			}
			
			task.state = state;
			return stateChanged || numChanged;
			
		}
		
		protected function getConditionCurNum(condition:IConditionVo):uint {
			throw("需要扩展");
		}
		
		protected function getTaskByTid(t_id:uint):ITaskVo {
			for (var i:int = 0; i < taskList.length; i++) 
			{
				var item:ITaskVo = taskList[i];
				if (item.t_id==t_id) 
				{
					return item;
				}
			}
			return null;
		}
		
	}

}