package happyfish.actModule.giftGetAct.view.current 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.ui.GridItem;
	/**
	 * ...
	 * @author ZC
	 */
	public class CurrentListView extends DefaultListView
	{
		private var state:uint;
		public function CurrentListView(uiview:MovieClip, _container:DisplayObjectContainer, _pageLength:uint = 5, hidebotton:Boolean = false, _autoAlginButton:Boolean = true, _state :uint = 0 ) 
		{
			super(uiview as MovieClip, _container, _pageLength, hidebotton, _autoAlginButton);	
			state = _state;
		}
		
		
		override protected function createItem(value:Object):GridItem 
		{
			var tmp:GridItem;
			
			if (itemUiClass==null) 
			{
				tmp = new itemClass() as GridItem;
			}else {
				tmp = new itemClass(new itemUiClass(),state) as GridItem;
			}
			
			tmp.setData(value);
			
			return tmp;
		}
	}

}