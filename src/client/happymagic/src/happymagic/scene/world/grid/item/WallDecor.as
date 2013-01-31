package happymagic.scene.world.grid.item 
{
	import com.friendsofed.isometric.IsoObject;
	import com.friendsofed.isometric.Point3D;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.cacher.CacheSprite;
	import happyfish.events.GameMouseEvent;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.grid.SolidObject;
	import happyfish.scene.world.WorldState;
	import happyfish.scene.world.WorldView;
	import happymagic.model.vo.DecorType;
	import happymagic.model.vo.DecorVo;
	
	/**
	 * ...
	 * @author Beck Xu
	 */
	public class WallDecor extends SolidObject
	{
		public var decorVo:DecorVo;
		
		public function WallDecor($data:Object, $worldState:WorldState,__callBack:Function=null) 
		{
			_bodyCompleteCallBack = __callBack;
			super($data, $worldState);
			decorVo = $data as DecorVo;
			typeName = "WallDecor";
			//鼠标事件
            view.container.addEventListener(MouseEvent.ROLL_OVER, this.onMouseOver);
            view.container.addEventListener(MouseEvent.ROLL_OUT, this.onMouseOut);
			view.container.addEventListener(MouseEvent.MOUSE_MOVE, this.onMouseOverMove);
            view.container.addEventListener(MouseEvent.CLICK, this.onClick);
			
			view.container.sortPriority = -3;
		}
		
		
        override protected function makeView():IsoSprite
        {
			//test case
			this._view = new IsoSprite(this.layer);
			this.asset = new CacheSprite(false,1);
			asset.bodyComplete_callback=bodyComplete;
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
			playAnimation(this.movieLable);
			if (_bodyCompleteCallBack!=null) 
			{
				_bodyCompleteCallBack.apply(null,[this]);
			}
		}
		
		/**
		 * 镜像处理
		 */
		public function setMirror($x:int):void
		{
			//trace("setMirror", $x);
			if ($x != IsoUtil.roomStart) {
				this.asset.scaleX = -1;
				
				this.grid_size_x = this._data.size_z;
				this.grid_size_z = this._data.size_x;
				
				this.mirror = 1;
			} else {
				this.asset.scaleX = 1;
				
				this.grid_size_x = this._data.size_x;
				this.grid_size_z = this._data.size_z;
				
				this.mirror = 0;
			}
		}
		
		public function setIsoTile():void
		{
			if (this._isoTiles['0_0']) {
				this.removeIsoTile();
			} else {
				this.addIsoTile();
			}
		}
		
		override public function move($grid_pos:Point3D):void
		{
			super.move($grid_pos);
			
			if (data.type==DecorType.WALL_DECOR) 
			{
				if (this.isDoorArea(this.gridPos.x, this.gridPos.z)) {
					this.removeIsoTile();
					
					this.setMirror($grid_pos.x);
					
					this.addIsoTile();
				}
			}
			
		}
		
		
		override public function positionIsValid() : Boolean
		{
			if (!isWallArea(this.gridPos.x, this.gridPos.z)) {
				return false;
			}
			
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
						
						var tmpitem:IsoItem = _worldState.world.getNodeItem(gridPos.x,gridPos.z);
						
						if (tmpitem) 
						{
							if (tmpitem is WallDecor) 
							{
								canPut= false;
							}
							
						}
					}
				}
			}
			
			//如果不是所有格都是自己,就返回false
			return canPut;
		}
		

	}

}