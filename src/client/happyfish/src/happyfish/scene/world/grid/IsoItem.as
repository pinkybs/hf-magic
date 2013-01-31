package happyfish.scene.world.grid 
{
	import com.friendsofed.isometric.DrawnIsoTile;
	import com.friendsofed.isometric.Point3D;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import flash.filters.GlowFilter;
	import flash.geom.Point;
	import flash.utils.getDefinitionByName;
	import flash.utils.getQualifiedClassName;
	import happyfish.cacher.CacheSprite;
	import happyfish.events.GameMouseEvent;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoLayer;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.WorldState;
	import happyfish.utils.SysTracer;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	/**
	 * ...
	 * @author Beck
	 */
	public class IsoItem extends BaseItem
	{
		protected var _view:IsoSprite;
		protected var alive:Boolean = true;
		public var asset:CacheSprite;
		private var newasset:CacheSprite;
		
		public var x:int;
		public var y:int;
		public var z:int;
		public var type:int;
		//横向占用格子
		public var grid_size_x:int;
		//竖向占用格子
		public var grid_size_z:int;
		
		//是否镜像 0为向右,1为向左
		public var mirror:int;
		
		public var gridPos:Point3D=new Point3D();
		public var isoUiSprite:CacheSprite;
		
		protected var _isoTiles:Object = new Object();
		
		protected var _bodyCompleteCallBack:Function;
		
		
		protected var typeName:String;
		
		protected var _physics:Boolean = false;
		
		//是否可保存DIY
		private var _saveAble:Boolean=true;
		
		/**
		 * 
		 * @param	$data	[Object] class_name,x,z
		 * @param	$worldState
		 */
		public function IsoItem($data:Object, $worldState:WorldState) 
		{
			super($data, $worldState);
			
			this.setData(this._data);
			
		}
		
		public function setData($data:Object):void
		{
			this.x = this._data.x;
			this.z = this._data.z;
			
			if(_view) _view.setPos(new Point3D(x, 0, z));
		}
		
        public function get view():IsoSprite
        {
            if (this._view == null)
            {
                this._view = this.makeView();
            }
			if(_view) _view.isoItem = this;
            return this._view;
        }
		
		protected function view_complete():void
		{
			_view.isoItem = this;
			if (asset && newasset) 
			{
				_view.container.removeChild(asset);
			}
			if (newasset) asset = newasset;
			
			newasset = null;
		}
		
		/**
		 * 设置刷新显示对象
		 * @param	className	素材类名
		 * @param	_callBack
		 */
		public function resetView(className:String="",_callBack:Function=null):void {
			if (className) {
				_data.class_name = className;
			}
			
			_bodyCompleteCallBack = _callBack;
			
			newasset = new CacheSprite(true);
			if (_callBack!=null) 
			{
				newasset.bodyComplete_callback = _callBack;
			}else {
				newasset.bodyComplete_callback = view_complete;
			}
			newasset.className = _data.class_name;
			
		}
		
		public function get bodyCompleteCallBack():Function { return _bodyCompleteCallBack; }
		
		public function set bodyCompleteCallBack(value:Function):void 
		{
			_bodyCompleteCallBack = value;
		}
		
		protected function makeView():IsoSprite
		{
			throw new Error("BaseItem: get makeView(): override me");	
		}
		
        override public function remove() : void
        {
			
			
			//设置格子数据
			this._worldState.world.removeToGrid(this);
			//从层中清除
            this._view.parent.removeIsoChild(this._view);
			//标记已清除
            this.alive = false;
			//清除自己的印章对象
			this.removeIsoUiSprite();
			//清除物品的占格可视对象
			this.removeIsoTile();
			//从world中的_items列表中清除
			this._worldState.world.removeItem(this);
            
        }
		/**
		 * 落地后
		 */
		public function landed():void {
			return;
		}
		
		public function positionIsValid() : Boolean
		{
			return true;
		}
		
		/**
		 * 当物品被拿起的时候,显示底层的tile框
		 */
		public function addIsoTile():void
		{
			for (var i:int = 0; i < this.grid_size_x; i++) {
				for (var j:int = 0; j < this.grid_size_z; j++) {
					this._isoTiles[i+'_'+j] = new DrawnIsoTile(IsoUtil.TILE_SIZE, 0x009932);
					this._isoTiles[i+'_'+j].x = i * IsoUtil.TILE_SIZE;
					this._isoTiles[i+'_'+j].z = j * IsoUtil.TILE_SIZE;
					this._view.container.addChild(this._isoTiles[i+'_'+j]);
				}
			}
		}
		
		/**
		 * 移除物件自身内的占格格子表现
		 */
		public function removeIsoTile():void
		{
			for (var i:int = 0; i < this.grid_size_x; i++) {
				for (var j:int = 0; j < this.grid_size_z; j++) {
					if (this._isoTiles[i + '_' + j]) {
						if (_isoTiles[i + '_' + j].parent) 
						{
							this._view.container.removeChild(this._isoTiles[i + '_' + j]);
						}
						
						this._isoTiles[i + '_' + j] = null;
					}
				}
			}
			this._isoTiles = new Object();
		}
		
		public function commonFinish():void
		{
			this.removeIsoTile();
		}
		
		public function onMouseOut(event:MouseEvent=null) : void
        {
			_view.container.dispatchEvent(new GameMouseEvent(GameMouseEvent.OUT, this, typeName));
        }
		
		protected function onMouseOver(e:MouseEvent):void
		{
			if (!asset.bitmap_movie_mc) 
			{
				return;
			}
			if (!asset.bitmap_movie_mc.hitTestPoint(asset.bitmap_movie_mc.mouseX, asset.bitmap_movie_mc.mouseY, true)) 
			{
				e.stopImmediatePropagation();
				(view.container.parent as IsoLayer).nextItemMouseEvent(view, e);
				return;
			}
			var pixel:uint = asset.bitmap_movie_mc.bitmapData.getPixel32(asset.bitmap_movie_mc.mouseX, asset.bitmap_movie_mc.mouseY);
			if (pixel>0) 
			{
				(view.container.parent as IsoLayer).outItem();
				(view.container.parent as IsoLayer).inItem(view.isoItem);
				_view.container.dispatchEvent(new GameMouseEvent(GameMouseEvent.OVER, this, typeName, e));
			}else {
				e.stopImmediatePropagation();
				(view.container.parent as IsoLayer).nextItemMouseEvent(view, e);	
			}
			
		}
		
		protected function onMouseOverMove(e:MouseEvent):void
		{
			if (!asset.bitmap_movie_mc) 
			{
				return;
			}
			//if (!asset.bitmap_movie_mc.hitTestPoint(asset.bitmap_movie_mc.mouseX, asset.bitmap_movie_mc.mouseY, true)) 
			//{
				//e.stopImmediatePropagation();
				//(view.container.parent as IsoLayer).nextItemMouseEvent(view, e);
				//return;
			//}
			var pixel:uint = asset.bitmap_movie_mc.bitmapData.getPixel32(asset.bitmap_movie_mc.mouseX, asset.bitmap_movie_mc.mouseY);
			if (pixel>0) 
			{
				(view.container.parent as IsoLayer).outItem();
				(view.container.parent as IsoLayer).inItem(view.isoItem);
				_view.container.dispatchEvent(new GameMouseEvent(GameMouseEvent.OVER, this, typeName, e));
			}else {
				onMouseOut(e);
				(view.container.parent as IsoLayer).nextItemMouseEvent(view, e);
				e.stopImmediatePropagation();
			}
		}
		
		protected function onClick(e:MouseEvent):void
		{
			
			if (!asset.bitmap_movie_mc) 
			{
				return;
			}
			
			var pixel:uint = asset.bitmap_movie_mc.bitmapData.getPixel32(asset.bitmap_movie_mc.mouseX, asset.bitmap_movie_mc.mouseY);
			if (pixel>0) 
			{
				view.container.dispatchEvent(new GameMouseEvent(GameMouseEvent.CLICK, this, typeName, e));
				//e.stopImmediatePropagation();
			}else {
				if (view.container == e.target) {
					view.container.dispatchEvent(new GameMouseEvent(GameMouseEvent.CLICK, this, typeName, e));
				}else {
					if((view.container.parent as IsoLayer).nextItemMouseEvent(view, e)) e.stopImmediatePropagation();	
				}
				
				//if ((view.container.parent as IsoLayer).getClickTargetPoint(e)) e.stopImmediatePropagation();
			}
		}
		
		/**
		 * 返回是否碰撞到
		 * @return
		 */
		public function checkPoint():Boolean {
			var pixel:uint = asset.bitmap_movie_mc.bitmapData.getPixel32(asset.bitmap_movie_mc.mouseX, asset.bitmap_movie_mc.mouseY);
			if (pixel>0) 
			{
				return true;
			}else {
				return false;
			}
		}
		
		/**
		 * 滤镜消除
		 */
        public function hideGlow() : void
        {
			if(view) this.view.container.filters = [];
            return;
        }
		
		/**
		 * 滤镜
		 */
        public function showGlow() : void
        {
            this.view.container.filters = [new GlowFilter(16776960, 1, 5, 5, 10, 1, false, false)];
            return;
        }
		
		/**
		 * 判断是否在房屋外
		 * @return
		 */
		public function outOfArea():Boolean
		{	
			if (!this._worldState.checkInRoom(this.gridPos.x, this.gridPos.z)) {
				return true;
			} 
			
			return false;
		}
		
		public function rorate($flag:Boolean = true):void
		{
			if (this.mirror == this._data.mirror) {
				//return;
			}
			
			if ($flag) {
				if (this.asset.scaleX == -1) {
					this.grid_size_x = this._data.size_z;
					this.grid_size_z = this._data.size_x;
				}
				
				if (this.asset.scaleX == 1) {
					this.grid_size_x = this._data.size_x;
					this.grid_size_z = this._data.size_z;
				}
			} else {
				if (this.asset.scaleX == 1) {
					this.grid_size_x = this._data.size_z;
					this.grid_size_z = this._data.size_x;
				}
				
				if (this.asset.scaleX == -1) {
					this.grid_size_x = this._data.size_x;
					this.grid_size_z = this._data.size_z;
				}
			}
		}
		
		public function setMirro(_mirror:int):void {
			if (_mirror) 
			{
				asset.scaleX = -1;
			}else {
				asset.scaleX = 1;
			}
		}
		
		/**
		 * 完成DIY时的拖动
		 */
		public function finishMove():void
		{
			
			//this.rorate(false);
			//恢复自己所在格的不可行走和可DIY属性
			this._worldState.world.removeToGrid(this);
			
			//更新位置数据
			this.x = this.gridPos.x;
			this.z = this.gridPos.z;
			
			//this.rorate(true);
			//设置自己现在所在格的不可行走和可DIY属性
			this._worldState.world.addToGrid(this);
			
			//深度排序
			this._view.parent.sort();
			
			//移除DIY时表现
			this.commonFinish();
		}
		
		public function isWallArea($x:int, $z:int):Boolean
		{
			return _worldState.isWallArea($x, $z);
		}
		
		public function isDoorArea($x:int, $z:int):Boolean {
			return _worldState.isDoorArea($x, $z);
		}
		
		public function move($grid_pos:Point3D):void
		{
			this.gridPos = $grid_pos;
			SysTracer.systrace("move ",$grid_pos.x, $grid_pos.z);
			this._view.setPos($grid_pos);
			
			//标示此地是否可以放置
			if (this is SolidObject) {
				(this as SolidObject).saveAble = positionIsValid();
			}
			
			//检测和UI的碰撞,创建一个新的sprite
			if (DisplayManager.uiDiyMenu.hitTestPoint(this._view.container.stage.mouseX, this._view.container.stage.mouseY, true)) {
				if (!this.isoUiSprite) {
					this.isoUiSprite = new CacheSprite();
					this.isoUiSprite.className = this._data.class_name;
					DisplayManager.uiSprite.addChild(this.isoUiSprite);
					this.isoUiSprite.mouseChildren = 
					this.isoUiSprite.mouseEnabled = false;
				}
				
				this.isoUiSprite.x = DisplayManager.uiSprite.mouseX;
				this.isoUiSprite.y = DisplayManager.uiSprite.mouseY;
				this.view.container.alpha = 0;
			} else {
				removeIsoUiSprite();
			}
			
			//设置层排序
			if(_view.parent) this._view.parent.setChildIndex(this._view.container, this._view.parent.numChildren - 1);
		}
		
		/**
		 * 移除DIY拖动时用的ui层上的显示对象
		 */
		public function removeIsoUiSprite():void
		{
			if (this.isoUiSprite) {
				DisplayManager.uiSprite.removeChild(this.isoUiSprite);
				this.isoUiSprite = null;
			}
		}
		
		public function set visible(value:Boolean):void {
			_view.container.visible = value;
		}
		
		public function get visible():Boolean {
			return _view.container.visible;
		}
		
		/**
		 * loading状态
		 * 
		 * value为 false 时表现loading状态
		 * value为 true 时表现正常状态
		 */
		public function set loadingState(value:Boolean):void {
			if (!_view) 
			{
				return;
			}
			mouseEnabled = value;
			if (value) 
			{
				_view.container.alpha = 1;
			}else {
				_view.container.alpha = .5;
			}
		}
		
		public function set mouseEnabled(value:Boolean):void {
			if (!_view) 
			{
				return;
			}
			_view.container.mouseEnabled = value;
			_view.container.mouseChildren = value;
			
		}
		
		public function get mouseEnabled():Boolean {
			return _view.container.mouseEnabled;
		}
		
		public function set alpha(value:Number):void {
			_view.container.alpha = value;
		}
		
		public function get saveAble():Boolean 
		{
			return _saveAble;
		}
		
		public function set saveAble(value:Boolean):void 
		{
			_saveAble = value;
			if (value) {
				view.container.alpha = 1;
			}else {
				view.container.alpha = 0.5;
			}
		}
		
		public function get physics():Boolean 
		{
			return _physics;
		}
		
		public function set physics(value:Boolean):void 
		{
			_physics = value;
		}
		
		public function clear():void {
			
		}
		
		//public function get customRoundNode():Node {
			//
		//}
	}

}