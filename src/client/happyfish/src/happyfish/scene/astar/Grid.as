package happyfish.scene.astar
{
	import com.adobe.utils.ArrayUtil;
	import happyfish.scene.iso.IsoUtil;
	/**
	 * Holds a two-dimensional array of Nodes methods to manipulate them, start node and end node for finding a path.
	 */
	public class Grid
	{
		private var _startNode:Node;
		private var _endNode:Node;
		private var _nodes:Array;
		private var _numCols:int;
		private var _numRows:int;
		private var _walkAbleList:Array;
		private var _diyAbleList:Array;
		
		/**
		 * Constructor.
		 */
		public function Grid(numCols:int, numRows:int)
		{
			_numCols = numCols;
			_numRows = numRows;
			_nodes = new Array();
			_walkAbleList = new Array();
			_diyAbleList = new Array();
			
			for(var i:int = 0; i < _numCols; i++)
			{
				_nodes[i] = new Array();
				for(var j:int = 0; j < _numRows; j++)
				{
					_nodes[i][j] = new Node(i, j);
					if (i != 0 && j!=0) 
					{
						_walkAbleList.push(_nodes[i][j]);
					}
					_diyAbleList.push(_nodes[i][j]);
				}
			}
		}
		
		
		////////////////////////////////////////
		// public methods
		////////////////////////////////////////
		
		/**
		 * Returns the node at the given coords.
		 * @param x The x coord.
		 * @param y The y coord.
		 */
		public function getNode(x:int, y:int):Node
		{
			return _nodes[x][y] as Node;
		}
		
		public function hasNode(x:int, y:int):Boolean
		{
			if (x < 0 || x >= _numCols || y < 0 || y >= _numRows) {
				return false;
			} else {
				return true;
			}
		}
		
		/**
		 * Sets the node at the given coords as the end node.
		 * @param x The x coord.
		 * @param y The y coord.
		 */
		public function setEndNode(x:int, y:int):void
		{
			_endNode = _nodes[x][y] as Node;
		}
		
		/**
		 * Sets the node at the given coords as the start node.
		 * @param x The x coord.
		 * @param y The y coord.
		 */
		public function setStartNode(x:int, y:int):void
		{
			_startNode = _nodes[x][y] as Node;
		}
		
		/**
		 * Sets the node at the given coords as walkable or not.
		 * @param x The x coord.
		 * @param y The y coord.
		 */
		public function setWalkable(x:int, y:int, value:Boolean):void
		{
			var node:Node = _nodes[x][y] as Node;
			//if (value && x!=IsoUtil.roomStart && y!=IsoUtil.roomStart) 
			if (value) 
			{
				_walkAbleList.push(node);
			}else {
				ArrayUtil.removeValueFromArray(_walkAbleList,node);
			}
			
			node.walkable = value;
		}
		
		/**
		 * Sets the node at the given coords as walkable or not.
		 * @param x The x coord.
		 * @param y The y coord.
		 */
		public function setDiyable(x:int, y:int, value:Boolean):void
		{
			var node:Node = _nodes[x][y] as Node;
			if (value) 
			{
				_diyAbleList.push(node);
			}else {
				ArrayUtil.removeValueFromArray(_diyAbleList,node);
			}
			node.diyable = value;
		}
		
		public function getCustomNode():Node {
			var nodeX:uint = Math.floor(Math.random() * (_numCols-1));
			var nodeY:uint = Math.floor(Math.random() * (_numRows-1));
			
			return _nodes[nodeX][nodeY];
		}
		
		public function getCustomWalkAbleNode():Node {
			if (_walkAbleList.length>0) 
			{
				var index:uint = Math.floor(Math.random() * (_walkAbleList.length-1));
				return _walkAbleList[index];
			}else {
				return null;	
			}
		}
		
		public function getCustomDiyAbleNode():Node {
			if (_diyAbleList.length>0) 
			{
				var index:uint = Math.floor(Math.random() * (_diyAbleList.length-1));
				return _diyAbleList[index];
			}else {
				return null;	
			}
		}
		
		////////////////////////////////////////
		// getters / setters
		////////////////////////////////////////
		
		/**
		 * Returns the end node.
		 */
		public function get endNode():Node
		{
			return _endNode;
		}
		
		/**
		 * Returns the number of columns in the grid.
		 */
		public function get numCols():int
		{
			return _numCols;
		}
		
		/**
		 * Returns the number of rows in the grid.
		 */
		public function get numRows():int
		{
			return _numRows;
		}
		
		/**
		 * Returns the start node.
		 */
		public function get startNode():Node
		{
			return _startNode;
		}
		
		public function get walkAbleList():Array { return _walkAbleList; }
		
		public function set walkAbleList(value:Array):void 
		{
			_walkAbleList = value;
		}
		
		public function clear():void {
			_walkAbleList = [];
			_diyAbleList = [];
		}
		
	}
}