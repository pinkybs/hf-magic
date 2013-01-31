package happymagic.model.command 
{
	import adobe.utils.CustomActions;
	import com.adobe.serialization.json.JSON;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.model.vo.GuidesVo;
	import happyfish.task.vo.ITaskVo;
	import happymagic.manager.DataManager;
	import happymagic.model.MagicUrlLoader;
	import happymagic.model.vo.DecorClassVo;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.DiaryVo;
	import happymagic.model.vo.ItemVo;
	import happymagic.model.vo.TaskVo;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.grid.person.Student;
	
	/**
	 * 场景,用户信息初始化
	 * @author Beck
	 */
	public class initCommand extends BaseDataCommand
	{
		
		public function initCommand() 
		{

		}
		
		public function load():void {
			
			createLoad();
			
			createRequest(InterfaceURLManager.getInstance().getUrl('loadinit'), { tmp:1 } );
			var loader:MagicUrlLoader = new MagicUrlLoader();
			loader.retry = true;
			loader.addEventListener(Event.COMPLETE, load_complete);
			
			
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			var tmdm:DataManager = DataManager.getInstance();
			
			//赋值userVo
			DataManager.getInstance().userInfo = DataManager.getInstance().curSceneUser;
			DataManager.getInstance().currentUser = DataManager.getInstance().curSceneUser;
			
			//水晶交换包
			if (data.switchVo) 
			{
				DataManager.getInstance().switchVo = data.switchVo;
			}
			
			var i:int;
			if (objdata.magics) 
			{
				//玩家学会的魔法
				for (i = 0; i < objdata.magics.length; i++) 
				{
					DataManager.getInstance().magicList.push(DataManager.getInstance().getMagicClass(objdata.magics[i]));
				}
			}
			
			
			//变化术
			DataManager.getInstance().transMagics = objdata.transMagics;
			//拥有的道具
			if (objdata.items) 
			{
				var tmpItemvo:ItemVo;
				var tmpItem:Array;
				DataManager.getInstance().items = new Array();
				for (i = 0; i < objdata.items.length; i++) 
				{
					tmpItem = objdata.items[i] as Array;
					tmpItemvo = new ItemVo().setValue( { i_id:tmpItem[0], num:tmpItem[1], id:tmpItem[2] } );
					DataManager.getInstance().items.push(tmpItemvo);
				}
			}
			
			
			//任务
			if (objdata.tasks) 
			{
				var tmptaskArr:Vector.<ITaskVo> = new Vector.<ITaskVo>();
				for (i = 0; i < objdata.tasks.length; i++) 
				{
					tmptaskArr.push(new TaskVo().setValue(objdata.tasks[i]));
				}
				DataManager.getInstance().tasks = tmptaskArr;
				
			}
			
			//场景状态
			if (objdata.sceneState) 
			{
				tmdm.scenes = new Array();
				for (i = 0; i < objdata.sceneState.length; i++) 
				{
					tmdm.scenes.push(tmdm.getSceneVoByClass(objdata.sceneState[i][0],objdata.sceneState[i][1]));
				}
			}
			
			//日志
			if (objdata.diarys) 
			{
				
				
			}
			
			//引导状态
			tmdm.guides = new Array();
			if (objdata.guides) 
			{
				for (i = 0; i < objdata.guides.length; i++) 
				{
					tmdm.guides.push(new GuidesVo().setValue(objdata.guides[i]));
				}
			}
			
			//活动模块数据
			tmdm.acts = new Array();
			if (objdata.acts) 
			{
				for (var j:int = 0; j < objdata.acts.length; j++) 
				{
					tmdm.acts.push(new ActVo().setData(objdata.acts[j]));
				}
			}
			
			//连续模块数据
			//tmdm.signAward = new Array();
			//if (objdata.signAward)
			//{
				//for (i =  0; i < objdata.signAward.length; i++) 
				//{
					//tmdm.signAward.push(objdata.signAward[i]);
				//}
			//}
			commandComplete();
		}
	}

}