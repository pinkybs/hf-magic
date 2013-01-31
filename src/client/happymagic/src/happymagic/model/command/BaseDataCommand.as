package happymagic.model.command 
{
	import adobe.utils.CustomActions;
	import com.brokenfunction.json.decodeJson;
	import com.friendsofed.isometric.IsoUtils;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.geom.Point;
	import flash.net.URLRequest;
	import flash.net.URLVariables;
	import flash.utils.setTimeout;
	import happyfish.manager.EventManager;
	import happyfish.model.DataCommandBase;
	import happyfish.model.UrlConnecter;
	import happyfish.scene.astar.Grid;
	import happyfish.scene.astar.Node;
	import happyfish.scene.camera.CameraControl;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.utils.display.McShower;
	import happyfish.utils.SysTracer;
	import happymagic.control.command.ChangeUserDataCommand;
	import happymagic.display.control.StoryPlayCommand;
	import happymagic.display.view.itembox.events.ItemBoxEvent;
	import happymagic.events.DataManagerEvent;
	import happymagic.events.DiaryEvent;
	import happymagic.events.FriendsEvent;
	import happymagic.events.SceneEvent;
	import happymagic.events.SysMsgEvent;
	import happymagic.events.TaskEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.control.TakeResultVoControl;
	import happymagic.model.MagicUrlLoader;
	import happymagic.model.vo.DecorClassVo;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.DiaryVo;
	import happymagic.model.vo.EnemyVo;
	import happymagic.model.vo.ItemVo;
	import happymagic.model.vo.ResultVo;
	import happymagic.model.vo.StoryActionVo;
	import happymagic.model.vo.StoryVo;
	import happymagic.model.vo.StudentClassVo;
	import happymagic.model.vo.StudentStateType;
	import happymagic.model.vo.StudentStateVo;
	import happymagic.model.vo.StudentVo;
	import happymagic.model.vo.SwitchVo;
	import happymagic.model.vo.TaskVo;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.control.RoomUpgradeMvCommand;
	import happymagic.scene.world.grid.item.Decor;
	import happymagic.scene.world.grid.item.Door;
	import happymagic.scene.world.grid.person.Student;
	import happymagic.scene.world.MagicWorld;
	
	[Event(name = "complete", type = "flash.events.Event")]
	
	/**
	 * ...
	 * @author jj
	 */
	public class BaseDataCommand extends DataCommandBase
	{
		//是否显示错误信息
		public var takeResult:Boolean = true;
		//是否用飘屏显示错误信息
		public var piaoMsg:Boolean = true;
		public var piaoPoint:Point;
		public function BaseDataCommand(_callBack:Function=null) 
		{
			super(_callBack);
		}
		
		override protected function createLoad():void 
		{
			loader = new MagicUrlLoader() as UrlConnecter;
			//loader.retry = true;
			loader.addEventListener(Event.COMPLETE, load_complete);
		}
		
		override protected function load_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, load_complete);
			
			SysTracer.systrace(e.target.data);
			
			var tmdm:DataManager = DataManager.getInstance();
			var decor_vo:DecorVo;
			var user_vo:UserVo;
			var scene_student_vo:StudentVo;
			var tmpstudentState:StudentStateVo;
			
			var item:StudentClassVo;
			var item2:StudentStateVo;
			
			// decodeJson 反编译JS
			objdata = decodeJson(e.target.data);
			data = new Object();
			for (var name:String in objdata) 
			{
				var i:int;
				var j:int;
				switch (name) 
				{
					case "result":
						data.result = new ResultVo().setValue(objdata.result);
						if (takeResult || !data.result.isSuccess)
						{
						    TakeResultVoControl.getInstance().take(data.result, piaoMsg, piaoPoint);							
						}
                        if (objdata.result.roomLevelUp)
						{
							if (objdata.studentStates) 
							{							
								for (i = 0; i < objdata.studentStates.length; i++) 
								{
									for (j = 0; j < tmdm.studentStates.length; j++) 
									{
										item2 = tmdm.studentStates[j];
										if (item2.sid==objdata.studentStates[i].sid) 
										{
											item2.setValue(objdata.studentStates[i]);
											item2.unLock = 1;
											continue;
										}
									}
								}
							}							
						}
						
					break;
					
					case "changeUsers":
						//data.changeUsers = new Array();
						//更新用户数据及形象表现
						for (var k:int = 0; k < objdata.changeUsers.length; k++) 
						{
							new ChangeUserDataCommand(objdata.changeUsers[k]);
						}
					break;
					
					case "refreshScene":
						EventManager.getInstance().showSysMsg("更新中,请稍等");
						setTimeout(refreshScene, 1000, objdata.refreshScene);
						
					break;
					
					case "levelupScene":
						refreshScene(objdata.levelupScene);
						
						//开始表现房间升级时的一套动画表现
						new RoomUpgradeMvCommand();
					break;
					
					case "friends":
						var friends:Array = new Array();
						var tmpfriends:UserVo;
						objdata.friends.sortOn("exp", Array.NUMERIC | Array.DESCENDING);
						for (i= 0; i < objdata.friends.length; i++) 
						{
							tmpfriends = new UserVo().setData(objdata.friends[i]);
							tmpfriends.index = i;
							friends.push(tmpfriends);
						}
						data.friends = friends;
						tmdm.friends = friends;
						EventManager.getInstance().dispatchEvent(new FriendsEvent(FriendsEvent.FRIENDS_DATA_COMPLETE));
					break;
					
					case "addItem":
						data.addItem = new Array();
						for (i = 0; i < objdata.addItem.length; i++) 
						{
							data.addItem.push(new ItemVo().setValue( { i_id:objdata.addItem[i][0], num:objdata.addItem[i][1], id:objdata.addItem[i][2] } ));
						}
						tmdm.addItems(data.addItem);
						//通知道具箱更新显示
						EventManager.getInstance().dispatchEvent(new DataManagerEvent(DataManagerEvent.ITEMS_CHANGE));
					break;
					
					case "removeItems":
						data.removeItems = new Array();
						for (i = 0; i < objdata.removeItems.length; i++) 
						{
							if (objdata.removeItems[i].length>2) 
							{
								data.removeItems.push(new ItemVo().setValue( { i_id:objdata.removeItems[i][0], num:objdata.removeItems[i][1], id:objdata.removeItems[i][2] } ));
							}else {
								data.removeItems.push(new ItemVo().setValue( { i_id:objdata.removeItems[i][0], num:objdata.removeItems[i][1] } ));
							}
							
						}
						tmdm.removeItems(data.removeItems);
						EventManager.getInstance().dispatchEvent(new DataManagerEvent(DataManagerEvent.ITEMS_CHANGE));
					break;
					
					case "addDecor":
						data.addDecor = new Array();
						for (i = 0; i < objdata.addDecor.length; i++) 
						{
							data.addDecor.push(new DecorVo().setValue(objdata.addDecor[i]));
						}
						tmdm.addDecor(data.addDecor);
					break;
					
					case "addDecorBag":
						data.addDecorBag = new Array();
						for (i = 0; i < objdata.addDecorBag.length; i++) 
						{
							data.addDecorBag.push(new DecorVo().setValue( { id:objdata.addDecorBag[i][0],
							num:objdata.addDecorBag[i][1],
							d_id:objdata.addDecorBag[i][2]}
							));
						}
						tmdm.addDecorBag(data.addDecorBag);
						EventManager.getInstance().dispatchEvent(new DataManagerEvent(DataManagerEvent.DECORBAG_CHANGE));
					break;
					
					case "removeDecorBag":
						data.removeDecorBag = new Array();
						for (i = 0; i < objdata.removeDecorBag.length; i++) 
						{
							data.removeDecorBag.push(new DecorVo().setValue( { id:objdata.removeDecorBag[i][0],
							num:objdata.removeDecorBag[i][1],
							d_id:objdata.removeDecorBag[i][2]}
							));
						}
						tmdm.removeDecorBag(data.removeDecorBag);
						EventManager.getInstance().dispatchEvent(new DataManagerEvent(DataManagerEvent.DECORBAG_CHANGE));
					break;
					
					case "tasks":
						data.tasks = new Array();
						var tmptask:TaskVo;
						for (i = 0; i < objdata.tasks.length; i++) 
						{
							tmptask = new TaskVo().setValue(data.tasks[i]);
							DataManager.getInstance().setTask(tmptask);
							data.tasks.push(tmptask);
						}
					break;
					
					case "changeTasks":
						var tmpTaskChangeEvent:TaskEvent = new TaskEvent(TaskEvent.TASKS_STATE_CHANGE);
						
						data.changeTasks = new Array();
						data.addTasks = new Array();
						var tmpchangetask:TaskVo;
						for (i = 0; i < objdata.changeTasks.length; i++) 
						{
							tmpchangetask = new TaskVo().setValue(objdata.changeTasks[i]);
							//更新任务数据,并判断是否新任务
							if (DataManager.getInstance().setTask(tmpchangetask)) {
								data.addTasks.push(tmpchangetask);
								tmpTaskChangeEvent.addTasks.push(tmpchangetask);
							}else {
								data.changeTasks.push(tmpchangetask);
								tmpTaskChangeEvent.changeTasks.push(tmpchangetask);
							}
						}
						
						EventManager.getInstance().dispatchEvent(tmpTaskChangeEvent);
					break;
					
					case "students":
						data.students = new Array();
						var student_vo:StudentVo =  new StudentVo();
						for ( i = 0; i < objdata.students.length; i++ ) {
							student_vo.setValue(objdata.students[i]);
							if (objdata.students[i].state == StudentStateType.FIDDLE) {
								tmdm.fiddleStudents.push(student_vo);
							} else {
								tmdm.onDeskStudents.push(student_vo);
							}
							//增加到要创建的学生列表
							var tmpdoorid:int = ((this["door"] as Door).data as DecorVo).id;
							if (!tmdm.openDoorStudents[tmpdoorid]) 
							{
								tmdm.openDoorStudents[tmpdoorid] = new Array();
							}
							tmdm.openDoorStudents[tmpdoorid].push(student_vo);
							//tmdm.openDoorStudents.push(student_vo);
							data.students.push(student_vo);
						}
					break;
					
					case "results":
						data.results = new Array();
						var tmpresult:ResultVo;
						for (i = 0; i < objdata.results.length; i++) 
						{
							tmpresult = new ResultVo().setValue(objdata.results[i]);
							data.results.push(tmpresult);
						}
					break;
					
					case "changeStudents":
						data.changeStudents = new Array();
						var change_student_vo:StudentVo;
						for ( i = 0; i < objdata.changeStudents.length; i++ ) {
							change_student_vo =  new StudentVo();
							change_student_vo.setValue(objdata.changeStudents[i]);
							data.changeStudents.push(change_student_vo);
						}
					break;
					
					case "changeStudent":
						data.changeStudent = new StudentVo();
						data.changeStudent.setValue(objdata.changeStudent);
					break;
					
					case "scene":
						//清除场景数据
						tmdm.decorList = new Object();
						tmdm.floorList = [];
						tmdm.wallList = [];
						tmdm.fiddleStudents = [];
						tmdm.onDeskStudents = [];
						
						
						//场景建筑数据
						if (objdata.scene.decorList) 
						{
							for ( i= 0; i < objdata.scene.decorList.length; i++) 
							{
								decor_vo =  new DecorVo();
								decor_vo.setValue(objdata.scene.decorList[i]);
								//分类
								if (!DataManager.getInstance().decorList[decor_vo.type]) {
									tmdm.decorList[decor_vo.type] = new Array();
								}
								tmdm.decorList[decor_vo.type].push(decor_vo);
							}
						}
						

						//地板
						tmdm.floorList = objdata.scene.floorList;
						//墙壁
						tmdm.wallList = objdata.scene.wallList;
						
						//赋值userVo
						user_vo =  new UserVo().setData(objdata.scene.user);
						user_vo.students_limit = tmdm.getRoomLevel(user_vo.roomLevel).student_limit;
						user_vo.desk_limit = tmdm.getRoomLevel(user_vo.roomLevel).desk_limit;
						tmdm.curSceneUser = user_vo;
						if (tmdm.currentUser) 
						{
							if (user_vo.uid==tmdm.currentUser.uid) 
							{
								tmdm.currentUser = user_vo;
							}
						}
						
						//学生状态数据
						tmdm.studentStates = new Array();
						for (i = 0; i < DataManager.getInstance().studentClass.length; i++) 
						{
							item = DataManager.getInstance().studentClass[i];
							
							tmdm.studentStates.push(new StudentStateVo().setValue( { sid:item.sid } ));
						}
						if (objdata.scene.studentStates) 
						{
							
							for (i = 0; i < objdata.scene.studentStates.length; i++) 
							{
								for (j = 0; j < tmdm.studentStates.length; j++) 
								{
									item2 = tmdm.studentStates[j];
									if (item2.sid==objdata.scene.studentStates[i].sid) 
									{
										item2.setValue(objdata.scene.studentStates[i]);
										item2.unLock = 1;
										continue;
									}
								}
							}
						}
					
						//屋子中的学生
						for ( i = 0; i < objdata.scene.students.length; i++ ) {
							scene_student_vo =  new StudentVo();
							scene_student_vo.setValue(objdata.scene.students[i]);
							if (scene_student_vo.state == StudentStateType.FIDDLE) {
								tmdm.fiddleStudents.push(scene_student_vo);
							} else {
								tmdm.onDeskStudents.push(scene_student_vo);
							}
						}
						
						tmdm.enemys = new Array(); 
						if (objdata.scene.enemys) 
						{
							for (i = 0; i < objdata.scene.enemys.length; i++) 
							{
								tmdm.enemys.push(new EnemyVo().setValue(objdata.scene.enemys[i]));
							}
						}
						
					break;
					
					case "decorBagList":
						tmdm.decorBagList = new Array();
						var decor_array:Array = new Array();
						for ( i= 0; i < objdata.decorBagList.length; i++ ) 
						{
							decor_vo =  new DecorVo();
							decor_vo.setValue(objdata.decorBagList[i]);
							decor_array.push(decor_vo);
						}
						tmdm.addDecor(decor_array);
					break;
					
					case "diarys":
						if (!tmdm.diarys) 
						{
							tmdm.diarys = new Array();
						}
						for (i = 0; i < objdata.diarys.length; i++) 
						{
							tmdm.diarys.push(new DiaryVo().setData(objdata.diarys[i]));
						}
						//tmdm.diarys.sortOn("createTime");
						
						EventManager.getInstance().dispatchEvent(new DiaryEvent(DiaryEvent.DIARY_ADDED));
					break;
					
					//学生数据的变化值
					case "changeStudentState":
						var tmpstudentstate:StudentStateVo;
						var tmpstudentstate2:StudentStateVo;
						
						tmpstudentstate2 = new StudentStateVo().setValue(objdata.changeStudentState);
						data.changeStudentState = tmpstudentstate2;
						
						//修改当前学生状态数据
						tmpstudentstate = DataManager.getInstance().getStudentState(tmpstudentstate2.sid);
						tmpstudentstate.changeValue(objdata.changeStudentState);
						
						//如果升级，表现升级动画并更新学生形象
						if (objdata.changeStudentState.level>0) 
						{
							var student:Student = (DataManager.getInstance().worldState.world as MagicWorld).getStudent(tmpstudentstate.sid);
							var mc:McShower = new McShower(studentLevelUpMv, student.view.container, null, null, studentLevelMv_complete, [student]);
						}
					break;
					
					case "story":
						//剧情
						data.story = new StoryVo().setData(objdata.story);
						//表现剧情
						new StoryPlayCommand(data.story,DataManager.getInstance().worldState);
					break;
				}
			}
		}
		
		
		private function refreshScene(scenedata:Object):void {
			var tmdm:DataManager = DataManager.getInstance();
			
			//清除场景数据
			tmdm.decorList = new Object();
			tmdm.floorList = [];
			tmdm.wallList = [];
			tmdm.fiddleStudents = [];
			tmdm.onDeskStudents = [];
			
			var i:int;
			var j:int;
			var decor_vo:DecorVo;
			var user_vo:UserVo;
			var item:StudentClassVo;
			var item2:StudentStateVo;
			
			var scene_student_vo:StudentVo;
			
			//场景建筑数据
			if (scenedata.decorList) 
			{
				for ( i= 0; i < scenedata.decorList.length; i++) 
				{
					decor_vo =  new DecorVo();
					decor_vo.setValue(scenedata.decorList[i]);
					//分类
					if (!DataManager.getInstance().decorList[decor_vo.type]) {
						tmdm.decorList[decor_vo.type] = new Array();
					}
					tmdm.decorList[decor_vo.type].push(decor_vo);
				}
			}
			
			//地板
			tmdm.floorList = scenedata.floorList;
			//墙壁
			tmdm.wallList = scenedata.wallList;
			
			//赋值userVo
			user_vo =  new UserVo().setData(scenedata.user);
			tmdm.curSceneUser = user_vo;
			if (tmdm.currentUser) 
			{
				if (user_vo.uid==tmdm.currentUser.uid) 
				{
					tmdm.currentUser = user_vo;
				}
			}
			
			//学生状态数据
			tmdm.studentStates = new Array();
			for (i = 0; i < DataManager.getInstance().studentClass.length; i++) 
			{
				item = DataManager.getInstance().studentClass[i];
				
				tmdm.studentStates.push(new StudentStateVo().setValue( { sid:item.sid } ));
			}
			if (scenedata.studentStates) 
			{
				
				for (i = 0; i < scenedata.studentStates.length; i++) 
				{
					for (j = 0; j < tmdm.studentStates.length; j++) 
					{
						item2 = tmdm.studentStates[j];
						if (item2.sid==scenedata.studentStates[i].sid) 
						{
							item2.setValue(scenedata.studentStates[i]);
							continue;
						}
					}
				}
			}
					
			//屋子中的学生
			for ( i = 0; i < scenedata.students.length; i++ ) {
				scene_student_vo =  new StudentVo();
				scene_student_vo.setValue(scenedata.students[i]);
				if (scene_student_vo.state == StudentStateType.FIDDLE) {
					tmdm.fiddleStudents.push(scene_student_vo);
				} else {
					tmdm.onDeskStudents.push(scene_student_vo);
				}
			}
			
			tmdm.enemys = new Array(); 
			if (scenedata.enemys) 
			{
				for (i = 0; i < scenedata.enemys.length; i++) 
				{
					tmdm.enemys.push(new EnemyVo().setValue(scenedata.enemys[i]));
				}
			}
			
			//清除场景
			DataManager.getInstance().worldState.world.clear();
			//设置数据重新创建场景
			var world_data:Object = new Object();
			world_data['decorList'] = DataManager.getInstance().decorList;
			world_data['floorList'] = DataManager.getInstance().floorList;
			world_data['wallList'] = DataManager.getInstance().wallList;
			world_data['userInfo'] = DataManager.getInstance().curSceneUser;
			world_data['studentsList'] = DataManager.getInstance().getStudentsInRoom();
			DataManager.getInstance().worldState.world.create(world_data, true);
		}
		
		/**
		 * 学生升级动画完成时
		 */
		public function studentLevelMv_complete(student:Student):void 
		{
			student.resetView();
		}
		
		override public function commandComplete():void
		{
			
			if (callBack!=null) 
			{
				if (data.result) 
				{
					if (data.result.isSuccess) 
					{
						callBack.call();
					}
				}
			}
			dispatchEvent(new Event(Event.COMPLETE));
		}
		
	}

}