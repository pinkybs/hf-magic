package happyfish.scene.world.grid 
{
	import com.friendsofed.isometric.DrawnIsoTile;
	import com.friendsofed.isometric.Point3D;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.filters.GlowFilter;
	import happyfish.cacher.bitmapMc.events.BitmapCacherEvent;
	import happyfish.cacher.CacheSprite;
	import happyfish.events.GameMouseEvent;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.WorldState;
	import happyfish.scene.world.WorldView;
	/**
	 * ...
	 * @author Beck
	 */
	public class SolidObject extends IsoItem
	{
		private var _layer:int;
		private var _diy:int;
		private var noWayIcon:cantWalkIcon;
		
		public var nodes:Object = new Object;
		
		protected var movieLable:String;
		protected var mouse_over:Boolean;
		
		public var stopLabel:String="";
		protected var _diyState:Boolean;
		
		public function SolidObject($data:Object, $worldState:WorldState) 
		{
			super($data, $worldState);
			movieLable = "normal";
			this.layer = WorldView.LAYER_REALTIME_SORT;
			
		}
		
		override public function setData($data:Object):void
		{
			this.x = this._data.x;
			this.z = this._data.z;
			this.mirror = this._data.mirror;
			if (mirror==0) 
			{
				this.grid_size_x = this._data.size_x;
				this.grid_size_z = this._data.size_z;
			}else {
				this.grid_size_x = this._data.size_z;
				this.grid_size_z = this._data.size_x;
			}
			
			this.type = this._data.type;
			
			this.gridPos = new Point3D();
			this.gridPos.x = this.x;
			this.gridPos.z = this.z;
		}
		
		protected function set layer($layer:int):void
		{
			this._layer = $layer;
		}
		
		protected function get layer():int
		{
			return this._layer;
		}
		
        override protected function makeView():IsoSprite
        {
			/**
            this.sprite = new IsoSymbolLoader(2);
            this.sprite.asset.url = type.spriteURL;
            this.sprite.asset.addEventListener(SymbolLoader.ASSET_LOADED, this.onSpriteLoaded);
            if (this.direction == "SW" || this.direction == "NW")
            {
                this.sprite.container.scaleX = -1;
            }
			*/
			//test case
			this._view = new IsoSprite(this.layer);
			
			
			
			this.asset = new CacheSprite();//CacherManager.load();
			asset.bodyComplete_callback=bodyComplete;
			this.asset.className = this._data.class_name;

			this._view.container.addChild(this.asset);
			
			/*
			 * 设置属性 
			 */
			this.x = this._data.x;
			this.z = this._data.z;
			this.mirror = this._data.mirror;
			if (mirror==0) 
			{
				this.grid_size_x = this._data.size_x;
				this.grid_size_z = this._data.size_z;
			}else {
				this.grid_size_x = this._data.size_z;
				this.grid_size_z = this._data.size_x;
			}
			
			
			var pos:Point3D = new Point3D(this._data.x, 0, this._data.z);
			this._view.setPos(pos);
			setMirro(mirror);
            return this._view;
        }
		
		/**
		 * 位置缓存对象完成时调用
		 */
		protected function bodyComplete():void
		{	
			playAnimation(this.movieLable);
			if (_bodyCompleteCallBack!=null) 
			{
				_bodyCompleteCallBack();
			}
		}
		
        private function onSpriteLoaded(event:Event) : void
        {
            this.updateAnimation();
            return;
        }
		
		/**
		 * 显示不能走图标
		 */
		public function showCantWalkIcon():void 
		{
			if (!noWayIcon) 
			{
				noWayIcon = new cantWalkIcon();
			}
			view.container.addChild(noWayIcon);
			noWayIcon.y = -asset.height/2;
			if (mirror==0) 
			{
				noWayIcon.x = -IsoUtil.TILE_SIZE;
			}
		}
		
		public function hideCantWalkIcon():void {
			if (noWayIcon) 
			{
				if (noWayIcon.parent) 
				{
					noWayIcon.parent.removeChild(noWayIcon);
					noWayIcon = null;
				}
			}
		}
		
		/**
		 * 验证位置是否可以放置
		 * @return
		 */
        override public function positionIsValid() : Boolean
        {
			//如果是墙所在位置,就不可以放置
			if (this.isWallArea(this.gridPos.x, this.gridPos.z)) {
				return false;
			}
			return this.basicPositionValid();
		}
		
		protected function basicPositionValid():Boolean
		{
			var node:Node;
			var allSelf:uint = grid_size_x * grid_size_z;
			var canPut:Boolean=true;
			for (var i:int = 0; i < this.grid_size_x; i++) {
				for (var j:int = 0; j < this.grid_size_z; j++) {
					//如果是建筑自己的所在位置则验证通过
					if (this.nodes[this.gridPos.x + i]) {
						if (this.nodes[this.gridPos.x + i][this.gridPos.z + j]) {
							continue;
						}
					}
					
					if (!this._worldState.checkInRoom(this.gridPos.x + i, this.gridPos.z + j)) {
						canPut=false;
					}else {
						node = this._worldState.grid.getNode(this.gridPos.x + i, this.gridPos.z + j);
						if (node.diyable === false) {
							canPut= false;
						}
					}
				}
			}
			
			//如果不是所有格都是自己,就返回false
			return canPut;
			
		}
		
		/**
		 * 让显示对象播放某个标签的动画
		 * @param	$label
		 * @param	isTmp	是否临时更换动画,区别是只更换动画,不修改movieLable,下次可以直接用这个标签回到原来的动画片段
		 */
		public function playAnimation($label:String,isTmp:Boolean=false):void
		{
			if(!isTmp) movieLable = $label;
			if (this.asset.bitmap_movie_mc) {
				this.asset.bitmap_movie_mc.gotoAndPlayLabels($label);
			}
		}
		
		//private function completedPlay(e:BitmapCacherEvent):void
		//{
			//this.asset.removeEventListener(BitmapCacherEvent.SPRITE_CACHE_COMPLETED, completedPlay);
		//}
		
		/**
		 * 播放动画
		 */
        protected function updateAnimation() : void
        {
			return;
            if (this.direction == "SW")
            {
                if (this.sprite.asset.hasAnimation("idleSW"))
                {
                    this.animationSuffix = "SW";
                    this.sprite.container.scaleX = 1;
                }
			}
            this.sprite.asset.playAnimation("idle" + this.animationSuffix);
            return;
        }
		
		/**
		 * 设置是否在DIY状态
		 */
		public function set diyState(value:Boolean):void {
			_diyState = value;
			if (value) 
			{
				playAnimation(stopLabel, true);
			}else {
				playAnimation(movieLable, true);
			}
			return;
		}
		
		
	}

}