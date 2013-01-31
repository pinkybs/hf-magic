package happyfish.display.ui.defaultList 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import happyfish.display.ui.GridPage;
	/**
	 * ...
	 * @author slamjj
	 */
	public class DefaultVLineListView extends DefaultListView
	{
		public function DefaultVLineListView(uiview:MovieClip,_container:DisplayObjectContainer,_pageLength:uint,_hideButton:Boolean=false,_autoAlginButton:Boolean=true) 
		{
			
			super(uiview as MovieClip, _container, _pageLength,_hideButton,_autoAlginButton);
			
			//pageLength = _pageLength;
		}
		
		override public function init(gridWidth:Number, gridHeight:Number, tileWidth:Number, tileHeight:Number, gridX:Number = 0, gridY:Number = 0, tileAlgin:String = "TL", algin:String = "TL"):void 
		{
			super.init(gridWidth, gridHeight, tileWidth, tileHeight, gridX, gridY, tileAlgin, algin);
			
			//设置右边按钮到列表最右侧
			if (autoAlginButton) 
			{
				if (nextBtn) {
					nextBtn.x = 0;
					nextBtn.y = gridY + gridHeight;
				}
			}
			
		}
		
	}

}