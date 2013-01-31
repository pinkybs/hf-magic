package happymagic.display.control 
{
	import com.friendsofed.isometric.Point3D;
	import flash.display.SimpleButton;
	import flash.events.MouseEvent;
	import flash.utils.setTimeout;
	import happyfish.events.ModuleEvent;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.ModuleManager;
	import happyfish.scene.camera.CameraControl;
	import happyfish.scene.camera.MovieMaskView;
	import happyfish.scene.personAction.PersonActionVo;
	import happyfish.scene.world.grid.Person;
	import happyfish.scene.world.WorldState;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.task.TaskInfoView;
	import happymagic.display.view.ui.AwardResultView;
	import happymagic.display.view.ui.personMsg.PersonMsgManager;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.AvatarVo;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.StoryActionVo;
	import happymagic.model.vo.StoryVo;
	import happymagic.model.vo.TaskClassVo;
	import happymagic.model.vo.TaskVo;
	import happymagic.scene.world.bigScene.MassesView;
	import happymagic.scene.world.bigScene.StoryPersonView;
	import happymagic.scene.world.control.AvatarCommand;
	import happymagic.scene.world.MagicWorld;
	/**
	 * ...
	 * @author slamjj
	 */
	public class StoryPlayCommand 
	{
		private var story:StoryVo;
		private var npcs:Object;
		private var needInitNpcNum:uint;
		private var currentActionIndex:uint;
		private var maskmv:MovieMaskView;
		private var _worldState:WorldState;
		private var taskId:uint;
		private var skipBtn:SimpleButton;
		private var killed:Boolean;
		
		public function StoryPlayCommand(_story:StoryVo,_state:WorldState) 
		{
			story = _story;
			_worldState = _state;
			DisplayManager.sceneSprite.mouseChildren = false;
			DisplayManager.uiSprite.mouseChildren = false;
			//_worldState.view.stage.mouseChildren = false;
			initData();
		}
		
		private function initData():void {
			//分解出所有人物数据
			needInitNpcNum=0;
			npcs = new Object();
			
			var item:AvatarVo;
			for (var i:int = 0; i < story.actions.length; i++) 
			{
				if (!npcs[story.actions[i].npcId]) 
				{
					needInitNpcNum++;
					if (story.actions[i].avatarId==0) 
					{
						npcs[story.actions[i].npcId] = { npcId:story.actions[i].npcId, avatarId:DataManager.getInstance().currentUser.avatar,class_name:DataManager.getInstance().currentUser.className,name:DataManager.getInstance().currentUser.name };
					}else {
						item = DataManager.getInstance().getAvatarVo(story.actions[i].avatarId);
						npcs[story.actions[i].npcId] = { npcId:story.actions[i].npcId, avatarId:item.avatarId,class_name:item.className,name:item.name };
					}
				}
			}
			
			initNpc();
		}
		
		private function initNpc():void {
			var tmpperson:StoryPersonView;
			
			for (var name:String in npcs) 
			{
				var item:Object = npcs[name];
				tmpperson = new StoryPersonView(item, DataManager.getInstance().worldState, initNpc_callback);
				npcs[name].view = tmpperson;
				_worldState.world.addItem(tmpperson);
			}
		}
		
		public function initNpc_callback():void 
		{
			needInitNpcNum--;
			if (needInitNpcNum<=0) 
			{
				initNpc_complete();
			}
		}
		
		private function initNpc_complete():void 
		{
			hideUi();
		}
		
		private function hideUi():void {
			//隐藏UI
			DisplayManager.uiSprite.visible = false;
			//隐藏所有npc与学生
			(DataManager.getInstance().worldState.world as MagicWorld).hidePlayer();
			
			showMask();
		}
		
		private function showMask():void {
			maskmv = new MovieMaskView();
			maskmv.showMaskMv( DisplayManager.storyUiSprite, DisplayManager.uiSprite.stage.stageWidth,
					DisplayManager.uiSprite.stage.stageHeight, 100, 1);
					
			setTimeout(startPlay,1200);
		}
		
		private function startPlay():void {
			
			//显示SKIP按钮
			skipBtn = new storySkipBtn();
			skipBtn.x = maskmv.getRect(maskmv.stage).right - 130;
			skipBtn.y = maskmv.getRect(maskmv.stage).top + 40;
			maskmv.parent.addChild(skipBtn);
			skipBtn.addEventListener(MouseEvent.CLICK, skipFun);
			
			currentActionIndex = 0;
			takeAction(story.actions[currentActionIndex]);
		}
		
		private function skipFun(e:MouseEvent):void 
		{
			
			var tmpaction:StoryActionVo = story.actions[story.actions.length - 1];
			if (tmpaction.coin || tmpaction.gem || tmpaction.decorId || tmpaction.itemId || tmpaction.taskId ) 
			{
				//判断当前是不是已经在执行最后一条了
				if (currentActionIndex!=story.actions.length-1) 
				{
					//如果有奖励或任务,就显示他们再结束剧情动画
					takeAwardTaskAction(tmpaction);
				}
			}
			
			storyComplete();
		}
		
		private function takeAwardTaskAction(action:StoryActionVo):void {
			if (action.coin || action.gem || action.decorId || action.itemId) 
				{
					setTimeout(showAward,1000,action);
					taskId = action.taskId;
				}else {
					//显示得到新任务
					if (action.taskId) 
					{
						showNewTask(action.taskId);
					}
				}
		}
		
		private function takeAction(action:StoryActionVo):void {
			
			//显示奖励
			
			if (action.coin || action.gem || action.decorId || action.itemId || action.taskId) 
			{
				
				takeAwardTaskAction(action);
				
				//奖励和任务只出现在最后一条行为，任务或奖励后直接关闭mask，不然ui会不能显示，也就看不到任务和奖励面板了
				//所以这里直接就下一步行为（也就是结束剧情了）
				nextAction();
				return;
			}
			
			
			
			var npc:StoryPersonView = npcs[action.npcId].view;
			
			if (action.x || action.y) 
			{
				if (action.immediately) 
				{
					//立即把NPC移动到指定位置
					npc.setPos(new Point3D(action.x, 0, action.y));
					if (action.faceX || action.faceY) 
					{
						npc.faceTowardsSpace(new Point3D(action.faceX, 0, action.faceY));
						npc.stopAnimation(Person.MOVE);
					}
					//执行下一步
					nextAction();
				}else {
					if (action.wait) 
					{
						npc.addCommand(new AvatarCommand(new Point3D(action.x, 0, action.y), nextAction,new Point3D(action.faceX,0,action.faceY)));
					}else {
						npc.addCommand(new AvatarCommand(new Point3D(action.x, 0, action.y),null,new Point3D(action.faceX,0,action.faceY)));
						nextAction();
					}
					
				}
			}else if (action.content) 
			{
				//显示对话
				if (action.wait) 
				{
					PersonMsgManager.getInstance().addMsg(npc, action.content, action.chatTime, nextAction);
				}else {
					PersonMsgManager.getInstance().addMsg(npc, action.content,action.chatTime);
					nextAction();
				}
			}else if (action.hide) 
			{
				clearNpc(npc.data.npcId);
				nextAction();
			}
			
			checkCamera(action, npc);
		}
		
		private function showAward(action:StoryActionVo):void {
			var awards:Array = new Array();
				var i:int;
				if (action.coin) 
				{
					awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_COIN, num:action.coin } ));
				}
				
				if (action.gem) 
				{
					awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_GEM, num:action.gem } ));
				}
				
				if (action.itemId)
				{
					for (i = 0; i < action.itemId.length; i++) 
					{
						awards.push(new ConditionVo().setData( { type:ConditionType.ITEM, id:action.itemId[i][0], num:action.itemId[i][1] } ));
					}
				}
				
				if (action.decorId)
				{
					for (i = 0; i < action.decorId.length; i++) 
					{
						awards.push(new ConditionVo().setData( { type:ConditionType.DECOR, id:action.decorId[i][0], num:action.decorId[i][1] } ));
					}
				}
				
				ModuleManager.getInstance().addEventListener(ModuleEvent.MODULE_CLOSE, awardView_close);
				var awardwin:AwardResultView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_AWARD_RESULT, ModuleDict.MODULE_AWARD_RESULT_CLASS,true) as AwardResultView;
					awardwin.setData( { name:LocaleWords.getInstance().getWord("awardTile"), awards:awards } );
				DisplayManager.uiSprite.setBg(awardwin);
		}
		
		private function showNewTask(taskId:uint):void {
			
			//nextAction();
			var taskvo:TaskVo = DataManager.getInstance().getTaskByTid(taskId);
			if (taskvo) 
			{
				var tmptask:TaskInfoView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_TASKINFO, ModuleDict.MODULE_TASKINFO_CLASS) as TaskInfoView;
				tmptask.setData([taskvo]);
			}
			
		}
		
		private function awardView_close(e:ModuleEvent):void 
		{
			e.target.removeEventListener(ModuleEvent.MODULE_CLOSE, awardView_close);
			//显示得到新任务
			if (taskId) 
			{
				showNewTask(taskId);
			}
		}
		
		private function checkCamera(action:StoryActionVo,npc:StoryPersonView):void {
			if (action.camera) 
			{
				CameraControl.getInstance().followTarget(npc.asset, _worldState.view.isoView.camera);
			}
		}
		
		private function nextAction():void {
			
			if (killed) 
			{
				return;
			}
			
			currentActionIndex++;
			if (currentActionIndex<=story.actions.length-1) 
			{
				takeAction(story.actions[currentActionIndex]);
			}else {
				storyComplete();
			}
		}
		
		/**
		 * 剧情完成
		 */
		private function storyComplete():void 
		{	
			killed = true;
			//关闭黑幕
			maskmv.closeMaskMv();
			
			setTimeout(closeMovieMask_complete, 1000);
			
			//清除command
			clear();
		}
		
		private function closeMovieMask_complete():void 
		{
			//显示UI和主角
			DisplayManager.uiSprite.visible = true;
			//隐藏所有npc与学生
			(DataManager.getInstance().worldState.world as MagicWorld).showPlayer();
			
			
		}
		
		private function clearNpc(name:String):void {
			var item:StoryPersonView = npcs[name].view;
			item.remove();
			npcs[name] = null;
		}
		
		private function clear():void 
		{
			maskmv = null;
			
			skipBtn.removeEventListener(MouseEvent.CLICK, skipFun);
			skipBtn.parent.removeChild(skipBtn);
			skipBtn = null;
			
			//去除所有人物
			for (var name:String in npcs) 
			{
				var item:StoryPersonView = npcs[name].view;
				item.remove();
			}
			npcs = null;
			
			DisplayManager.sceneSprite.mouseChildren = 
			DisplayManager.uiSprite.mouseChildren = true;
		}
		
	}

}