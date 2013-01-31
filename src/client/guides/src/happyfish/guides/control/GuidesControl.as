package happyfish.guides.control 
{
	import com.friendsofed.isometric.IsoUtils;
	import com.greensock.TweenMax;
	import flash.display.DisplayObjectContainer;
	import flash.events.Event;
	import flash.geom.Point;
	import happyfish.events.ModuleEvent;
	import happyfish.guides.view.GuidesView;
	import happyfish.manager.ActTipsManager;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.ModuleMvType;
	import happyfish.manager.module.vo.ModuleVo;
	import happyfish.model.command.SaveGuidesCommand;
	import happyfish.model.vo.GuidesState;
	import happyfish.model.vo.GuidesVo;
	import happyfish.utils.display.McShower;
	import happyfish.view.SysChatsMsgView;
	import happymagic.display.view.magic.MagicItemList;
	import happymagic.display.view.magic.MagicItemRender;
	import happymagic.display.view.ModuleDict;
	import happymagic.events.ActionStepEvent;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.UiManager;
	import happymagic.scene.world.grid.item.Desk;
	import happymagic.scene.world.grid.item.Door;
	import happymagic.scene.world.grid.person.Player;
	import happymagic.scene.world.grid.person.Student;
	import happymagic.scene.world.MagicWorld;
	/**
	 * ...
	 * @author jj
	 */
	public class GuidesControl
	{
		private var data:Array;
		private var curStepGuides:GuidesVo;
		private var chatMsgView:SysChatsMsgView;
		private var curStepIndex:uint;
		private var listView:GuidesView;
		private var teacherface:String;
		private var modeulevo:ModuleVo
		public function GuidesControl(__listView:GuidesView) 
		{
			listView = __listView;

			modeulevo = new ModuleVo();
			modeulevo.name = "sysChatsMsgView";
			modeulevo.className = "happyfish.view.SysChatsMsgView";
			modeulevo.layer = 1;
			modeulevo.single = false;
			modeulevo.algin = AlginType.BR;
			modeulevo.x = 15;
			modeulevo.y = 65;
			modeulevo.fx = -300;
			modeulevo.fy = 20;
			modeulevo.mvType = ModuleMvType.FROM_RIGHT;
			modeulevo.mvTime = 0.5;
			
			EventManager.getInstance().addEventListener(ActionStepEvent.ACTION_HAPPEN, actionHappen);
			EventManager.getInstance().addEventListener(SceneEvent.CHANGE_SCENE, hideguide);
			EventManager.getInstance().addEventListener(SceneEvent.SCENE_DATA_COMPLETE,SceneChangeComplete);
			ModuleManager.getInstance().addEventListener(ModuleEvent.MODULE_CLOSE, moduleClose);
		}
		
		private function moduleClose(e:ModuleEvent):void
		{
			switch(e.moduleName)
			{
				case ModuleDict.MODULE_COMPOUNDTOTAL:
				EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_MIXBUTTONCONTACTEVENT));
				EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_MIXCOMPLETECONTACTEVENT));
				break;
				
				case ModuleDict.MODULE_MAGICCLASS:
				EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_TEACHMAGICCONTACTEVENT));				
				break;
				
				case ModuleDict.MODULE_USEMAGIC_LIST:
				EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_CHANGEARTCONTACTEVENT));
				//
				break;
			}
		}
		
		private function chats_complete(e:Event):void 
		{
			curStepGuides = DataManager.getInstance().getCurGuides();
			
			if (curStepGuides) 
			{
				showActTips(curStepGuides.actTips[curStepIndex]);
			}
		}
		
		private function actionHappen(e:ActionStepEvent):void 
		{
			if (!curStepGuides) {
				return;
			}
			
			var i:int;
			for (i = 0; i < curStepGuides.contactevent.length; i++ )
			{
				if (e.actType == curStepGuides.contactevent[i])
				{
					 curStepIndex = curStepGuides.contact[i];
					 curStepIndex = curStepIndex - 1;
					 if (curStepIndex == -1)
					 {
						 
						 if (curStepGuides.actTips[curStepIndex] != "blank")
						 {
							 showActTips(curStepGuides.actTips[curStepIndex]);
						 }
						 if (curStepGuides.chats[curStepIndex] != "blank")
						 {
							 showChats(curStepGuides.chats[curStepIndex]);
						 }	
					 }
					 else
					 {

                         EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, curStepGuides.eventType[curStepIndex]));
					 }

				}
			}
			//判断是否当前操作里的一个步骤
			if (!curStepGuides) 
			{
				EventManager.getInstance().removeEventListener(ActionStepEvent.ACTION_HAPPEN, actionHappen);
				return;
			}
			if (curStepGuides.eventType[curStepIndex]==e.actType) 
			{
				//进入下一步
				nextStep(e.actType);
				return;
			}
			
			//如果不是当前行为的事件,再判断下是否是别的步骤的最终操作
			var tmp:Array = DataManager.getInstance().guides;
		    var tmpvo:GuidesVo;
			for (i = 0; i < tmp.length; i++) 
			{
				tmpvo = tmp[i] as GuidesVo;
				if (tmpvo.endStepEvent==e.actType && tmpvo.state==GuidesState.UNFINISH) 
				{
					//提交完成行为
					finishStep(tmpvo.gid);
					//修改该行为状态
					tmpvo.state = GuidesState.FINISHED;
					//如果是当前行为的最后一步,就直接结束进入下一行为
					if (tmpvo.gid==curStepGuides.gid) 
					{
						start();
					}
				}
			}
		}
		
		private function nextStep(curtype:String):void
		{
			//判断当前行为是否全部结束
			if (curtype==curStepGuides.endStepEvent) 
			{
				if (curStepGuides.chats[curStepIndex + 1]) 
				{
                        if (curStepGuides.index == 5 || curStepGuides.index == 6)
						{
							
						 chatMsgView.lastbool = true;
						}
										   
					    //显示下一句对话
					    showChats(curStepGuides.chats[curStepIndex + 1]);
					

					if (curStepGuides.actTips[curStepIndex + 1] != "blank")
					{
						showActTips(curStepGuides.actTips[curStepIndex + 1]);
					}
					
				}
				else
				{
					chatMsgView.clickend();
				}
				//把乐乐消失掉

				flashplaystar();
				//进入下一个行为
				curStepGuides.state = GuidesState.FINISHED;
				var tmppp:Array = DataManager.getInstance().guides;
				//提交完成行为
				finishStep(curStepGuides.gid);
				start();
			}else {
				//step加1
				curStepIndex++;
			
				//显示下一句对话
				showChats(curStepGuides.chats[curStepIndex]);
				
				//判断下一步的eventtype
				if (curStepGuides.eventType[curStepIndex]=="blank") 
				{
					//如果是空,就进入下一步
					nextStep("blank");
				}
			}
			
		}
		
		private function finishStep(gid:uint):void
		{
			var command:SaveGuidesCommand = new SaveGuidesCommand();
			command.addEventListener(Event.COMPLETE, finishStep_complete);
			command.save(gid);
			
			listView.finishStep(gid);
		}
		
		private function finishStep_complete(e:Event):void 
		{
			
		}
		
		
		
		public function start():void {
			curStepGuides = DataManager.getInstance().getCurGuides();

			if (!curStepGuides) 
			{
				//所有步骤完成,关闭control
				EventManager.getInstance().addEventListener(ActionStepEvent.ACTION_HAPPEN, actionHappen);
				
				return;
			}
			
			curStepIndex = 0;
			showChats(curStepGuides.chats[curStepIndex]);
			
		}
		
		private function showChats(str:String):void {
			ActTipsManager.getInstance().hideActTips();
			if (str=="blank") 
			{
				curStepGuides = DataManager.getInstance().getCurGuides();
				
				showActTips(curStepGuides.actTips[curStepIndex]);
			}else {
				teacherface = CutString(str);
				str = returnCutEndNewString(str);
				if (DisplayManager.uiSprite.getModule("sysChatsMsgView"))
				{
					DisplayManager.uiSprite.showModule("sysChatsMsgView");
				}
				else
				{
				  chatMsgView = DisplayManager.uiSprite.addModuleByVo(modeulevo) as SysChatsMsgView;
			      chatMsgView.addEventListener(Event.COMPLETE, chats_complete);
				}

                   chatMsgView.setData(str.split("||"),teacherface);

			}
		}
		
		private function showActTips(tipsType:String):void {
			ActTipsManager.getInstance().hideActTips();
			
			var world:MagicWorld = DataManager.getInstance().getVar("magicWorld") as MagicWorld;
			var p:Point = new Point();
			var temp:DisplayObjectContainer;
			switch (tipsType) 
			{
				case ActTipsType.clickDoor:
					var door:Door = world.getReadyDoor();
					if (door) 
					{
						if (door.mirror==1) 
						{
							p = new Point( -15, -25);
						}else {
							p = new Point( 15,  -25);
						}
						//将本地坐标转换进世界坐标
						p = door.view.container.localToGlobal(p);
						//将世界坐标转换到本地坐标
						p = door.view.container.parent.globalToLocal(p);
						ActTipsManager.getInstance().showActTips(p, 0, door.view.container.parent);
					}
				break;
				
				case ActTipsType.clickStudentToTeach:
					var student:Student = world.getNeedTeachStudent();
					if (student) 
					{
						p = new Point(0,-15);
						ActTipsManager.getInstance().showActTips(p, 0, student.view.container);
					}
				break;

				case ActTipsType.clickStudentTakeEvent:
					var student2:Student = world.getEventStudent();
					if (student2) 
					{
						p = new Point(0,-15);
						ActTipsManager.getInstance().showActTips(p, 0, student2.view.container);
					}
				break;
				
				case ActTipsType.clickDeskCrystal:
					var desk:Desk = world.getHaveCrystalDesk();
					if (desk) 
					{
						p = new Point(0,-10);
						ActTipsManager.getInstance().showActTips(p, 0, desk.view.container);
					}
				break;
				
				case ActTipsType.clickPlayerToTrans:
					var player:Player = world.player;
					p = new Point(0, -28);
					if (player)
					{
					ActTipsManager.getInstance().showActTips(p, 0, player.view.container);						
					}

				break; 
				
		
				case ActTipsType.clickFinishGuide:
					p = new Point(70,225);
					ActTipsManager.getInstance().showActTips(p, 0, DisplayManager.uiSprite.getModule("guidesView").view);
					ActTipsManager.getInstance().SetHaloScaleXY(0.8, 0.8);
				break; 

				case ActTipsType.clickMenuConjure:
					p = new Point( -306, 30);
					ActTipsManager.getInstance().showActTips(p, 0, DisplayManager.uiSprite.getModule("menu").view);
					ActTipsManager.getInstance().SetHaloScaleXY(0.6, 0.6);
					DisplayManager.uiSprite.showModule(ModuleDict.MODULE_FRIENDS);
					DisplayManager.uiSprite.showModule(ModuleDict.MODULE_MAINMENU);
				break;
				
				case ActTipsType.clickChangeArt:
					p = new Point(125,35);
					ActTipsManager.getInstance().showActTips(p, 0, DisplayManager.uiSprite.getModule(ModuleDict.MODULE_USEMAGIC_LIST).view);
					ActTipsManager.getInstance().SetHaloScaleXY(1, 1);
				break
				
				case ActTipsType.clickTeachMagic:
					p = new Point(0,145);
					ActTipsManager.getInstance().showActTips(p, 0, DisplayManager.uiSprite.getModule(ModuleDict.MODULE_MAGICCLASS).view);
				break;
				
				case ActTipsType.clickMenuMix:
					p = new Point(-260,30);
					ActTipsManager.getInstance().showActTips(p, 0, DisplayManager.uiSprite.getModule("menu").view);
					ActTipsManager.getInstance().SetHaloScaleXY(0.6, 0.6);
					DisplayManager.uiSprite.showModule(ModuleDict.MODULE_FRIENDS);
					DisplayManager.uiSprite.showModule(ModuleDict.MODULE_MAINMENU);					
				break;
				
				case ActTipsType.clickMixItem:
					p = new Point(-30,-150);
					ActTipsManager.getInstance().showActTips(p, 0, DisplayManager.uiSprite.getModule(ModuleDict.MODULE_COMPOUNDTOTAL).view);
					ActTipsManager.getInstance().SetHaloScaleXY(1, 1);
				break;
				
				case ActTipsType.clickMixButton:
					p = new Point(50,180);
					ActTipsManager.getInstance().showActTips(p, 0, DisplayManager.uiSprite.getModule(ModuleDict.MODULE_COMPOUNDTOTAL).view);
					ActTipsManager.getInstance().SetHaloScaleXY(1, 1);
				break;
				
				//-------------------------------------------------------------------------
				
				case ActTipsType.closeTransButton:
				DisplayManager.uiSprite.showModule(ModuleDict.MODULE_FRIENDS);
				DisplayManager.uiSprite.showModule(ModuleDict.MODULE_MAINMENU);
				ModuleManager.getInstance().closeModule("magicItemList", true);
				break;
				
				case ActTipsType.closeMixButton:
				ModuleManager.getInstance().closeModule("MixMagicView", true);
				break;
			}
			
			
		}
		
		//截取一段字符串（参数1：原字符串 参数2：截取头字符 参数3：截取后字符）
		private function CutString(original:String,backstr:String = "<",behindstr:String = ">"):String
		{
			var backnum:int = 0;
			var backboolean:Boolean = true;
			var behindboolean:Boolean = true;
			var behindnum:int = 0;
			for (var i:int = 0; i < original.length; i++ )
			{
				if (original.charAt(i) ==backstr)
				{
					backnum = i;
					backnum += 1;
					backboolean = false;
				}
				if (original.charAt(i) ==behindstr)
				{
					behindnum = i;
					behindboolean = false;
				}
			}
			if (!backboolean && !behindboolean)
			{
				return original.substring(backnum,behindnum);
			}
		   return original;
		}
		
		//返回被截取后剩余的字符串（参数1：原字符串 参数2：截取头字符 参数3：截取后字符）
		private function returnCutEndNewString(original:String,backstr:String = "<",behindstr:String = ">"):String
		{
			var backboolean:Boolean = true;
			var behindboolean:Boolean = true;
			var behindnum:int = 0;
			for (var i:int = 0; i < original.length; i++ )
			{
				if (original.charAt(i) ==backstr)
				{
					backboolean = false;
				}
				if (original.charAt(i) ==behindstr)
				{
					behindnum = i;
					behindnum += 1;
					behindboolean = false;
				}
			}
			if (!backboolean && !behindboolean)
			{
				return original.substring(behindnum,original.length);
			}
		   return original;			
		}
		
		//播放任务奖励的效果
		private function flashplaystar():void
		{
			//1，动画名字 2，放在那个容器里,,,第5参数是回调函数
			var flashMv:McShower = new McShower(starmove, DisplayManager.uiSprite, null, null,taskUIShine);
			var p:Point = new Point(400, 260);
			flashMv.setMcScaleXY(1.0, 1.0);
			//p = chatMsgView.view.parent.localToGlobal(p);
			flashMv.x = p.x;
			flashMv.y = p.y;
		}
		
		//任务面板发亮的效果
		private function taskUIShine():void
		{
			TweenMax.to(listView.view, 0.3, {x:listView.view.x, y:listView.view.y, tint:0xffffff, yoyo:true, repeat:1} );
		}
		
		//场景切换时候的消息响应
		private function hideguide(e:SceneEvent):void
		{
			if (chatMsgView)
			{
			    chatMsgView.closeMe();	
			}

			if (DisplayManager.uiSprite.getModule("guidesView"))
			{
				DisplayManager.uiSprite.closeModule("guidesView");
			}
			
            ActTipsManager.getInstance().visible = false;


		}
		
		//场景切换完成以后的消息响应
		private function SceneChangeComplete(e:SceneEvent):void
		{
			if (!DataManager.getInstance().isSelfScene)
			{
				return;
			}
			if (DisplayManager.uiSprite.getModule("guidesView"))
			{
				DisplayManager.uiSprite.showModule("guidesView");
			}
			if (chatMsgView)
			{
			    DisplayManager.uiSprite.showModule("sysChatsMsgView");	
			}
			ActTipsManager.getInstance().visible = true;
			
		}
		
	}

}