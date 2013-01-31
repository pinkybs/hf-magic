package happyfish.guides.view 
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.UISprite;
	import happyfish.guides.view.GuidesListView;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.ModuleManager;
	import happyfish.model.command.SaveGuidesCommand;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.display.view.task.TaskListView;
	import happymagic.display.view.ui.AwardResultView;
	import happymagic.events.ActionStepEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class GuidesView extends UISprite
	{
		private var iview:guidesUi;
		private var list:GuidesListView;
		private var data:Array;
		
		public function GuidesView() 
		{
			super();
			_view = new guidesUi();
			iview = _view as guidesUi;
			
			list = new GuidesListView(new MovieClip(), iview);
			
			list.init(150, 150, 150, 20, 0, -20);
			iview.addEventListener(MouseEvent.CLICK, clickFun,true);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case iview.guidesFinishBtn:
					if (DataManager.getInstance().isGuidesAllComplete) 
					{
						finishGuides();
			            iview.removeEventListener(MouseEvent.CLICK, clickFun,true);						
					}else {
						EventManager.getInstance().showPiaoStr(PiaoMsgType.TYPE_BAD_STRING,
							LocaleWords.getInstance().getWord("cantFinishGuides"));
					}
				break;
			}
		}
		
		public function finishStep(gid:uint):void {
			var stepItem:GuidesListItemView = list.getItemByKey("gid", gid) as GuidesListItemView;
			if (stepItem) 
			{
				stepItem.finish();
			}
		}
		
		private function finishGuides():void
		{
			var command:SaveGuidesCommand = new SaveGuidesCommand();
			command.addEventListener(Event.COMPLETE, finishGuides_complete);
			command.save(DataManager.getInstance().guides[DataManager.getInstance().guides.length-1].gid);
		}
		
		private function finishGuides_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, finishGuides_complete);
			iview.addEventListener(MouseEvent.CLICK, clickFun,true);			
			if (e.target.data.result.isSuccess) 
			{
				//引导事件
				//EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_FINISH_GUIDE));
			    if (DisplayManager.uiSprite.getModule("sysChatsMsgView"))
				{
				  DisplayManager.uiSprite.closeModule("sysChatsMsgView", true);					
				}
				//表现奖励窗口
				var awards:Array = new Array();
				var i:int = 0;				
				if (e.target.data.result)
				{
					if (e.target.data.result.gem)
					{
						awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_GEM, num:e.target.data.result.gem } ));
					}
				}
				
				if (e.target.data.addItem)
				{
					for (i = 0; i < e.target.data.addItem.length; i++) 
					{
						awards.push(new ConditionVo().setData( { type:ConditionType.ITEM, id:e.target.data.addItem[i].i_id, num:e.target.data.addItem[i].num } ));
					}
				}
				
				if (e.target.data.addDecorBag)
				{
					for (i = 0; i < e.target.data.addDecorBag.length; i++) 
					{
						awards.push(new ConditionVo().setData( { type:ConditionType.DECOR, id:e.target.data.addDecorBag[i].d_id, num:e.target.data.addDecorBag[i].num } ));
					}
				}

				var awardwin:AwardResultView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_AWARD_RESULT, ModuleDict.MODULE_AWARD_RESULT_CLASS,true) as AwardResultView;
				awardwin.setData( { name:"新手引导奖励", awards:awards } );
				
			    var tastlistview:TaskListView = ModuleManager.getInstance().getModule(ModuleDict.MODULE_TASKLIST) as TaskListView;
			    tastlistview.openView();				
				
				//关闭面板
				closeMe(true);
								
			}
		}
		
		public function setData(value:Array):void {
			data = value;
			
			list.setData(data);
			list.addEventListener(Event.COMPLETE,_setDatacomplete)
			
		}
		private function _setDatacomplete(e:Event):void
		{
			setCurGuidesBg();
		}
		
		/**
		 * 设置当前引导的背景指示
		 */
		private function setCurGuidesBg():void
		{
			var curIndex:uint = DataManager.getInstance().getCurGuides().index;
			var tmp:GridItem = list.getItemByIndex(curIndex-1);
			if (tmp) 
			{
				var p:Point = new Point(tmp.view.x, tmp.view.y);
				p = tmp.view.parent.localToGlobal(p);
				p = iview.globalToLocal(p);
				iview.guidesCurBg.y = p.y;
				iview.guidesCurBg.visible = true;
			}else {
				iview.guidesCurBg.visible = false;
			}
		}
		
	}

}