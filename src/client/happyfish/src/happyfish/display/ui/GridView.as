package happyfish.display.ui 
{
	import com.greensock.TweenLite;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import xrope.GridLayout;
	
	/**
	 * 表格显示基类
	 * @author jj
	 */
	public class GridView extends Sprite
	{
		private var _data:Array;
		public var list:GridLayout;
		
		public function GridView(gridWidth:Number, gridHeight:Number, tileWidth:Number, tileHeight:Number, tileAlgin:String="TL", algin:String="TL" ) 
		{
			_data = new Array();
			
			list = new GridLayout(this, gridWidth, gridHeight, tileWidth, tileHeight, 0, 0, tileAlgin, algin);
			list.useBounds = true;
			list.autoLayoutWhenAdd = true;
		}
		
		public function setData(value:Array):void {
			_data = value;
			for (var i:int = 0; i < _data.length; i++) 
			{
				list.add(_data[i].view);
			}
			list.layout();
		}
		
		public function add(value:GridItem):void {
			value.view.name = name + "_" + _data.length.toString();
			_data.push(value);
			list.add(value.view);
			
			list.layout();
		}
		
		
		
		public function clearAll():void {
			list.removeAll();
			while (numChildren>0) 
			{
				removeChildAt(0);
			}
			_data = new Array();
		}
		
		public function get data():Array { return _data; }
		
		public function set data(value:Array):void 
		{
			_data = value;
		}
		
	}

}