package happymagic.model.vo 
{
	import happyfish.manager.EventManager;
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author slamjj
	 */
	public class StudentStateVo extends StudentClassVo 
	{
		public var exp:uint;
		public var maxpExp:uint;
		public var level:uint;
		public var unLock:uint;
		//可以领奖,需要领奖了,点击后优先做领奖请求
		public var needAward:uint;
		
		public function StudentStateVo() 
		{
			
		}
		
		public function setValue(value:Object):StudentStateVo {
			for (var name:String in value) 
			{
				if ( this.hasOwnProperty(name)) 
				{
					if (name=="sid") 
					{
						sid = value[name];
						var studentClass:StudentClassVo = DataManager.getInstance().getStudentClass(value[name]);
						avatar_id = studentClass.avatar_id;
						if (value.level>9) 
						{
							//className = studentClass.className;
							className = studentClass.className+(Math.floor(value.level/10)).toString();
						}else {
							className = studentClass.className;
						}
						
						name = studentClass.name;
						unLockMp = studentClass.unLockMp;
						content = studentClass.content;
					}else {
						this[name] = value[name];
					}
					
				}
			}
			
			if (level) {
				var tmplevel:StudentLevelClassVo = DataManager.getInstance().getStudentLevelClass(level);
				maxpExp = tmplevel.exp;
			}
			
			
			return this;
		}
		
		public function changeValue(obj:Object):void 
		{
			exp += Number(obj.exp);
			level += obj.level;
			needAward = obj.needAward;
			
			if (obj.level) 
			{
				var tmplevel:StudentLevelClassVo = DataManager.getInstance().getStudentLevelClass(level);
				maxpExp = tmplevel.exp;
				
				exp = 0;
				
				var studentClass:StudentClassVo = DataManager.getInstance().getStudentClass(sid);
				if (level>1) 
				{
					className = studentClass.className;
					//className = studentClass.className+level.toString();
				}else {
					className = studentClass.className;
				}
				
				var tmpclass:StudentVo = DataManager.getInstance().getStudentVoBySid(sid);
				if (tmpclass) 
				{
					tmpclass.class_name = className;
				}
				
			}
		}
		
	}

}