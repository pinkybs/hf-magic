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
	public class ItemBoxListView extends GridPage
	{
		
		public function ItemBoxListView(uiview:MovieClip,_container:DisplayObjectContainer) 
		{
			super(uiview as MovieClip, _container);
			
			uiview.x = 30;
			uiview.y = 18;
			
			pageLength = 5;

			init(500, 130, 85, 130,68,-10);
		}
		
		override protected function createItem(value:Object):GridItem 
		{
			var tmp:GridItem;
			
			tmp = new ItemBoxItemView(new itemBoxItemUi());
			tmp.setData(value);
			
			return tmp;
		}
		
		public function setXY(_Px:int,_Py:int,_Nx:int,_Ny:int):void
		{
			prevBtn.x = _Px;
			prevBtn.y = _Py
			nextBtn.x = _Nx;
			nextBtn.y = _Ny;
		}
		
	}

}