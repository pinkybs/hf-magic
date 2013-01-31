package happyfish.scene.iso 
{
	import com.friendsofed.isometric.IsoUtils;
	import com.friendsofed.isometric.Point3D;
	import flash.geom.Point;
	import happyfish.scene.astar.Node;
	/**
	 * 工具类,仅仅是避免框架使用者,直接调用最底层的IsoUtils的简单继承
	 * @author Beck
	 */
	public class IsoUtil extends IsoUtils
	{
        public static const TILE_SIZE:Number = 32;
		static public var roomStart:uint=24;
		
		public function IsoUtil() 
		{
			
		}
		
		// a more accurate version of 1.2247...
		public static const Y_CORRECT:Number = Math.cos(-Math.PI / 6) * Math.SQRT2;
		
		/**
		 * Converts a 3D point in isometric space to a 2D screen position.
		 * @arg pos the 3D point.
		 */
		public static function isoToScreen(pos:Point3D):Point
		{
			var screenX:Number = pos.x - pos.z;
			var screenY:Number = pos.y * Y_CORRECT + (pos.x + pos.z) * .5;
			return new Point(screenX, screenY);
		}
		
		/**
		 * Converts a 2D screen position to a 3D point in isometric space, assuming y = 0.
		 * @arg point the 2D point.
		 */
		public static function screenToIso(point:Point):Point3D
		{
			var xpos:Number = point.y + point.x * .5;
			var ypos:Number = 0;
			var zpos:Number = point.y - point.x * .5;
			return new Point3D(xpos, ypos, zpos);
		}
		
		/**
		 * 转换存储的格子3d坐标为实际ISO的3d坐标
		 * 其实就是还原本身格子的大小
		 * @param	pos
		 * @return
		 */
		public static function gridToIso(pos:Point3D):Point3D
		{
			var xpos:int = pos.x * TILE_SIZE;
			var ypos:int = pos.y * TILE_SIZE;
			var zpos:int = pos.z * TILE_SIZE;
			
			return new Point3D(xpos, ypos, zpos);
		}
		
		public static function isoToGrid(pos:Point3D):Point3D
		{
			var xpos:int = pos.x / TILE_SIZE;
			var ypos:int = pos.y / TILE_SIZE;
			var zpos:int = pos.z / TILE_SIZE;
			
			return new Point3D(xpos, ypos, zpos);
		}
		
		public static function nodeToScreen(node:Node):Point {
			var pos:Point3D = new Point3D(node.x, 0, node.y);
			pos = gridToIso(pos);
			return isoToScreen(pos);
		}
		
	}

}