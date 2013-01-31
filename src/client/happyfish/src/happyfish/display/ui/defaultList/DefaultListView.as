package happyfish.display.ui.defaultList 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.GridPage;
	
	/**
	 * ...
	 * @author jj
	 */
	public class DefaultListView extends GridPage
	{
		public var autoAlginButton:Boolean;
		protected var itemClass:Class;
		protected var itemUiClass:Class;
		
		public function DefaultListView(uiview:MovieClip,_container:DisplayObjectContainer,_pageLength:uint,_hideButton:Boolean=false,_autoAlginButton:Boolean=true) 
		{
			super(uiview as MovieClip, _container);
			hideButtonFlag = _hideButton;
			autoAlginButton = _autoAlginButton;
			pageLength = _pageLength;
			
		}
		
		public function setButtonPosition(leftX:int,leftY:int,rightX:int,rightY:int):void {
			prevBtn.x = leftX;
			prevBtn.y = leftY;
			
			nextBtn.x = rightX;
			nextBtn.y = rightY;
		}
		
		override public function init(gridWidth:Number, gridHeight:Number, tileWidth:Number, tileHeight:Number, gridX:Number = 0, gridY:Number = 0, tileAlgin:String = "TL", algin:String = "TL"):void 
		{
			super.init(gridWidth, gridHeight, tileWidth, tileHeight, gridX, gridY, tileAlgin, algin);
			
			//设置右边按钮到列表最右侧
			if (autoAlginButton) 
			{
				if(nextBtn) nextBtn.x = gridX + gridWidth + 2;
			}
			
		}
		
		public function setGridItem(_itemClass:Class,_itemUiClass:Class=null):void {
			itemClass = _itemClass;
			itemUiClass = _itemUiClass;
		}
		
		override protected function createItem(value:Object):GridItem 
		{
			var tmp:GridItem;
			
			if (itemUiClass==null) 
			{
				tmp = new itemClass() as GridItem;
			}else {
				tmp = new itemClass(new itemUiClass()) as GridItem;
			}
			
			tmp.setData(value);
			
			return tmp;
		}
		
		//去第几页
		public function gopageLength(_num:uint):void
		{
			currentPage = _num;
			initPage();

		}
		
	}

}