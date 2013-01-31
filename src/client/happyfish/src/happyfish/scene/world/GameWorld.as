package happyfish.scene.world 
{
	import adobe.utils.CustomActions;
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.geom.Matrix;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import happyfish.manager.module.ModuleManager;
	import happyfish.scene.astar.AStar;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.iso.IsoView;
	import happyfish.scene.world.grid.BaseItem;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.grid.Person;
	import happyfish.scene.world.grid.SolidObject;
	import happyfish.scene.world.grid.Tile;
	import happyfish.scene.world.grid.Wall;
	import happymagic.model.vo.StudentStateType;
	import happymagic.scene.world.grid.item.Desk;
	import happymagic.scene.world.grid.item.Door;
	import happymagic.scene.world.grid.item.WallDecor;
	import happymagic.scene.world.grid.person.Player;
	import happymagic.scene.world.grid.person.Student;
	/**
	 * ...
	 * @author Beck
	 */
	public class GameWorld
	{
		//游戏数据
		protected var _data:Object;
		
		//背景容器
        protected var _groundSprite:Sprite;
		//背景格子
        protected var _groundGrid:Object;
        public var groundRect:Rectangle;
		//背景的bitmapdata
		public var groundData:BitmapData;
		protected var backgroundColor:uint = 0x9355046;
		//IsoView
		protected var _view:WorldView;
		
		protected var _items:Array;
		protected var _worldState:WorldState;
		//记录此node上的物品
		public var nodeItems:Object = new Object();
		//记录此node上的物品
		public var decorInstanceItems:Object = new Object();
		//记录此node上的地板或者墙壁
		private var _nodeWallTileItems:Object = new Object();
		
		//主角
		protected var _player:Player;
		//场景主人
		public var scenePlayer:Player;
		//标记当前是否正在渲染场景
		public var sceneLoading:Boolean;
		public function GameWorld($worldState:WorldState) 
		{
			this._worldState = $worldState;
			this._view = this._worldState.view;
			
			this._items = new Array();
		}
		
		public function create($data:Object, $init_flg:Boolean = true):void
		{
			
		}
		
        public function createItem($gridItem:BaseItem) : void
        {
            this.addItem($gridItem);
            return;
        }
		
		/**
		 * 将物件放入物件列表
		 * @param	$item
		 * @param	tmpAdd	[boolean] 临时增加进场景，此时不要增加到物品队列中
		 */
		public function addItem($item:BaseItem,tmpAdd:Boolean=false):void
		{
			this._items.push($item);
		}
		
		/**
		 * 清除物件列表内的物件
		 * @param	$item
		 */
		public function removeItem($item:BaseItem):void
		{
			for (var i:int = 0; i < this._items.length; i++ ) {
				if (BaseItem(this._items[i]) === $item) {
					//删除这个项目
					_items.splice(i, 1);
					return;
				}
			}
		}
		
		public function getDecorByIdType(id:uint,type:Class):IsoItem {
			for (var i:int = 0; i < items.length; i++) 
			{
				if ((items[i] is type)) 
				{
					if (items[i].data.id==id) 
					{
						return items[i];
					}
				}
			}
			return null;
		}
		
		public function getDecorById(id:uint):IsoItem {
			for (var i:int = 0; i < items.length; i++) 
			{
				if (!(items[i] is Person)) 
				{
					if (items[i].data.id==id) 
					{
						return items[i];
					}
				}
			}
			return null;
		}
		
		/**
		 * 返回一个空闲状态的学生Student对象
		 * @return
		 */
		public function getFiddleOneStudent():Student
		{
			for (var i:int = 0; i < this._items.length; i++ ) {
				//创建学生
				if (this._items[i] is Student) {
					if (this._items[i].data.state == StudentStateType.FIDDLE) {
						return this._items[i] as Student;
					}
				}
			}
			
			return null;
		}
		
		/**
		 * 返回在场景中的指定sid的学生的Student对象
		 * @param	sid
		 * @return
		 */
		public function getStudentBySid(sid:uint):Student {
			for (var i:int = 0; i < this._items.length; i++ ) {
				//创建学生
				if (this._items[i] is Student) {
					if (this._items[i].data.sid == sid) {
						return this._items[i] as Student;
					}
				}
			}
			
			return null;
		}
		
		/**
		 * 清除WORLD内所有内容
		 */
		public function clear():void {
			//所有墙与地板记录
			nodeWallTileItems = new Object();
			//物件记录
			nodeItems = new Object();
			//地板数据
			groundData.dispose();
			
		}
		
		public function destroyItems():void
		{
			
		}
		
        public function updateGroundBitmapData():void
        {
			if(groundData) groundData.dispose();
			groundData = new BitmapData(groundRect.width, groundRect.height, true,0x000000);
            this.groundData.draw(this._groundSprite, new Matrix(1, 0, 0, 1, -this.groundRect.x, -this.groundRect.y));
            return;
        }
		
		/**
		 * 加入格子,设置不可行走
		 * @param	$item
		 */
		public function addToGrid($item:BaseItem,inRoom:Boolean=true):void
		{
			var solid_object:SolidObject = SolidObject($item);
			
			var tmpx:int;
			var tmpz:int;
			var skip:Boolean;
			//设置不可行走和放置 XXX 只是觉得放在类实例化过程不太合适,应该加入场景之后才设置不可走,所以放在了这里
			for (var i:int = 0; i < solid_object.grid_size_x; i++) {
				
				solid_object.nodes[solid_object.x + i] = new Object();
				
				for (var j:int = 0; j < solid_object.grid_size_z; j++) {
					tmpx = solid_object.x + i;
					tmpz = solid_object.z + j;
					
					if (inRoom) 
					{
						if (!_worldState.checkInRoom(tmpx,tmpz)) 
						{
							continue;
						}
					}
					
					if (nodeItems[tmpx]) 
					{
						if (nodeItems[tmpx][tmpz]) 
						{
							if (!(nodeItems[tmpx][tmpz] is Wall )) 
							{
								skip = true;
							}
							
						}
					}
					if (!skip) 
					{
						solid_object.nodes[tmpx][tmpz] = true;
						this._worldState.grid.setWalkable(tmpx, tmpz, false);
						this._worldState.grid.setDiyable(tmpx, tmpz, false);
						this.saveNodeItems(solid_object,tmpx,tmpz);
					}
					
					
					/**
					 * 桌子的特殊处理
					 */
					if (solid_object is Desk) {
						if ( i==0 && j ==0) {
							this._worldState.grid.setWalkable(tmpx, tmpz, true);
						}
					}
					
					//门的特殊处理
					if (solid_object is Door) {
						if ( i==0 && j ==0) {
							
							if ((solid_object as Door).mirror) 
							{
								this._worldState.grid.setDiyable(tmpx, tmpz+1, false);
							}else {
								this._worldState.grid.setDiyable(tmpx+1, tmpz, false);
							}
						}
						this._worldState.grid.setWalkable(tmpx, tmpz, true);
					}
				}
			}
			
			
		}
		
		/**
		 * 回复物件下的格子的可行走和可DIY状态
		 * @param	$item
		 */
		public function removeToGrid($item:BaseItem,inRoom:Boolean=true):void
		{
			var solid_object:SolidObject = $item as SolidObject;
			
			var tmpx:int;
			var tmpz:int;
			var skip:Boolean;
			if (solid_object) 
			{
				//设置不可行走和放置 XXX 只是觉得放在类实例化过程不太合适,应该加入场景之后才设置不可走,所以放在了这里
				for (var i:int = 0; i < solid_object.grid_size_x; i++) {
					for (var j:int = 0; j < solid_object.grid_size_z; j++) {
						
						tmpx = solid_object.x + i;
						tmpz = solid_object.z + j;
						if (inRoom) 
						{
							if (!_worldState.checkInRoom(tmpx,tmpz)) 
							{
								continue;
							}
						}
						
						if (nodeItems[tmpx]) 
						{
							if (nodeItems[tmpx][tmpz]) 
							{
								if (nodeItems[tmpx][tmpz]!=$item  && !(nodeItems[tmpx][tmpz] is Wall)) 
								{
									skip = true;
								}
								
							}
						}
						if (!skip) 
						{
							this._worldState.grid.setWalkable(tmpx, tmpz, true);
							this._worldState.grid.setDiyable(tmpx, tmpz, true);
							removeNodeItems(solid_object,tmpx,tmpz);
						}
						
						
						if (solid_object is Door) {
							if ( i==0 && j ==0) {
								if ((solid_object as Door).mirror) 
								{
									this._worldState.grid.setDiyable(tmpx, tmpz+1, true);
								}else {
									this._worldState.grid.setDiyable(tmpx+1, tmpz, true);
								}
							}
							this._worldState.grid.setWalkable(tmpx, tmpz, false);
						}
					}
				}
				solid_object.nodes = new Object;
			}
			
			
			
		}
		
		/**
		 * 记录格子上所对应的对象
		 * @param	$item
		 */
		public function saveNodeItems($item:BaseItem,x:int,z:int):void
		{
			var solid_object:SolidObject = SolidObject($item);
			
			if (!this.nodeItems[x]) {
				this.nodeItems[x] = new Object();
			}
			
			//TODO 好像只记了一个物体的一个格子
			this.nodeItems[x][z] = solid_object;
			
			//依靠桌子实例id记录item对象
			if (solid_object is Desk) {
				this.decorInstanceItems[solid_object.data.id] = Desk(solid_object);
			}
		}
		
		/**
		 * 从占格物件列表中移除
		 * @param	$item
		 */
		public function removeNodeItems($item:BaseItem,x:int,z:int):void {
			var solid_object:SolidObject = SolidObject($item);
			
			if (nodeItems[x]) {
				if (nodeItems[x][z]) 
				{
					this.nodeItems[x][z] = null;
				}
			}
			
			//依靠桌子实例id记录item对象
			if (solid_object is Desk) {
				this.decorInstanceItems[solid_object.data.id] = null;
			}
		}
		
		/**
		 * 记录墙纸物件到列表中
		 * @param	$item
		 */
		public function saveWallTileNodeItem($item:BaseItem):void
		{
			var solid_object:IsoItem = IsoItem($item);
			
			if (solid_object.x == IsoUtil.roomStart && solid_object.z == IsoUtil.roomStart) {
				return;
			}
			
			if (!this.nodeWallTileItems[solid_object.x]) {
				this.nodeWallTileItems[solid_object.x] = new Object();
			}

			this.nodeWallTileItems[solid_object.x][solid_object.z] = solid_object;
		}
		
		public function get userInfo():Object
		{
			return {};
		}
		
		/**
		 * 待继承
		 */
		public function leaveEditMode():void
		{
			throw("待继承");
		}
		
		public function get groundSprite():Sprite		
		{
			return this._groundSprite;
		}
		
		public function get player():Player		
		{
			return this._player;
		}
		
		public function get items():Array { return _items; }
		
		public function get nodeWallTileItems():Object { return _nodeWallTileItems; }
		
		public function set nodeWallTileItems(value:Object):void 
		{
			_nodeWallTileItems = value;
		}
		
		public function getWallByNode(x:int, y:int):IsoItem {
			if (nodeWallTileItems[x]) 
			{
				if (nodeWallTileItems[x][y]) 
				{
					return nodeWallTileItems[x][y];
				}
			}
			return null;
		}
		
		public function findPath(startNode:Point,endNode:Point,mustGo:Boolean=false):Array
		{
			var _path:Array;
			
			var astar:AStar = new AStar();
			this._worldState.grid.setStartNode(startNode.x, startNode.y);
			this._worldState.grid.setEndNode(endNode.x, endNode.y);
			
			if(astar.findPath(_worldState.grid,mustGo))
			{
				astar.path.splice(0, 1);
				_path = astar.path;
				
				//判断路径步数
				if (_path.length==0) 
				{
					return null;
				}
				
			} else {
				return null;
			}
			
			return _path;
		}
		
		public function getNodeItem(x:Number, z:Number):IsoItem 
		{
			if (nodeItems[x]) 
			{
				if (nodeItems[x][z]) 
				{
					return nodeItems[x][z];
				}
			}
			
			return null;
		}
	}

}