package happymagic.task.manager 
{
	import happyfish.manager.EventManager;
	import happyfish.task.IConditionVo;
	import happyfish.task.manager.TaskStateManager;
	import happyfish.task.vo.ITaskVo;
	import happymagic.events.TaskEvent;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.ConditionType;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class MagicTaskStateManager extends TaskStateManager 
	{
		
		public function MagicTaskStateManager() 
		{
			super();
			initManager(EventManager.getInstance());
		}
		
		override protected function get taskList():Vector.<ITaskVo> 
		{
			return DataManager.getInstance().tasks;
		}
		
		override protected function dispatchChangeEvent(tasks:Array):void 
		{
			var tmpTaskChangeEvent:TaskEvent = new TaskEvent(TaskEvent.TASKS_STATE_CHANGE);
			tmpTaskChangeEvent.changeTasks = tasks;
			_eventDispatcher.dispatchEvent(tmpTaskChangeEvent);
		}
		
		override protected function getConditionCurNum(condition:IConditionVo):uint 
		{
			var num:uint=0;
			
			switch (condition.type) 
			{
				case ConditionType.ITEM:
					num = DataManager.getInstance().getItemNum(uint(condition.id));
				break;
				
				case ConditionType.DECOR:
					num = DataManager.getInstance().getDecorNum(uint(condition.id));
				break;
				
				case ConditionType.USER:
					switch (condition.id) 
					{
						case ConditionType.USER_COIN:
							num = DataManager.getInstance().currentUser.coin;
						break;
						
						case ConditionType.USER_GEM:
							num = DataManager.getInstance().currentUser.gem;
						break;
						
						case ConditionType.USER_EXP:
							num = DataManager.getInstance().currentUser.exp; 
						break;
					}
				break;
			}
			
			return num;
		}
		
	}

}