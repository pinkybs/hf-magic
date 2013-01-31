package happyfish.display.view 
{
	import flash.display.MovieClip;
	/**
	 * ...
	 * @author Beck Xu
	 */
	public class ItemRender
	{
		protected var _data:Object;
		protected var _view:MovieClip;
		public function ItemRender() 
		{
			
		}
		
		public function set data($data:Object):void
		{
			return;
		}
		
		public function get view():MovieClip
		{
			return this._view;
		}
		
		public function get data():Object
		{
			return this._data;
		}
		
		public function set numDiff($vl:int):void
		{
			return;
		}
	}

}