package happyfish.scene.astar
{
	import com.friendsofed.isometric.IsoUtils;
	import com.friendsofed.isometric.Point3D;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import happyfish.scene.iso.IsoUtil;
	/**
	 * ...
	 * @author slamjj
	 */
	public class NodesUtil
	{
		public static var boxSize:Number=22;
		public function NodesUtil() 
		{
			
		}
		
		public static function getRectNodes(rect:Rectangle):Array {
			var arr:Array = new Array();
			for (var i:int = 0; i < rect.width; i++) 
			{
				for (var m:int = 0; m < rect.height; m++) 
				{
					arr.push(new Node(rect.left+i, rect.top+m));
				}
			}
			return arr;
		}
		
		public static function getAroundNodes(nodes:Array, offsetPos:Point = null):Array {
			
			nodes = outArroundNodes(nodes);
			
			var arr:Array = new Array();
			
			arr = arr.concat(getLineAllNodes(nodes, 0));
			
			if (nodes.length>2) 
			{
				for (var i:int = 1; i < nodes.length-1; i++) 
				{
					arr = arr.concat(getLine2PointNodes(nodes, i));
				}
			}
			
			if (nodes.length>1) 
			{
				arr = arr.concat(getLineAllNodes(nodes, nodes.length-1));
			}
			
			if (offsetPos) 
			{
				ofsetNodes(arr, offsetPos);
			}
			
			return arr;
		}
		
		public static function outArroundNodes(nodes:Array):Array
		{
			
			var numCols:int = nodes.length + 1;
			var numRows:int = nodes[0].length + 1;
			
			var arr:Array = new Array();
			var tmparr:Array;
			var tmpnode:Node;
			for (var i:int = -1; i < numCols; i++) 
			{
				tmparr = new Array();
				for (var n:int = -1; n < numRows; n++) 
				{
					tmpnode = new Node(0,0);
					tmpnode.x = i;
					tmpnode.y = n;
					tmparr.push(tmpnode);
				}
				arr.push(tmparr);
			}
			
			return arr;
		}
		
		public static function getNodePosition(node:Node):Point {
			
			var pos:Point = IsoUtils.isoToScreen(IsoUtil.gridToIso(new Point3D(node.x,0, node.y)));
			return new Point(pos.x, pos.y);
		}
		
		private static function ofsetNodes(nodes:Array,ofsetPos:Point):void {
			
			for (var i:int = 0; i < nodes.length; i++) 
			{
				//trace("ofsetNodes1",i,nodes[i].x,nodes[i].y);
				nodes[i].x += ofsetPos.x;
				nodes[i].y += ofsetPos.y;
				//trace("ofsetNodes2",i,nodes[i].x,nodes[i].y);
			}
		}
		
		private static function getLine2PointNodes(nodes:Array,lineIndex:int):Array {
			var lineLength:uint = nodes[lineIndex].length;
			
			var arr:Array = new Array();
			var tmpnode:Node;
			
			tmpnode = new Node();
			tmpnode.x = nodes[lineIndex][0].x;
			tmpnode.y = nodes[lineIndex][0].y;
			arr.push(tmpnode);
			
			tmpnode = new Node();
			tmpnode.x = nodes[lineIndex][lineLength-1].x;
			tmpnode.y = nodes[lineIndex][lineLength-1].y;
			arr.push(tmpnode);
			
			return arr;
		}
		
		private static function getLineAllNodes(nodes:Array, lineIndex:int):Array {
			var lineLength:uint = nodes[lineIndex].length;
			
			var tmpnode:Node;
			var arr:Array = new Array();
			for (var i:int = 0; i < lineLength; i++) 
			{
				tmpnode = new Node();
				tmpnode.x = nodes[lineIndex][i].x;
				tmpnode.y = nodes[lineIndex][i].y;
				arr.push(tmpnode);
			}
			
			return arr;
		}
		
		public static function isSamePosition(node1:Node,node2:Node):Boolean {
			if (node1.x==node2.x && node1.y==node2.y) 
			{
				return true;
			}else {
				return false;
			}
		}
		
		/**
		 * 判断路径中是否有指定的NODE,如果有返回INDEX,如果没有返回-1
		 * @param	path
		 * @param	node
		 * @return
		 */
		public static function checkPathThrowNode(path:Array,node:Node):int {
			for (var i:int = 0; i < path.length; i++) 
			{
				if (path[i].x==node.x && path[i].y==node.y) 
				{
					return i;
				}
			}
			return -1;
		}
		
	}

}