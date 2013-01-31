package happymagic.display.view.maxMp 
{
	import com.greensock.TweenLite;
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.geom.Point;
	import flash.utils.Timer;
	import happyfish.display.ui.EnergyBarView;
	import happyfish.display.view.PerBarView;
	import happyfish.display.view.UISprite;
	import happyfish.feed.Command.FeedControlCommond;
	import happyfish.feed.FeedType;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.SoundEffectManager;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.student.StudentFruitView;
	import happymagic.display.view.task.TaskNeedItemListView;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.RoomLevelVo;
	import happymagic.model.vo.UserVo;
	import xrope.LayoutAlign;
	/**
	 * ...
	 * @author ZC
	 */
	public class MaxMpView extends UISprite
	{
		private var iview:MaxMpViewUi;
		private var awardList:TaskNeedItemListView;
		private var unlockList:TaskNeedItemListView;
		private var data:RoomLevelVo;
		private var levelnum:uint;
		private var maxUpBar:PerBarView;
		private var viewstate:uint;
		private var num:uint;
		public static const MAXMPLEVELUP :uint = 0;//0是当前升级面板
		public static const MAXMPNEXTLEVELUP:uint = 1;//1是下一级升级面板信息
		private var energyBarView:EnergyBarView;
		
		private var gainRoomLevelVo:RoomLevelVo; //升级时候使用的Vo
		
		
		
		public function MaxMpView() 
		{
			_view = new  MaxMpViewUi();
			iview = _view as MaxMpViewUi;
			
		    unlockList = new TaskNeedItemListView(new conditionListUi(), _view);
			unlockList.pageLength = 4;
			unlockList.x = -100;
			unlockList.y = 30;
			unlockList.init(300, 73, 70, 80, 0, -40, LayoutAlign.LEFT);
			
			awardList = new TaskNeedItemListView(new conditionListUi(), _view);
			awardList.pageLength = 4;
			awardList.x = -100;
			awardList.y = 100;
			awardList.init(300, 73, 70, 80, 0, -40, LayoutAlign.LEFT);
		
			iview.yesBtn.visible = false;
			iview.titlebarLevelUp.visible = false;
			iview.titlebarNextLevelUp.visible = false;
			iview.MaxMpLevelUpWord.visible = false;
			iview.MaxMpNextLevelUpWord.visible = false;
			iview.LevelUpLight.visible = false;
			iview.affirmbtn.visible = false;
			iview.feedbtn.visible = false;
			iview.yesBtn.visible = false;
			iview.levelUpStarLight.visible = false;
			iview.star.visible = false;
			iview.MaxNum.visible = false;
			
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			
			num = 0;
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			var studentfruitview:StudentFruitView;
			var gainRoomLevelVo:RoomLevelVo = DataManager.getInstance().getRoomLevel(data.level + levelnum);
			switch(e.target.name)
			{
				case "yesBtn":
				closeMe(true);
				break;
				
				case "closebn":
				if (viewstate == MAXMPLEVELUP)
				{
				    if (isLockStudent())
				    {
				        studentfruitview = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_STUDENTFRIUT, ModuleDict.MODULE_STUDENTFRIUT_CLASS,false,AlginType.CENTER,0,-100) as StudentFruitView;
					    studentfruitview.setData(DataManager.getInstance().getUnlockStudent(gainRoomLevelVo.needMaxMp));
				    }					
				}
				closeMe(true);
				break;
				
				case "affirmbtn":
				if (isLockStudent())
				{
				        studentfruitview = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_STUDENTFRIUT, ModuleDict.MODULE_STUDENTFRIUT_CLASS,false,AlginType.CENTER,0,-100) as StudentFruitView;
					    studentfruitview.setData(DataManager.getInstance().getUnlockStudent(gainRoomLevelVo.needMaxMp));
				}
				closeMe(true);
				break;
				
				case "feedbtn":
				if (isLockStudent())
				{
				     studentfruitview = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_STUDENTFRIUT, ModuleDict.MODULE_STUDENTFRIUT_CLASS,false,AlginType.CENTER,0,-100) as StudentFruitView;
					 studentfruitview.setData(DataManager.getInstance().getUnlockStudent(gainRoomLevelVo.needMaxMp));
				}
				closeMe(true);
				break;
			}
		}
		
		public function setData(Rvo:RoomLevelVo, _state:uint, _levelnum:uint = 1 ):void
		{
			var itemDistance:Number;
		    unlockList.clear();
			awardList.clear();
			data = Rvo;
			levelnum = _levelnum;
            viewstate = _state;
			
			//maxUpBar = new PerBarView(iview.magicBar, 355);			
			//maxUpBar.minW = 0;
			//maxUpBar.maxValue = data.needMaxMp;
			var point:Point = new Point(-205,-141);
            energyBarView = new EnergyBarView(iview, new ExpBarUi(), point,355);
			switch(viewstate)
			{
				case MAXMPLEVELUP:
					iview.titlebarLevelUp.visible = true;
					iview.affirmbtn.visible = true;
					
					var feedControlCommond:FeedControlCommond = new FeedControlCommond();
					if (feedControlCommond.isExist(FeedType.MAXMPUPFEED))
					{
			           iview.feedbtn.visible = true;
					   feedControlCommond.init(iview.feedbtn);
					}
					
					iview.feedbtn.visible = true;
					iview.MaxMpLevelUpWord.visible = true;
					//maxUpBar.setData(data.needMaxMp);
					gainRoomLevelVo = DataManager.getInstance().getRoomLevel(data.level + levelnum);
					itemDistance = 355;
					energyBarView.setData(gainRoomLevelVo.needMaxMp, gainRoomLevelVo.needMaxMp, itemDistance, playend);
					//TweenLite.to(iview.moveitem, 1, { x:itemDistance, onComplete:startmovie } );
					//iview.moveitem["currentmp"].text = String(data.needMaxMp);
					//音效k
					iview.MaxMp.text = String(gainRoomLevelVo.needMaxMp);
				    SoundEffectManager.getInstance().playSound(new sound_lvup());					
				break;
				
				case MAXMPNEXTLEVELUP:
					iview.titlebarNextLevelUp.visible = true;
					iview.MaxMpNextLevelUpWord.visible = true;
					iview.yesBtn.visible = true;
					
					//maxUpBar.setData(DataManager.getInstance().currentUser.max_mp);
					itemDistance = DataManager.getInstance().currentUser.max_mp / data.needMaxMp * 355;
					
					if (DataManager.getInstance().currentUser.roomLevel == 20)
					{
					   itemDistance = 355;	
					   energyBarView.setData(DataManager.getInstance().currentUser.max_mp, DataManager.getInstance().currentUser.max_mp, itemDistance, null, true);
					   iview.MaxMpNextLevelUpWord.visible = false;
					   iview.MaxMp.visible = false;
					}
					else
					{
					   energyBarView.setData(data.needMaxMp, DataManager.getInstance().currentUser.max_mp, itemDistance);						
					}
					

					//TweenLite.to(iview.moveitem, 1, { x:itemDistance } );
					//iview.moveitem["currentmp"].text = String(DataManager.getInstance().currentUser.max_mp);
					iview.MaxMp.text = String(data.needMaxMp);
				break;
			}
			
			energyBarView.moveitemx = -9;
			energyBarView.start();
			
			//if (data.level >= 20)
			//{
				//iview.MaxMp.visible = false;;
				//iview.MaxNum.visible = false;
				//return;
			//}
			
			initRoom();

		}
		
		//面板的数据初始化
		private function initRoom():void
		{
			
			//var user:int = DataManager.getInstance().currentUser.roomLevel;
			
			
			var unlocks:Array = new Array();
			var tmpcondition:ConditionVo;
			var i:int = 0; 
			var j:int = 0;
			var k:int = 0;
			var awardsbool:Boolean = false;	//是否已经有这个奖励
			var datatemp:RoomLevelVo; // 临时的Vo
			
			//学生人数----------------------------------------------------------------------------------------------------------------------		
			var studentAdd:int;
			if (viewstate == MAXMPLEVELUP)
			{
				for ( i = levelnum; i > 0; i-- )
				{
                	studentAdd += DataManager.getInstance().getRoomLevel(data.level + i).student_limit  - DataManager.getInstance().getRoomLevel(data.level + i - 1).student_limit;				
				}				
			}
			else
			{
                studentAdd += DataManager.getInstance().getRoomLevel(data.level).student_limit  - DataManager.getInstance().getRoomLevel(data.level - 1).student_limit;					
			}

			if (studentAdd>0) 
			{
				tmpcondition = new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_STUDENT_LIMIT, num:studentAdd } ) as ConditionVo;
				unlocks.push(tmpcondition);
			}		    	
			//-----------------------------------------------------------------------------------------------------------------------------
			
			//课桌数-----------------------------------------------------------------------------------------------------------------------
			var deskAdd:int; 
			if (viewstate == MAXMPLEVELUP)
			{
				for ( i = levelnum; i > 0; i-- )
				{
                	deskAdd += DataManager.getInstance().getRoomLevel(data.level + i ).desk_limit - DataManager.getInstance().getRoomLevel(data.level + i - 1).desk_limit;		
				}				
			}
			else
			{
				deskAdd += DataManager.getInstance().getRoomLevel(data.level).desk_limit - DataManager.getInstance().getRoomLevel(data.level - 1).desk_limit;		
			}
			
			if (deskAdd>0) 
			{
				tmpcondition = new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_DESK_LIMIT, num:deskAdd } ) as ConditionVo;
				unlocks.push(tmpcondition);
			}			
			//-------------------------------------------------------------------------------------------------------------------------------
			
			unlockList.setData(unlocks);
					
			//奖励物品列表
			//道具---------------------------------------------------------------------------------------------------------------------------
			var awards:Array = new Array();
			var items:Array;
			if (viewstate == MAXMPLEVELUP)
			{
				if (data.items) 
				{
					for (k = levelnum; k > 0 ; k-- )
					{
						datatemp = DataManager.getInstance().getRoomLevel(data.level + k );
						items = datatemp.items;
						for (i = 0; i < items.length; i++) 
						{
							tmpcondition = new ConditionVo().setData( { type:ConditionType.ITEM, id:items[i][0], num:items[i][1] } ) as ConditionVo;
							itemsLabel:for (j = 0; j < awards.length; j++ )
							{
								if (awards[j].id == items[j][0])
								{
									awards[j].num += items[i][1];
									awardsbool = true;
									break itemsLabel;
								}
								else
								{
								 
								}
							}
							if (awardsbool)
							{
						    	awards.push(tmpcondition);							
							}
							awardsbool = false;
						}					
					}
				}				
			}
			else
			{
				if (data.items) 
				{
					datatemp = DataManager.getInstance().getRoomLevel(data.level);
					items = datatemp.items;
					for (i = 0; i < items.length; i++) 
					{
						tmpcondition = new ConditionVo().setData( { type:ConditionType.ITEM, id:items[i][0], num:items[i][1] } ) as ConditionVo;
				    	awards.push(tmpcondition);						
					}					
				}

			}
			awardsbool = false;	
            //------------------------------------------------------------------------------------------------------------------------------	
			//装饰物------------------------------------------------------------------------------------------------------------------------
			var decors:Array;
			if (viewstate == MAXMPLEVELUP)
			{	
				if (data.decors) 			
				{
					for (k = levelnum; k > 0; k-- )
					{
						datatemp = DataManager.getInstance().getRoomLevel(data.level + k );
						decors = datatemp.decors;
						for (i = 0; i < decors.length; i++) 
						{
							tmpcondition = new ConditionVo().setData( { type:ConditionType.DECOR, id:decors[i][0], num:decors[i][1] } ) as ConditionVo;
							decorLabel:for (j = 0; j < awards.length; j++ )
							{
								if (awards[j].id == decors[j][0])
								{
									awards[j].num += decors[i][1];
									awardsbool = true;
									break decorLabel;
								}
								else
								{
								 
								}
							}
							if (awardsbool)
							{
						   	 	awards.push(tmpcondition);							
							}
							awardsbool = false;	
						}					
					}
				}
			}
		    else
			{
				if (data.decors) 			
				{
					datatemp = DataManager.getInstance().getRoomLevel(data.level + k );
					decors = datatemp.decors;	
					for (i = 0; i < decors.length; i++) 
					{
						tmpcondition = new ConditionVo().setData( { type:ConditionType.DECOR, id:decors[i][0], num:decors[i][1] } ) as ConditionVo;
						awards.push(tmpcondition);	
					}						
				}			
			}			
			//--------------------------------------------------------------------------------------------------------------------------------			
			//钻石----------------------------------------------------------------------------------------------------------------------------
			var gemnum:int;	
			if (viewstate == MAXMPLEVELUP)
			{	
				if (data.gem) 
				{
			
					for ( i = levelnum; i > 0; i-- )
			    	{
                   		gemnum += DataManager.getInstance().getRoomLevel(data.level + i ).gem;		
			    	}
					tmpcondition = new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_GEM, num:gemnum } ) as ConditionVo;
					awards.push(tmpcondition);
				}				
			}
			else
			{
				if (data.gem) 
				{
                   	gemnum += DataManager.getInstance().getRoomLevel(data.level).gem;		
					tmpcondition = new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_GEM, num:gemnum } ) as ConditionVo;
					awards.push(tmpcondition);
				}					
			}
			//-------------------------------------------------------------------------------------------------------------------------------		
			//游戏币-------------------------------------------------------------------------------------------------------------------------
			var coinnum:int;
			if (viewstate == MAXMPLEVELUP)
			{
				if (data.coin) 
				{			
					for ( i = levelnum; i > 0; i-- )
			    	{
                   		coinnum += DataManager.getInstance().getRoomLevel(data.level + i ).coin;		
			    	}				
					tmpcondition = new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_COIN, num:coinnum } ) as ConditionVo;
					awards.push(tmpcondition);
				}				
			}
			else
			{
				if (data.coin) 
				{				
                   	coinnum += DataManager.getInstance().getRoomLevel(data.level + i ).coin;						
					tmpcondition = new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_COIN, num:coinnum } ) as ConditionVo;
					awards.push(tmpcondition);
				}				
			}		
			//--------------------------------------------------------------------------------------------------------------------------------
			
			awardList.setData(awards);
			
		}
		private function isLockStudent(): Boolean
		{
			//学生人数			
			var tempdata:RoomLevelVo = DataManager.getInstance().getRoomLevel(DataManager.getInstance().currentUser.roomLevel);
			var studentAdd:int = tempdata.student_limit - DataManager.getInstance().getRoomLevel(tempdata.level - 1).student_limit;
			if (studentAdd>0) 
			{
                  return true;
			}
			return false;
		}
		
		private function playend():void
		{
		   energyBarView.iview["moveitem"].visible = false;
		   iview.LevelUpLight.visible = true;
		   iview.MaxMpLevelUpWord.visible = true;
		   iview.levelUpStarLight.visible = true;
		   iview.star.visible = true;	
		   iview.MaxNum.visible = true;
		   
		   iview.addChild(iview.star);
		   iview.addChild(iview.levelUpStarLight);
		   iview.addChild(iview.MaxNum);
		   iview.MaxNum.text = String(gainRoomLevelVo.needMaxMp);
		}
		
	}

}