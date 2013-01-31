package happymagic.model.vo 
{
	import com.adobe.serialization.json.JSON;
	import com.brokenfunction.json.decodeJson;
	import com.brokenfunction.json.encodeJson;
	import happyfish.task.vo.ITaskVo;
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author jj
	 */
	public class TaskVo extends TaskClassVo implements ITaskVo
	{
		private var _state:uint;
		//条件的当前值
		private var _fc_curNums:Array;
		public function TaskVo() 
		{
			
		}
		
		public function setValue(obj:Object):TaskVo {
			for (var name:String in obj) 
			{
				if ( this.hasOwnProperty(name)) 
				{
					if (name=="state") 
					{
						state = uint(obj[name]) + 1;
					}else {
						this[name] = obj[name];
					}
				}
			}
			var i:int;
			var taskClass:TaskClassVo = DataManager.getInstance().getTaskClass(t_id);
			if (taskClass) 
			{
				var tmpobj:Object = decodeJson(JSON.encode(taskClass));
				for (var name2:String in tmpobj) 
				{
					this[name2] = tmpobj[name2];
				}
				
				finish_condition = new Array();
				for (i = 0; i < tmpobj.finish_condition.length; i++) 
				{
					finish_condition.push(new ConditionVo().setData(tmpobj.finish_condition[i]));
				}
				
				awards = new Array();
				for (i = 0; i < tmpobj.awards.length; i++) 
				{
					awards.push(new ConditionVo().setData(tmpobj.awards[i]));
				}
			}
			
			var tmp:ConditionVo;
			if (finish_condition) 
			{
				for (i = 0; i < finish_condition.length; i++) 
				{
					tmp = finish_condition[i];
					tmp.currentNum = fc_curNums[i];
				}
			}
			
			
			return this;
		}
		
		public function get fc_curNums():Array 
		{
			return _fc_curNums;
		}
		
		public function set fc_curNums(value:Array):void 
		{
			_fc_curNums = value;
		}
		
		public function get state():uint 
		{
			return _state;
		}
		
		public function set state(value:uint):void 
		{
			_state = value;
		}
		
	}

}