package happyfish.guides.view 
{
	import flash.display.MovieClip;
	import flash.text.TextFormat;
	import happyfish.display.ui.GridItem;
	import happyfish.model.vo.GuidesClassVo;
	import happyfish.model.vo.GuidesState;
	import happyfish.model.vo.GuidesVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class GuidesListItemView extends GridItem
	{
		private var iview:guidesItemUi;
		public var data:GuidesVo;
		
		public function GuidesListItemView(uiview:MovieClip) 
		{
			super(uiview);
			iview = uiview as guidesItemUi;
			
		}
		
		override public function setData(value:Object):void 
		{
			data = value as GuidesVo;
			
			iview.txt.text = data.name;
			initStateIcon();
		}
		
		//判断是否完成 在框里打勾
		private function initStateIcon():void {
			if (data.state==GuidesState.UNFINISH) 
			{
				iview.checkBox.gotoAndStop(1);
			}else {
				iview.checkBox.gotoAndStop(2);
			}
		}
		
		public function finish():void {
			data.state = GuidesState.FINISHED;
			initStateIcon();
		}
		
	}

}