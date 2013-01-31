package happyfish.scene.world.grid 
{
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.world.WorldState;
	/**
	 * ...
	 * @author Beck
	 */
	public class BaseItem
	{
		protected var _data:Object;
		protected var _worldState:WorldState;
		/**
		 * 
		 * @param	$data  这个东西的数据
		 */
		public function BaseItem($data:Object, $worldState:WorldState)
		{
			this._data = $data;
			this._worldState = $worldState;
		}
		
		public function get data():Object
		{
			return this._data;
		}
		
		public function remove() : void {}
		
	}

}