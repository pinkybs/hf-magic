package happymagic.display.view.friends 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.GridPage;
	
	/**
	 * ...
	 * @author jj
	 */
	public class FriendsListView extends GridPage
	{
		
		public function FriendsListView(uiview:MovieClip,_container:DisplayObjectContainer) 
		{
			super(uiview as MovieClip, _container);
			
			uiview.x = -318;
			uiview.y = 58;
			
			pageLength = 6;
			
			init(680, 100, 88, 100,0,-47);
		}
		
		override protected function createItem(value:Object):GridItem 
		{
			var tmp:GridItem;
			
			tmp = new FriendsItemView(new friendItemUi());
			tmp.setData(value);
			
			return tmp;
		}
		
	}

}