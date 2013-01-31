package happymagic.display.view.diary 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.GridPage;
	
	/**
	 * ...
	 * @author jj
	 */
	public class DiaryListView extends GridPage
	{
		
		public function DiaryListView(uiview:MovieClip,_container:DisplayObjectContainer) 
		{
			super(uiview as MovieClip, _container,false);
			
			uiview.x = -190;
			uiview.y = -130;
			
			pageLength = 5;
			
			init(460, 260, 460,60);
		}
		
		override protected function createItem(value:Object):GridItem 
		{
			var tmp:GridItem;
			
			tmp = new DiaryListItemView(new DailyRecordListItem());
			tmp.setData(value);
			
			return tmp;
		}
		
	}

}