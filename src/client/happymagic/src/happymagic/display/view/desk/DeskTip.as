package happymagic.display.view.desk 
{
	import happyfish.display.view.PerBarView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.local.LocaleWords;
	import happyfish.utils.DateTools;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.MagicType;
	import happymagic.model.vo.StudentStateType;
	import happymagic.model.vo.StudentStateVo;
	import happymagic.model.vo.StudentVo;
	
	/**
	 * ...
	 * @author Beck
	 */
	public class DeskTip extends UISprite
	{
		private var _iview:ui_learningtips;
		private var expBar:PerBarView;
		public function DeskTip() 
		{
			super();
			
			this._view = new ui_learningtips();
			this._iview = this._view as ui_learningtips;
			
			expBar = new PerBarView(_iview.expBar, _iview.expBar.width);
			expBar.minW = 2;
			expBar.tweenTime = 0;
			
			this._iview.study_status.gotoAndStop(1);
			
			DisplayManager.deskTip = this;
		}
		
		public function set data($data:Object):void
		{
			//this._iview.npc_name.text = $data.name;
			this._iview.crystal_num.text = $data.coin;
			
			var studentState:StudentStateVo = DataManager.getInstance().getStudentState($data.sid);
			_iview.nameTxt.text = studentState.name;
			_iview.levelTxt.text = "LV:" + studentState.level.toString();
			
			expBar.maxValue = studentState.maxpExp;
			expBar.setData(studentState.exp);
			_iview.expTxt.text = studentState.exp + "/" + studentState.maxpExp;
			
			if ($data.state != StudentStateType.INTERRUPT) {
				this._iview.study_status.gotoAndStop($data.state);
			} else {
				this._iview.study_status.gotoAndStop("interrupt");
			}
			
			this.countdown = $data.time;
		}
		
		public function set countdown($time:int):void
		{
			var time_count:String = DateTools.getLostTime($time * 1000,true,":",":",":","",true);
			this._iview.timing.text = time_count;
		}
		
	}

}