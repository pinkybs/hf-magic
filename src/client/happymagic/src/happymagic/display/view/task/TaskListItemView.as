package happymagic.display.view.task 
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.Tooltips;
	import happyfish.display.view.IconView;
	import happyfish.task.vo.TaskState;
	import happyfish.utils.display.ItemOverControl;
	import happymagic.model.vo.TaskVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class TaskListItemView extends GridItem
	{
		private var stateIcon:taskStateIcon;
		public var data:TaskVo;
		private var icon:IconView;
		
		public function TaskListItemView(uiview:MovieClip) 
		{
			super(uiview);
			
			stateIcon = new taskStateIcon();
			stateIcon.x = 20;
			stateIcon.y = 7;
			uiview.addChild(stateIcon);
			
			ItemOverControl.getInstance().addOverItem(uiview, ItemOverControl.getInstance().showGlow, ItemOverControl.getInstance().hideGlow,true);
			
			
		}
		
		override public function setData(value:Object):void 
		{
			data = value as TaskVo;
			
			icon = new IconView(0, 0);
			icon.setData(value.icon_class);
			view.addChildAt(icon,0);
			
			stateIcon.gotoAndStop(data.state);
			
			Tooltips.getInstance().register(view, data.content, Tooltips.getInstance().getBg("defaultBg"));
		}
		
		public function finishMv():void
		{
			data.state = TaskState.CAN_FINISH;
			stateIcon.gotoAndStop(data.state);
		}
		
		public function backToActived():void
		{
			data.state = TaskState.ACTIVED;
			stateIcon.gotoAndStop(data.state);
		}
		
	}

}