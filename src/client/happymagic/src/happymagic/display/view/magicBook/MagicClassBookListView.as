package happymagic.display.view.magicBook 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.GridPage;
	
	/**
	 * ...
	 * @author jj
	 */
	public class MagicClassBookListView extends GridPage
	{
		
		public function MagicClassBookListView(uiview:MovieClip,_container:DisplayObjectContainer) 
		{
			super(uiview, _container);
			
			pageLength = 12;
		}
		
		override protected function createItem(value:Object):GridItem 
		{
			var tmp:GridItem;
			
			tmp = new MagicClassBookItemView(new magicClassItemUi());
			tmp.setData(value);
			
			return tmp;
		}
		
	}

}