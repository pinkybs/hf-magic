package happymagic.model.command 
{
	import com.adobe.serialization.json.JSON;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.geom.Point;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.utils.SysTracer;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.events.DataManagerEvent;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.manager.DataManager;
	import happymagic.model.MagicUrlLoader;
	import happymagic.model.vo.DecorClassVo;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.MagicClassVo;
	import happymagic.model.vo.ResultVo;
	import happymagic.model.vo.StudentVo;
	import happymagic.scene.world.grid.item.Door;
	import happymagic.scene.world.grid.item.Desk;
	import happymagic.scene.world.grid.person.Student;
	/**
	 * ...
	 * @author Beck
	 */
	public class StudyMagicCommand extends BaseDataCommand
	{
		public var decor_id:int;
		public var student:Student;
		public var magic_id:int;
		
		public function StudyMagicCommand():void {
			
		}
		
		public function load($student:Student, $magic_id:int):void 
		{
			SysTracer.systrace("studyMagiccommand", $student.data.sid);
			
			createLoad();
			
			student = $student;
			decor_id = this.student.data.decor_id;
			createRequest(InterfaceURLManager.getInstance().getUrl('studymagic'), {student_id:$student.data.sid,decor_id:student.data.decor_id,magic_id:$magic_id } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			if (data.changeStudent) 
			{
				DataManager.getInstance().setStudentVo(data.changeStudent);
			}
			
			commandComplete();
		}
		
	}

}