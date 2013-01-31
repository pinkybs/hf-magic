package happymagic.display.view.magicBook 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.GridPage;
	
	/**
	 * ...
	 * @author jj
	 */
	public class MixMagicListView extends DefaultListView
	{
		
		public function MixMagicListView(uiview:MovieClip, _container:DisplayObjectContainer, _pageLength:uint = 5, hidebotton:Boolean = false, _autoAlginButton:Boolean = true) 
		{
			super(uiview as MovieClip, _container, _pageLength, hidebotton, _autoAlginButton);	
		}			
		
	}

}