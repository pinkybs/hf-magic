package happymagic.actModule.signAward.View 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.GridPage;
	/**
	 * ...
	 * @author ZC
	 */
	public class SignAwardListView extends GridPage
	{
		
		public function SignAwardListView(uiview:MovieClip,_container:DisplayObjectContainer,_pageLength:uint=5) 
		{
			super(uiview as MovieClip, _container,true);
			pageLength = _pageLength;
		}

		override protected function createItem(value:Object):GridItem 
		{
			var tmp:GridItem;
			
			tmp = new SignAwardItemView(new signawarditemview());
			tmp.setData(value);
			
			return tmp;
		}		
	}

}