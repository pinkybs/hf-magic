package happymagic.display.view.itembox 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.GridPage;
	
	/**
	 * ...
	 * @author jj
	 */
	public class ItemShopListView extends GridPage
	{
		
		public function ItemShopListView(uiview:MovieClip,_container:DisplayObjectContainer) 
		{
			super(uiview as MovieClip, _container,false);
			
			
			uiview.x = -220;
			uiview.y = -137;
			
			pageLength = 8;
			
			init(500, 190, 110, 140,0,20);
		}
		
		override protected function createItem(value:Object):GridItem 
		{
			var tmp:GridItem;
			
			tmp = new ItemShopItemView(new newshopitemui());
			tmp.setData(value);
			
			return tmp;
		}
		
	}

}