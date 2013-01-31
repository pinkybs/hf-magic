package  
{
	import com.adobe.serialization.json.JSON;
	import com.friendsofed.isometric.DrawnIsoTile;
	import com.friendsofed.isometric.Point3D;
	import flash.display.BitmapData;
	import flash.display.Sprite;
	import flash.geom.Matrix;
	import flash.utils.ByteArray;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.utils.Base64;
	
	/**
	 * ...
	 * @author jj
	 */
	public class GridWorld extends Sprite
	{
		private var data:Object;
		private var nodes:Object;
		
		public function GridWorld() 
		{
			//x = -1000;
			
		}
		
		public function setData(mapData:Object,gridSize:uint,mapSize:uint):void {
			
			y = (-mapSize/2)*gridSize;
			
			data = mapData;
			
			IsoUtil.TILE_SIZE = gridSize;
			
			var i:int;
			var m:int;
			var tmp:Node;
			if (data.data.length<=0) 
			{
				
				for (i = 0; i < mapSize; i++) 
				{
					for (m = 0; m < mapSize; m++) 
					{
						tmp = new Node(i, m);
						tmp.walkable = true;
						setNode(tmp);
					}
				}
			}else {
				var by:ByteArray = Base64.decodeToByteArray(data.data);
				by.uncompress();
				data.data = by.readUTF();
				
				var tmparr:Array = new Array();
				var tmparr2:Array=data.data.split(";");
				for (i = 0; i < tmparr2.length; i++) 
				{
					tmparr.push(new Array());
					tmparr[i]=(tmparr2[i].split(","));
				}
				for (i = 0; i < tmparr.length; i++) 
				{
					for (m = 0; m < tmparr.length; m++) 
					{
						tmp = new Node(i, m);
						tmp.walkable = (tmparr[i][m]==1) ? true : false;
						setNode(tmp);
					}
				}
			}
			
			//创建格子素材
			var tmptilesize:Number = IsoUtil.TILE_SIZE;
			var tileMap:BitmapData = new BitmapData(IsoUtil.TILE_SIZE * 2, IsoUtil.TILE_SIZE, true, 0xffffff);
			var tileMc:Sprite = new Sprite();
			tileMc.graphics.lineStyle(1, 0x000000,.5);
			tileMc.graphics.beginFill(0xFF3300);
			tileMc.graphics.moveTo( -tmptilesize, 0);
			tileMc.graphics.lineTo(0, -tmptilesize / 2);
			tileMc.graphics.lineTo(tmptilesize, 0);
			tileMc.graphics.lineTo(0, tmptilesize / 2);
			tileMc.graphics.lineTo( -tmptilesize, 0);
			tileMc.graphics.endFill();
					var tmpmatrix:Matrix = new Matrix();
					tmpmatrix.translate(tmptilesize,tmptilesize/2);
					tileMap.draw(tileMc,tmpmatrix);
			
			
			var tmpGrid:TileView;
			var tmpnode2:Node;
			for (var name:String in nodes) 
			{
				for (var name2:String in nodes[name]) 
				{
					tmpnode2 = nodes[name][name2];
					tmpGrid = new TileView(tileMap);
					tmpGrid.setNode(tmpnode2);
					tmpGrid.position = new Point3D(tmpnode2.x*IsoUtil.TILE_SIZE, 0, tmpnode2.y*IsoUtil.TILE_SIZE);
					
					addChild(tmpGrid);
				}
			}
		}
		
		public function setNode(value:Node):void {
			if (!nodes) 
			{
				nodes = new Object();
			}
			if (!nodes[value.x]) 
			{
				nodes[value.x] = new Object();
			}
			nodes[value.x][value.y] = value;
		}
		
		
		public function outData():Object {
			
			var nodesarr:Array = new Array();
			var tmparr:Array;
			for (var name:String in nodes) 
			{
				tmparr = new Array();
				
				for (var name2:String in nodes[name]) 
				{
					tmparr[name2]=nodes[name][name2].walkable ? 1 :0;
				}
				
				nodesarr.push(tmparr.join(","));
			}
			var str:String;
			str = nodesarr.join(";");
			
			//var by:ByteArray = new ByteArray();
			//by.writeUTF(str);
			//by.compress();
			//str = Base64.encodeByteArray(by);
			//
			//data.data = str;
			
			
			//***********************************
			
			var by:ByteArray = new ByteArray();
			by.writeObject(nodes);
			by.compress();
			str = Base64.encodeByteArray(by);
			
			data.data = str;
			
			
			return data;
		}
		
	}

}