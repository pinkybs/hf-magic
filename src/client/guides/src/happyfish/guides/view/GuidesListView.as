package happyfish.guides.view 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.GridPage;
	
	/**
	 * ...
	 * @author jj
	 */
	public class GuidesListView extends GridPage
	{
		
		public function GuidesListView(uiview:MovieClip,_container:DisplayObjectContainer) 
		{
			super(uiview,_container);
			uiview.x = 14;
			uiview.y = 70;
			
			pageLength = 6;
		}
		
		override protected function createItem(value:Object):GridItem 
		{
			var tmp:GridItem;
			
			tmp = new GuidesListItemView(new guidesItemUi());
			tmp.setData(value);
			
			return tmp;
		}
		
	}

}