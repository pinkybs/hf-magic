package happymagic.model.control 
{
	import flash.geom.Point;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.AlginType;
	import happymagic.display.view.levelUpgrade.LevelUpgradeView;
	import happymagic.display.view.maxMp.MaxMpView;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.events.DataManagerEvent;
	import happymagic.events.MainInfoEvent;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.events.UserInfoChangeVo;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.LevelInfoVo;
	import happymagic.model.vo.ResultVo;
	import happymagic.model.vo.StudentStateVo;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.MagicWorld;
	
	/**
	 * ...
	 * @author jj
	 */
	public class TakeResultVoControl 
	{
		
		public function TakeResultVoControl(access:Private) 
		{
			
			if (access != null)
			{	
				if (instance == null)
				{				
					instance = this;
				}
			}
			else
			{	
				throw new Error( "TakeResultVoControl"+"单例" );
			}
		}
		
		/**
		 * 处理resultVo
		 * @param	value
		 * @param	piao	是否用飘屏显示错误信息
		 */
		public function take(value:ResultVo,piao:Boolean=false,piaoPoint:Point=null):void {
			
			//显示加减水晶等信息表现
			var infoChange:UserInfoChangeVo = new UserInfoChangeVo();
			infoChange.turnFromResultVo(value);
			
			//tmp
			//infoChange.levelUp = true;
			
			//显示升级面板
			if (infoChange.levelUp) 
			{
				var tmpuser:UserVo = DataManager.getInstance().currentUser;
				tmpuser.level++;
				
				var nextlevel:LevelInfoVo = DataManager.getInstance().getLevelInfo(tmpuser.level + 1);
				var tmplevel:LevelInfoVo = DataManager.getInstance().getLevelInfo(tmpuser.level);
				
				tmpuser.max_exp = nextlevel.max_exp;
				//tmpuser.max_mp = tmplevel.magic_limit + (DataManager.getInstance().worldState.world as MagicWorld).getAddMaxMp();
				tmpuser.max_mp += 10;
				tmpuser.mp = tmpuser.max_mp;
				infoChange.mp = 0;
				
				DataManager.getInstance().curSceneUser = tmpuser;
				if (DataManager.getInstance().isDiying) 
				{
					DisplayManager.uiSprite.getModule(ModuleDict.MODULE_MAININFO)["diyingUserLevelUp"]();
				}
				
				var levelInfoView:LevelUpgradeView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_LEVELINFO, ModuleDict.MODULE_LEVELINFO_CLASS,true,AlginType.CENTER,30,-50) as LevelUpgradeView;
				levelInfoView.setData(DataManager.getInstance().getLevelInfo(tmpuser.level), 0);
				DisplayManager.uiSprite.setBg(levelInfoView);
			}
			
			//显示房间升级面板
			if (infoChange.roomLevelUp) 
			{	
				//修改当前用户房间等级
				var tmpCurUser:UserVo = DataManager.getInstance().currentUser;
				tmpCurUser.roomLevel+=infoChange.roomLevelUp;
				
				//修改当前最大学生数和最大课桌数
				tmpCurUser.students_limit = DataManager.getInstance().getRoomLevel(tmpCurUser.roomLevel).student_limit;
				tmpCurUser.desk_limit = DataManager.getInstance().getRoomLevel(tmpCurUser.roomLevel).desk_limit;
				
				DataManager.getInstance().curSceneUser = tmpCurUser;
				if (DataManager.getInstance().isDiying) 
				{
					DisplayManager.uiSprite.getModule(ModuleDict.MODULE_MAININFO)["diyingRoomLevelUp"](infoChange.roomLevelUp);
				}
				
				//解锁学生
				for (var i:int = 0; i < DataManager.getInstance().studentStates.length; i++) 
				{
					var item:StudentStateVo = DataManager.getInstance().studentStates[i];
					if (item.unLockMp<=DataManager.getInstance().currentUser.max_mp) 
					{
						item.unLock = 1;
					}
				}
				
				var maxmpview:MaxMpView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_MAXMP, ModuleDict.MODULE_MAXMP_CLASS, true, AlginType.CENTER, 10, -70) as MaxMpView;
				DisplayManager.uiSprite.setBg(maxmpview);
			    maxmpview.setData(DataManager.getInstance().getRoomLevel(tmpCurUser.roomLevel-infoChange.roomLevelUp), MaxMpView.MAXMPLEVELUP,infoChange.roomLevelUp);
				
			}
			
			infoChange.piao = piao;
			infoChange.showPoint = piaoPoint;
			if (!infoChange.isEmpty) 
			{
				var e:DataManagerEvent = new DataManagerEvent(DataManagerEvent.USERINFO_CHANGE);
				e.userChange = infoChange;
				EventManager.getInstance().dispatchEvent(e);
			}
			
			//报错信息弹出
			if (value.status!=ResultVo.SUCCESS) 
			{
				if (piao) 
				{
					//漂字
					var msgs_new:Array = [[PiaoMsgType.TYPE_BAD_STRING, value.content]];
					if (!piaoPoint) 
					{
						piaoPoint = new Point(DisplayManager.uiSprite.mouseX, DisplayManager.uiSprite.mouseY);
					}
					var event_piao_msg:PiaoMsgEvent = new PiaoMsgEvent(PiaoMsgEvent.SHOW_PIAO_MSG, msgs_new, piaoPoint.x, piaoPoint.y);
					EventManager.getInstance().dispatchEvent(event_piao_msg);
				}else {
					EventManager.getInstance().showSysMsg(LocaleWords.getInstance().getWord(value.content));
				}
				
			}
		}
		
		public static function getInstance():TakeResultVoControl
		{
			if (instance == null)
			{
				instance = new TakeResultVoControl( new Private() );
			}
			return instance;
		}
		
		
		private static var instance:TakeResultVoControl;
		
	}
	
}
class Private {}