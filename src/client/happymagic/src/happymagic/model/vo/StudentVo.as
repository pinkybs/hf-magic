package happymagic.model.vo 
{
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author Beck
	 */
	public class StudentVo
	{
		public var avatar_id:int;
		public var sid:uint;
		public var decor_id:int;
		public var state:int;
		public var time:int;
		public var magic_id:int;
		public var event_time:int;
		public var stone_time:int;
		//是否可偷 
		public var can_steal:int=1;
		public var class_name:String;
		public var x:int;
		public var y:int;
		public var z:int;
		public var coin:int;
		public function StudentVo() 
		{
			
		}
		
		public function setValue(obj:Object):StudentVo {
			for (var name:String in obj) 
			{
				if (name=="sid") 
				{
					sid = obj[name];
					
					//TODO 临时应付数据为空的情况
					if (!sid) 
					{
						sid = 5;
					}
					
					
					avatar_id = DataManager.getInstance().getStudentClass(sid).avatar_id;
				}else if (name == "avatar_id") {
					//TODO 临时保留的，以后不会有这个变量
					avatar_id = obj[name];
					class_name = DataManager.getInstance().getAvatarVo(avatar_id).className;
				}else if (name == "can_steal") {
					this[name] = obj[name];
				}else {
					this[name] = obj[name];
				}
			}
			var studentState:StudentStateVo = DataManager.getInstance().getStudentState(sid);
			if (studentState.level>1) 
			{
				class_name = DataManager.getInstance().getAvatarVo(avatar_id).className;
				//class_name = DataManager.getInstance().getAvatarVo(avatar_id).className+studentState.level.toString();
			}else {
				class_name = DataManager.getInstance().getAvatarVo(avatar_id).className;
			}
			
			
			return this;
		}
	}

}