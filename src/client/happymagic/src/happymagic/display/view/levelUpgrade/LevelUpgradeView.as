package happymagic.display.view.levelUpgrade 
{
	import flash.events.MouseEvent;
	import happyfish.display.view.UISprite;
	import happyfish.feed.Command.FeedControlCommond;
	import happyfish.feed.FeedType;
	import happyfish.manager.SoundEffectManager;
	import happymagic.display.view.task.TaskNeedItemListView;
	import happymagic.manager.DataManager;
	import happymagic.model.command.ShareFeedCommand;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.ItemClassVo;
	import happymagic.model.vo.LevelInfoVo;
	import happymagic.model.vo.MagicClassVo;
	import happymagic.model.vo.MixMagicVo;
	import happymagic.model.vo.TransMagicVo;
	import xrope.LayoutAlign;
	
	/**
	 * ...
	 * @author jj
	 */
	public class LevelUpgradeView extends UISprite
	{
		private var unlockList:TaskNeedItemListView;
		private var data:LevelInfoVo;
		private var awardList:TaskNeedItemListView;
		private var iview:LevelPlayerUi;
		public static const PlayerLevelD:uint = 0;//0是当前升级面板
		public static const PlayerLevelN:uint = 1;//1是下一级升级面板信息	
		public function LevelUpgradeView() 
		{
			super();
			_view = new LevelPlayerUi();
			
			iview = _view as LevelPlayerUi;
			
			iview.addEventListener(MouseEvent.CLICK, clickFun, true);
			
			unlockList = new TaskNeedItemListView(new conditionListUi(), _view);
			unlockList.pageLength = 4;
			unlockList.x = -139;
			unlockList.y = -28;
			unlockList.init(300, 73, 70, 80, 0, -40, LayoutAlign.LEFT);
			
			awardList = new TaskNeedItemListView(new conditionListUi(), _view);
			awardList.pageLength = 4;
			awardList.x = -139;
			awardList.y = 75;
			awardList.init(300, 73, 70, 80, 0, -40, LayoutAlign.LEFT);
			iview.LevelNum.mouseEnabled = false;
			iview.fenxiang.visible = false;
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case iview.closeBtn:
				case iview.closeBtn1:
				closeMe(true);
				break;
				
				case iview.fenxiang: 
				//分享+100按钮效果
				closeMe(true);
				break;
				
				case iview.gouButton:
				//显示下一级的面板中的打勾按钮处理
				closeMe(true);
				break;
			}
		}
		
		public function setData(value:LevelInfoVo, state:uint):void {
			
			switch(state)
			{
				case PlayerLevelD:
				iview.biaoti2.visible = false;
				iview.biaoti22.visible = false;
	            iview.gouButton.visible = false;
				iview.QuestionTips.visible = false;
				iview.lightBeam.mouseChildren = false;
				iview.lightBeam.mouseEnabled = false;
				iview.whitelight.mouseChildren = false;
				iview.whitelight.mouseEnabled = false;
				iview.LevelNum.text = String(DataManager.getInstance().currentUser.level);
				//音效
			    SoundEffectManager.getInstance().playSound(new sound_lvup());
				
				var feedControlCommond:FeedControlCommond = new FeedControlCommond();
				if (feedControlCommond.isExist(FeedType.LEVELUPFEED))
				{
					iview.fenxiang.visible = true;
					feedControlCommond.init(iview.fenxiang);
				}
				
				iview.fenxiang.visible = true;
				break;
				
				case PlayerLevelN:				
					iview.stardiandian.visible = false;
					iview.lightBeam.visible = false;
					iview.bunchStar.visible = false;
					iview.biaoti1.visible = false;
					iview.whitelight.visible = false;
					iview.biaotibiankuang.visible = false;
					iview.yinying.visible = false;
					iview.LevelNum.text = String(DataManager.getInstance().currentUser.level + 1);
					iview.closeBtn1.visible = false;
					iview.fenxiang.visible = false;
					iview.Dbackground.visible = false;
					iview.star1.visible = false;
					iview.star2.visible = false;
					
					if (DataManager.getInstance().currentUser.level == 76)
					{
						iview.biaoti22.DExpTxt.text = String(DataManager.getInstance().currentUser.exp);
						iview.biaoti22.NExpTxt.text = String("MAX");							
					}
					else
					{
						var nextlevel:LevelInfoVo = DataManager.getInstance().getLevelInfo(DataManager.getInstance().currentUser.level + 1);		
						iview.biaoti22.DExpTxt.text = String(DataManager.getInstance().currentUser.exp);
						iview.biaoti22.NExpTxt.text = String(nextlevel.max_exp);						
					}
					
					
				break;
				
			}
			data = value;
			
			unlockList.clear();
			awardList.clear();
			

			var i:int;
			//解锁内容列表
			var tmparr:Array=DataManager.getInstance().getUnlockMagicClass(data.level).concat(
					DataManager.getInstance().getUnlockTrans(data.level));
					
			var unlocks:Array = new Array();
			for (i = 0; i < tmparr.length; i++) 
			{
				if (tmparr[i] is MagicClassVo) 
				{
					unlocks.push(new ConditionVo().setData( { type:ConditionType.MAGIC_CLASS, id:tmparr[i].magic_id } ));
				}
				else if (tmparr[i] is TransMagicVo) {
					unlocks.push(new ConditionVo().setData( { type:ConditionType.TRANS, id:tmparr[i].trans_mid } ));
				}
			}
			
			
			var tmpcondition:ConditionVo;
			//var sceneAdd:int = data.tile_x_length - DataManager.getInstance().getLevelInfo(data.level - 1).tile_x_length;
			//if (sceneAdd>0) 
			//{
				//tmpcondition = new ConditionVo().setData( { type:ConditionType.SCENE_UPGRADE, id:"", num:sceneAdd } ) as ConditionVo;
				//unlocks.push(tmpcondition);
			//}
			
			//学生人数			
			//var studentAdd:int = data.student_limit - DataManager.getInstance().getLevelInfo(data.level - 1).student_limit;
			//if (studentAdd>0) 
			//{
				//tmpcondition = new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_STUDENT_LIMIT, num:studentAdd } ) as ConditionVo;
				//unlocks.push(tmpcondition);
			//}
			
			//魔法最大值
			var mpAdd:int=data.magic_limit-DataManager.getInstance().getLevelInfo(data.level-1).magic_limit;
			if (mpAdd>0) 
			{
				tmpcondition = new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_MP_MAX, num:mpAdd } ) as ConditionVo;
				unlocks.push(tmpcondition);
			}
			//课桌数
			//var deskAdd:int=data.desk_limit-DataManager.getInstance().getLevelInfo(data.level-1).desk_limit;
			//if (deskAdd>0) 
			//{
				//tmpcondition = new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_DESK_LIMIT, num:deskAdd } ) as ConditionVo;
				//unlocks.push(tmpcondition);
			//}
			
			unlockList.setData(unlocks);
			
			
			//奖励物品列表
			var awards:Array = new Array();
			
			//游戏币
			if (data.coin) 
			{
				tmpcondition = new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_COIN, num:data.coin } ) as ConditionVo;
				awards.push(tmpcondition);
			}
			
			//钻石
			if (data.gem) 
			{
				tmpcondition = new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_GEM, num:data.gem } ) as ConditionVo;
				awards.push(tmpcondition);
			}
			
			//道具
			if (data.items) 
			{
				for (i = 0; i < data.items.length; i++) 
				{
					tmpcondition = new ConditionVo().setData( { type:ConditionType.ITEM, id:data.items[i][0], num:data.items[i][1] } ) as ConditionVo;
					awards.push(tmpcondition);
				}
			}
				
			awardList.setData(awards);
			

		}
		
	}

}