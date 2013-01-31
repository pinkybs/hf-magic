package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author jj
	 */
	public class TaskClassVo extends BasicVo
	{
		private var _t_id:uint;
		public var index:int;
		public var type:int;
		//任务分类id
		public var taskType:uint;
		public var name:String;
		public var content:String;
		public var icon_class:String;
		public var quest_str:String;
		public var sceneId:uint;
		public var npcId:uint;
		public var finishSceneId:uint;
		public var finishNpcId:uint;
		private var _finish_condition:Array;
		public var awards:Array;
		
		public function TaskClassVo() 
		{
			
		}
		
		override public function setData(obj:Object):BasicVo 
		{
			super.setData(obj);
			var i:int;
			finish_condition = new Array();
			for (i = 0; i < obj.finish_condition.length; i++) 
			{
				finish_condition.push(new ConditionVo().setData(obj.finish_condition[i]));
			}
			
			awards = new Array();
			for (i = 0; i < obj.awards.length; i++) 
			{
				awards.push(new ConditionVo().setData(obj.awards[i]));
			}
			
			return this;
		}
		
		public function get t_id():uint 
		{
			return _t_id;
		}
		
		public function set t_id(value:uint):void 
		{
			_t_id = value;
		}
		
		public function get finish_condition():Array 
		{
			return _finish_condition;
		}
		
		public function set finish_condition(value:Array):void 
		{
			_finish_condition = value;
		}
		
		
	}

}