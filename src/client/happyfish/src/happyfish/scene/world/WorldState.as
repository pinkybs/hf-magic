package happyfish.scene.world 
{
	import com.adobe.utils.ArrayUtil;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import happyfish.scene.astar.Grid;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.utils.NodesByteTools;
	import happyfish.scene.world.control.IsoPhysicsControl;
	import happyfish.scene.world.control.MouseCursorAction;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.SceneState;
	import happymagic.model.vo.SceneVo;
	import happymagic.scene.world.control.MouseDefaultAction;
	/**
	 * 存储世界对象
	 * @author Beck Xu
	 */
	public class WorldState
	{
		public var wallRectLeft:Rectangle;
		public var wallRectRight:Rectangle;
		public var doorRectLeft:Rectangle;
		public var doorRectRight:Rectangle;
		private var roomWalkNodes:Array;
		private var outRoomWalkNodes:Array;
		public var view:WorldView;
		public var world:GameWorld;
		public var diy:Boolean = false;
		public var mouseAction:MouseCursorAction;
		public var physicsControl:IsoPhysicsControl;
		
		public var roomRect:Rectangle;
		
		public var grid:Grid;
		
		public function WorldState() 
		{
            mouseAction = new MouseDefaultAction(this);
            MouseCursorAction.defaultAction = mouseAction;
		}
		
		/**
		 * 创建场景GRID数据
		 * tileX和tileY都为0时
		 * @param	tile_x_length	房间的X大小
		 * @param	tile_z_length	房间的Z大小
		 */
		public function initGrid(tile_x_length:int, tile_z_length:int):void
		{
			//创建grid
			//this.grid = new Grid(tile_x_length, tile_z_length);
			this.grid = new Grid(60, 60);
			
			var sceneVo:SceneVo = DataManager.getInstance().getSceneVoByClass(DataManager.getInstance().currentUser.currentSceneId, SceneState.OPEN);
			if(!sceneVo.nodes) sceneVo.nodes=NodesByteTools.turnBase64ToNodes(sceneVo.nodeStr);
			for (var i:int = 0; i < sceneVo.nodes.length; i++) 
			{
				for (var m:int = 0; m < sceneVo.nodes[i].length; m++) 
				{
					grid.setWalkable(i, m, sceneVo.nodes[i][m].walkable);
				}
			}
			
			if (tile_x_length==0 || tile_z_length==0) 
			{
				roomRect = new Rectangle(0, 0, 59, 59);
				wallRectLeft = new Rectangle(0, 0, 0, 0);
				wallRectRight = new Rectangle(0, 0, 0, 0);
				doorRectLeft = new Rectangle(0, 0, 0, 0);
				doorRectRight = new Rectangle(0, 0, 0, 0);
			}else {
				roomRect = new Rectangle(IsoUtil.roomStart, IsoUtil.roomStart, tile_x_length + 1, tile_z_length + 1);
				
				wallRectLeft = new Rectangle(IsoUtil.roomStart, IsoUtil.roomStart, tile_x_length+1, 1);
				wallRectRight = new Rectangle(IsoUtil.roomStart, IsoUtil.roomStart, 1, tile_z_length + 1);
				
				doorRectLeft = new Rectangle(IsoUtil.roomStart + 1, IsoUtil.roomStart + 1, tile_x_length + 1, 1);
				doorRectRight = new Rectangle(IsoUtil.roomStart + 1, IsoUtil.roomStart + 1, 1, tile_z_length + 1);
			}
			
			
			roomWalkNodes = null;
			outRoomWalkNodes = null;
		}
		
		/**
		 * 设置房间周边一圈格子为不可走
		 */
		public function closeRoomGrid():void {
			var i:int;
			for (i = 0; i < roomRect.width; i++) 
			{
				grid.setWalkable(roomRect.left+i, roomRect.top, false);
				grid.setWalkable(roomRect.left+i, roomRect.bottom, false);
			}
			
			for (i = 0; i < roomRect.height; i++) 
			{
				grid.setWalkable( roomRect.left, roomRect.top + i, false);
				grid.setWalkable(roomRect.right, roomRect.top + i,  false);
			}
		}
		
		public function isWallArea($x:int, $z:int):Boolean
		{
			var tmpp:Point = new Point($x, $z);
			
			return wallRectLeft.containsPoint(tmpp) || wallRectRight.containsPoint(tmpp);
		}
		
		public function isDoorArea($x:int, $z:int):Boolean
		{
			var tmpp:Point = new Point($x, $z);
			
			//return doorRectLeft.containsPoint(tmpp) || doorRectRight.containsPoint(tmpp);
			if ($x==IsoUtil.roomStart && $z==IsoUtil.roomStart) 
			{
				return false;
			}
			return wallRectLeft.containsPoint(tmpp) || wallRectRight.containsPoint(tmpp);
		}
		
		public function init($view:WorldView, $world:GameWorld,$physicsControl:IsoPhysicsControl):void
		{
			this.view = $view;
			this.world = $world;
			physicsControl = $physicsControl;
		}
		
		public function checkInRoom(x:Number, z:Number):Boolean
		{
			return roomRect.containsPoint(new Point(x, z));
		}
		
		public function getCustomRoomWalkAbleNode():Node {
			
			if (!roomWalkNodes) createRoomWalkAbles();
			
			var tmpnode:Node;
			var index:uint;
			if (roomWalkNodes.length>0) 
			{
				index = Math.floor(Math.random() * (roomWalkNodes.length-1));
				tmpnode = roomWalkNodes[index];
				
				return tmpnode;
			}else {
				return null;	
			}
		}
		
		public function getCustomOutRoomWalkAbleNode():Node {
			
			if (!outRoomWalkNodes) createRoomWalkAbles();
			
			var tmpnode:Node;
			var index:uint;
			if (outRoomWalkNodes.length>0) 
			{
				index = Math.floor(Math.random() * (outRoomWalkNodes.length-1));
				tmpnode = outRoomWalkNodes[index];
				
				return tmpnode;
			}else {
				return null;	
			}
			
		}
		
		public function createRoomWalkAbles():void {
			roomWalkNodes = new Array();
			outRoomWalkNodes = new Array();
			
			for (var i:int = 0; i < grid.walkAbleList.length; i++) 
			{
				if (checkInRoom(grid.walkAbleList[i].x, grid.walkAbleList[i].y)
					&& !isWallArea(grid.walkAbleList[i].x,grid.walkAbleList[i].y)) 
				{
					roomWalkNodes.push(grid.walkAbleList[i]);
				}else {
					outRoomWalkNodes.push(grid.walkAbleList[i]);
				}
			}
			
			if (roomRect.width>=59 && roomRect.height>=59) 
			{
				outRoomWalkNodes = roomWalkNodes;
			}
		}
		
		public function clearRoomWalkAbles():void
		{
			roomWalkNodes = null;
			outRoomWalkNodes = null;
		}
		
	}

}