package happymagic.model.command 
{
	import com.brokenfunction.json.decodeJson;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import happyfish.feed.vo.FeedVo;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.model.vo.GuidesClassVo;
	import happymagic.manager.DataManager;
	import happymagic.model.MagicUrlLoader;
	import happymagic.model.vo.AvatarVo;
	import happymagic.model.vo.DecorClassVo;
	import happymagic.model.vo.EnemyClassVo;
	import happymagic.model.vo.ItemClassVo;
	import happymagic.model.vo.ItemVo;
	import happymagic.model.vo.LevelInfoVo;
	import happymagic.model.vo.MagicClassVo;
	import happymagic.model.vo.MixMagicVo;
	import happymagic.model.vo.NpcChatVo;
	import happymagic.model.vo.NpcVo;
	import happymagic.model.vo.RoomLevelVo;
	import happymagic.model.vo.RoomSizeVo;
	import happymagic.model.vo.SceneClassVo;
	import happymagic.model.vo.SignAwardVo;
	import happymagic.model.vo.StudentClassVo;
	import happymagic.model.vo.StudentLevelClassVo;
	import happymagic.model.vo.SwitchLevelVo;
	import happymagic.model.vo.TaskClassVo;
	import happymagic.model.vo.TaskTipsVo;
	import happymagic.model.vo.TransMagicVo;
	/**
	 * 初始静态信息
	 * @author Beck
	 */
	public class initStaticCommand extends EventDispatcher
	{
		
		public function initStaticCommand() 
		{
			
		}
		
		public function load():void {
			var loader:MagicUrlLoader = new MagicUrlLoader();
			loader.retry = true;
			loader.addEventListener(Event.COMPLETE, load_complete);
			
			var request:URLRequest = new URLRequest(InterfaceURLManager.getInstance().getUrl('loadstatic'));
			request.method = URLRequestMethod.POST;
			var vars:URLVariables = new URLVariables();
			vars.tmp = 1;
			
			request.data = vars;
			loader.load(request);
		}
		
		private function load_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, load_complete);
			
			//return;
			var data:Object = decodeJson(e.target.data);
			
			var i:int;
			
			
			//存入datamanager
			var datam:DataManager = DataManager.getInstance();
			
			
			var decor_array:Array = new Array();
			//装饰物类
			for (var name:String in data.decorClass) {
				var decor_class_vo:DecorClassVo =  new DecorClassVo();
				decor_class_vo.setData(data.decorClass[name]);
				if (decor_class_vo.d_id == 191001) {
					decor_class_vo.size_z = 2;
				}
				
				decor_array.push(decor_class_vo);
			}
			datam.decorClass = decor_array;
			
			//魔法课程类
			var magic_array:Array = new Array();
			var magic_class_vo:MagicClassVo;
			for (i = 0; i < data.magicClass.length; i++) 
			{
				magic_class_vo =  new MagicClassVo();
				magic_class_vo.setData(data.magicClass[i]);
				
				magic_array.push(magic_class_vo);
			}
			datam.magicClass = magic_array;
			
			//合成术类
			var mix_array:Array = new Array();
			var mix_class_vo:MixMagicVo;
			for (i = 0; i < data.mixMagicClass.length; i++) 
			{
				mix_class_vo =  new MixMagicVo();
				mix_class_vo.setData(data.mixMagicClass[i]);
				
				mix_array.push(mix_class_vo);
			}
			datam.mixMagics = mix_array;
			
			//变化术类
			var trans_array:Array = new Array();
			var trans_class_vo:TransMagicVo;
			for (i = 0; i < data.transMagicClass.length; i++) 
			{
				trans_class_vo =  new TransMagicVo();
				trans_class_vo.setData(data.transMagicClass[i]);
				
				//TODO tmp
				//trans_class_vo.class_name = "magicchange.1.egg";
				
				trans_array.push(trans_class_vo);
			}
			datam.transMagicClass = trans_array;
			
			//道具基础类
			var item_array:Array = new Array();
			var item_class_vo:ItemClassVo;
			for (i = 0; i < data.itemClass.length; i++) 
			{
				item_class_vo =  new ItemClassVo();
				item_class_vo.setData(data.itemClass[i]);
				
				item_array.push(item_class_vo);
			}
			datam.itemClass = item_array;
			
			//任务类
			if (data.taskClass) 
			{
				var taskArr:Array = new Array();
				var tmpTask:TaskClassVo = new TaskClassVo();
				for (i = 0; i < data.taskClass.length; i++) 
				{
					tmpTask =  new TaskClassVo().setData(data.taskClass[i]) as TaskClassVo;
					taskArr.push(tmpTask);
				}
				datam.taskClass = taskArr;
			}
			
			
			//avatar
			if (data.avatarClass) 
			{
				for (i = 0; i < data.avatarClass.length; i++) 
				{
					datam.avatars.push(new AvatarVo().setData(data.avatarClass[i]));
				}
			}
			
			
			//用户等级信息
			datam.levelInfos = new Array();
			if (data.levelInfos) 
			{
				for (i = 0; i < data.levelInfos.length; i++) 
				{
					datam.levelInfos.push(new LevelInfoVo().setData(data.levelInfos[i]));
				}
			}
			
			//房间等级信息
			datam.roomLevelClass = new Array();
			if (data.roomLevelClass) 
			{
				for (i = 0; i < data.roomLevelClass.length; i++) 
				{
					datam.roomLevelClass.push(new RoomLevelVo().setValue(data.roomLevelClass[i]));
				}
			}
			
			//交换背包信息
			//if (data.switchLevel) 
			//{
				//data.switchLevel.sortOn("level", Array.NUMERIC);
				//datam.switchLevel = new Array();
				//for (i = 0; i < data.switchLevel.length; i++) 
				//{
					//datam.switchLevel.push(new SwitchLevelVo().setData(data.switchLevel[i]));
				//}
			//}
			
			//场景类
			if (data.sceneClass) 
			{
				var sceneClass:Array = new Array();
				for (i = 0; i < data.sceneClass.length; i++) 
				{
					sceneClass.push(new SceneClassVo().setData(data.sceneClass[i]));
				}
				data.sceneClass = sceneClass;
				datam.sceneClass = sceneClass;
			}
			
			//npc类
			if (data.npcClass) 
			{
				var npcClass:Array = new Array();
				for (i = 0; i < data.npcClass.length; i++) 
				{
					npcClass.push(new NpcVo().setData(data.npcClass[i]));
				}
				data.npcClass = npcClass;
				datam.npcClass = npcClass;
			}
			
			if (data.enemyClass) 
			{
				var tmpEnamyClass:Array = new Array();
				for (i = 0; i < data.enemyClass.length; i++) 
				{
					tmpEnamyClass.push(new EnemyClassVo().setData(data.enemyClass[i]));
				}
				data.enemyClass = tmpEnamyClass;
				datam.enemyClass = tmpEnamyClass;
			}
			
			if (data.guideClass) 
			{
				var tmpGuideClass:Array = new Array();
				for (i = 0; i < data.guideClass.length; i++) 
				{
					tmpGuideClass.push(new GuidesClassVo().setData(data.guideClass[i]));
				}
				data.guideClass = tmpGuideClass;
				datam.guidesClass = tmpGuideClass;
			}
			
			//房间扩展信息
			if (data.roomSizeClass) 
			{
				var tmpRoomSize:Array = new Array();
				for (i = 0; i < data.roomSizeClass.length; i++) 
				{
					tmpRoomSize.push(new RoomSizeVo().setData(data.roomSizeClass[i]));
				}
				data.roomSizeClass = tmpRoomSize;
				datam.roomSizeClass = tmpRoomSize;
			}
			
			//feed类
			if (data.feedClass) 
			{
				var tmpFeedClass:Array = new Array();
				for (i = 0; i < data.feedClass.length; i++) 
				{
					tmpFeedClass.push(new FeedVo().setData(data.feedClass[i]));
				}
				data.feedClass = tmpFeedClass;
				datam.feedClass = tmpFeedClass;
			}
			
			//学生等级类数据
			if (data.studentLevelClass) 
			{
				var tmpStudentLevelClass:Array = new Array();
				for (i = 0; i < data.studentLevelClass.length; i++) 
				{
					tmpStudentLevelClass.push(new StudentLevelClassVo().setData(data.studentLevelClass[i]));
				}
				data.studentLevelClass = tmpStudentLevelClass;
				datam.studentLevelClass = tmpStudentLevelClass;
			}
			
			//学生类
			if (data.studentClass) 
			{
				var tmpStudentClass:Array = new Array();
				for (i = 0; i < data.studentClass.length; i++) 
				{
					tmpStudentClass.push(new StudentClassVo().setData(data.studentClass[i]));
				}
				data.studentClass = tmpStudentClass;
				datam.studentClass = tmpStudentClass;
			}
			
			//连续登陆类
			if (data.signAwardClass)
			{
				var tmpsignAwardClass:Array = new Array();
				for (i = 0; i < data.signAwardClass.length; i++) 
				{
					tmpsignAwardClass.push(new SignAwardVo().setVaule(data.signAwardClass[i]));
				}
				data.signAwardClass = tmpsignAwardClass;
				datam.signAwardClass = tmpsignAwardClass;				
			}
			
			//任务说明类
			if (data.taskTips) 
			{
				var taskTips:Array = new Array();
				for (var j:int = 0; j < data.taskTips.length; j++) 
				{
					taskTips.push(new TaskTipsVo().setData(data.taskTips[j]));
				}
				data.taskTips = taskTips;
				datam.taskTips = taskTips;
			}
			
			//配置信息
			//datam.basicClass = data.basicClass;
			
			dispatchEvent(new Event(Event.COMPLETE));
		}
	}

}