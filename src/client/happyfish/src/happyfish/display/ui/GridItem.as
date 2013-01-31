package happyfish.display.ui 
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import happyfish.display.ui.events.GridPageEvent;
	/**
	 * 表格列表item项基类
	 * @author jj
	 */
	public class GridItem
	{
		public var view:MovieClip;
		public var index:uint;
		public function GridItem(uiview:MovieClip) 
		{
			view = uiview;
			view.control = this;
			view.mouseChildren = false;
			view.buttonMode = true;
			view.addEventListener(MouseEvent.CLICK, itemSelectFun);
		}
		
		protected function itemSelectFun(e:MouseEvent):void 
		{
			var event:GridPageEvent = new GridPageEvent(GridPageEvent.ITEM_SELECT);
			event.item = this;
			view.dispatchEvent(event);
		}
		
		public function setData(value:Object):void {
			throw("need override");
		}
		
		public function add(value:Object):void
		{
			
		}
		
		public function delNum(num:uint):Array
		{
			return null;
		}
		
	}

}