package happymagic.display.view.edit 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.GridPage;
	
	/**
	 * ...
	 * @author jj
	 */
	public class DecorListView extends GridPage
	{
		
		public function DecorListView(uiview:MovieClip,_container:DisplayObjectContainer) 
		{
			super(uiview, _container);
			
			blankItemUi = goMixItemUi;
			
			pageLength = 6;
		}
		
		override protected function createItem(value:Object):GridItem 
		{
			var tmp:GridItem;
			
			tmp = new DecorListItemView(new ui_decor());
			tmp.setData(value);
			
			return tmp;
		}
		
		override protected function createBlankItem():GridItem 
		{
			var tmp:GridItem;
			
			tmp = new GoMixDecorItemView(new goMixItemUi());
			
			return tmp;
		}
		
	}

}