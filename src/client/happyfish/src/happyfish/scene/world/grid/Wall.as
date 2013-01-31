package happyfish.scene.world.grid 
{
	import com.friendsofed.isometric.Point3D;
	import com.greensock.events.LoaderEvent;
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.display.DisplayObject;
	import flash.geom.Matrix;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import happyfish.cacher.bitmapMc.display.BitmapMc;
	import happyfish.cacher.bitmapMc.display.BitmapMovieMc;
	import happyfish.cacher.CacheSprite;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.WorldState;
	import happyfish.scene.world.WorldView;
	import happymagic.model.vo.DecorVo;
	import happymagic.scene.world.grid.item.Door;
	/**
	 * ...
	 * @author Beck
	 */
	public class Wall extends SolidObject
	{
		public var decorVo:DecorVo;
		public function Wall($data:Object, $worldState:WorldState,__callBack:Function=null) 
		{
			_bodyCompleteCallBack = __callBack;
			super($data, $worldState);
			decorVo = $data as DecorVo;
			view.container.sortPriority = -4;
		}
		
		/**
		 * 重写,设置层
		 */
		override protected function get layer():int
		{
			return WorldView.LAYER_REALTIME_SORT;
		}
		
        override protected function makeView():IsoSprite
        {
			//test case
			this._view = new IsoSprite(this.layer);
			this.asset = new CacheSprite();
			asset.bodyComplete_callback = bodyComplete;
			this.asset.className = this._data.class_name;
			this._view.container.addChild(this.asset);
			
			/*
			 * 设置属性 
			 */
			this.x = this._data.x;
			this.z = this._data.z;
			
			this.grid_size_x = this._data.size_x;
			this.grid_size_z = this._data.size_z;
			
			this.setMirror(this.x);
			
			var pos:Point3D = new Point3D(this._data.x, 0, this._data.z);
			this._view.setPos(pos);
			
            return this._view;
        }
		
		override protected function bodyComplete():void 
		{
			super.bodyComplete();
		}
		
		/**
		 * 镜像处理
		 */
		public function setMirror($x:int):void
		{
			//if ($x != 24) {
			if ($x != IsoUtil.roomStart) {
				this.asset.scaleX = -1;
				
				this.grid_size_x = this._data.size_z;
				this.grid_size_z = this._data.size_x;
			} else {
				this.asset.scaleX = 1;
				
				this.grid_size_x = this._data.size_x;
				this.grid_size_z = this._data.size_z;
			}
		}
		
		override public function move($grid_pos:Point3D):void
		{
			super.move($grid_pos);
			this.setMirror($grid_pos.x);
		}
		
		override public function positionIsValid() : Boolean
		{
			if (!this.isWallArea(this.gridPos.x, this.gridPos.z)) {
				return false;
			}
		
			if (!this._worldState.checkInRoom(this.gridPos.x, this.gridPos.z)) {
				return false;
			} 
			
			return true;
		}
		
		override public function finishMove():void
		{
			//回复所占格状态
			this._worldState.world.removeToGrid(this);
			
			this.x = this.gridPos.x;
			this.z = this.gridPos.z;
			
			//原有墙纸的移除
			Wall(this._worldState.world.nodeWallTileItems[x][z]).remove();
			
			this._worldState.world.addToGrid(this);
			
			//深度排序
			this._view.parent.sort();
			
			//放入wall tile list
			//this._worldState.world.saveWallTileNodeItem(this);
			
			this.commonFinish();
		}
		
		override public function remove():void
		{
			this._view.parent.removeIsoChild(this._view);
			
			this.removeIsoUiSprite();
			this.removeIsoTile();
			//this._worldState.view.isoView.removeIsoChild(this._view);
			
			this._worldState.world.removeItem(this);
		}
		
		
		public function cutDoor(doorMap:BitmapMovieMc):void
		{
			doorMap.gotoAndStop(1);
			
			cutBitmap(asset.bitmap_movie_mc, asset.bitmap_movie_mc.bitmapData.clone(), doorMap, doorMap.bitmapData.clone());
		}
		
		/**
		 * 重设墙的显示
		 */
		public function resetWallView():void
		{
			resetView(data.class_name, view_complete);
			setMirror(this.x);
		}
		
		override protected function view_complete():void 
		{
			super.view_complete();
			
			_view.container.addChild(asset);
		}
		
		private function cutBitmap(souce:DisplayObject, souceBmd:BitmapData, cutTarget:DisplayObject, cutBmd:BitmapData):void {
			cutBmd= new BitmapData(cutTarget.width, cutTarget.height, true, 0x00000000);
			var bs:BitmapData = new BitmapData(cutTarget.width, cutTarget.height, true, 0x00000000);
			var tmpmatrix:Matrix=new Matrix();
			cutBmd.draw(cutTarget,tmpmatrix,null,null,new Rectangle(0, 0, cutTarget.width, cutTarget.height) );
			
			var rect:Rectangle = new Rectangle(0, 0, souce.width, souce.height);
			var pt:Point = new Point(0, 0);
			
			var recttmp:Rectangle = cutTarget.getRect(souce);
			var rect2:Rectangle = new Rectangle(souce.x - cutTarget.x - 34, 
												souce.y-cutTarget.y-(souceBmd.height-cutBmd.height+2),
												souceBmd.width, souceBmd.height);
												
			var filter:BitmapData = new BitmapData(souceBmd.width, souceBmd.height, true);
			filter.fillRect(rect,  0xff0000ff);
			filter.threshold(cutBmd, rect2, pt, ">", 0x00000000, 0x00000000);	//所有不透明的点变为透明
			
			var tmpbd:BitmapData = (souce["bitmapData"] as BitmapData).clone();
			tmpbd.copyPixels(souceBmd, rect, pt, filter, pt, false);
			souce["bitmapData"] = tmpbd;
		}
		
	}

}