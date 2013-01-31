package happymagic.display.view.rehandling 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.ui.GridItem;
	/**
	 * ...
	 * @author zc
	 */
	public class RehandlingInfoListView extends DefaultListView
	{
		
		public function RehandlingInfoListView(uiview:MovieClip,_container:DisplayObjectContainer,_pageLength:uint,_hideButton:Boolean=false,_autoAlginButton:Boolean=true) 
		{
			super(uiview, _container, _pageLength, _hideButton, _autoAlginButton);
			pageLength = _pageLength;			
		}
	
	}

}