package happymagic.control.command 
{
	import happymagic.manager.DataManager;
	import happymagic.model.vo.StudentVo;
	import happymagic.scene.world.grid.person.Student;
	import happymagic.scene.world.MagicWorld;
	/**
	 * 修改或增加学生数据,并更新表现
	 * @author ...
	 */
	public class ChangeStudentCommand 
	{
		
		public function ChangeStudentCommand(data:StudentVo) 
		{
			var world:MagicWorld = DataManager.getInstance().worldState.world as MagicWorld;
			var tmpstudent:Student = world.getStudentBySid(data.sid);
			if (tmpstudent) 
			{
				//已有学生,修改数据和表现
				DataManager.getInstance().setStudentVo(data);
				tmpstudent.resetData(data);
				//重设计时器
				tmpstudent.countDown();
				//重设状态表现
				//tmpstudent.initStateDisplay();
			}else {
				//还没有,是新增学生,创建
				
			}
			
		}
		
	}

}