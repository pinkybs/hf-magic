package happymagic.display.view.task 
{
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.view.IconView;
	import happyfish.display.view.UISprite;
	import happymagic.display.view.task.TaskNeedItemListView;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.TaskVo;
	import xrope.LayoutAlign;
	/**
	 * ...
	 * @author jj
	 */
	public class FinishTaskResultView extends UISprite
	{
		private var iview:TaskFinishView;
		private var data:TaskVo;
		private var awardsMc:TaskNeedItemListView;
		private var npcFace:IconView;
		
		public function FinishTaskResultView() 
		{
			super();
			_view = new TaskFinishView() as MovieClip;
			
			iview = _view as TaskFinishView;
			iview.addEventListener(MouseEvent.CLICK, clickFun, true);
			
			awardsMc = new TaskNeedItemListView(new taskviewlist(),_view);
			awardsMc.x = -152;
			awardsMc.y = 25;

			awardsMc.init(333, 110, 60, 110, 0, -55,LayoutAlign.LEFT);
		}
		
		private function clickFun(e:Event):void 
		{
			switch (e.target) 
			{
				case iview.yesBtn:
				closeMe(true);
				break;
			}
		}
		
		public function setData(value:TaskVo):void {
			data = value;
			
			npcFace=new IconView(116, 116, new Rectangle(105,-136,116,116));
			npcFace.setData(data.icon_class);
			iview.addChild(npcFace);
			
			iview.nameTxt.text = data.name;
			awardsMc.setData(value.awards);
		}
		
		override public function closeMe(del:Boolean=true):void {
			//隐藏自己
			DisplayManager.uiSprite.closeModule(name,del);
		}
	}

}