package happyfish.scene.world.grid 
{
	import com.friendsofed.isometric.Point3D;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.cacher.CacheSprite;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.world.WorldState;
	import happyfish.scene.world.WorldView;
	import happymagic.model.vo.DecorVo;
	/**
	 * ...
	 * @author Beck
	 */
	public class Tile extends IsoItem
	{
		public var decorVo:DecorVo;
		
		
		public function Tile($data:Object, $worldState:WorldState,_callBack:Function=null) 
		{
			_bodyCompleteCallBack = _callBack;
			super($data, $worldState);
			decorVo = $data as DecorVo;
		}
		
		override public function setData($data:Object):void
		{
			this.x = this._data.x;
			this.z = this._data.z;
			this.mirror = this._data.mirror;
			this.grid_size_x = this._data.size_x;
			this.grid_size_z = this._data.size_z;
			this.type = this._data.type;
		}
		
		override protected function makeView():IsoSprite
		{
			this._view = new IsoSprite(WorldView.LAYER_BOTTOM);
			
			this.asset = new CacheSprite();
			asset.bodyComplete_callback = bodyComplete;
			this.asset.className = this._data.class_name;
			this._view.container.addChild(this.asset);
			
			var pos:Point3D = new Point3D(this._data.x, 0, this._data.z);
			this._view.setPos(pos);
			
            return this._view;
		}
		
		/**
		 * 位置缓存对象完成时调用
		 */
		protected function bodyComplete():void
		{
			if (_bodyCompleteCallBack!=null) 
			{
				_bodyCompleteCallBack();
			}
		}
		
		override public function move($grid_pos:Point3D):void
		{
			this.gridPos = $grid_pos;
			this._view.setPos($grid_pos);
			
			super.move($grid_pos);
			
			//标示此地是否可以放置
			if (this.positionIsValid()) {
				this.view.container.alpha = 1;
			} else {
				this.view.container.alpha = 0.5;
			}
		}
		
		/**
		 * 判断是否在房屋外
		 * @return
		 */
		override public function positionIsValid():Boolean
		{
			if (!this._worldState.checkInRoom(this.gridPos.x, this.gridPos.z)) {
				return false;
			} 
			
			//TODO bug 应该单独判断
			if (this.isWallArea(this.gridPos.x, this.gridPos.z)) {
				return false;
			}
			
			return true;
		}
		
		override public function finishMove():void
		{
			this.x = this.gridPos.x;
			this.z = this.gridPos.z;
			
			this.commonFinish();
		}
		
	}

}