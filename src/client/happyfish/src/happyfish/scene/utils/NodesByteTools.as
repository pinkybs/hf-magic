package happyfish.scene.utils 
{
	import flash.utils.ByteArray;
	import happyfish.scene.astar.Node;
	import happyfish.utils.Base64;
	/**
	 * ...
	 * @author jj
	 */
	public class NodesByteTools
	{
		
		public function NodesByteTools() 
		{
			
		}
		
		public static function turnBase64ToNodes(value:String):Array {
			var i:int;
			var m:int;
			
			var by:ByteArray = Base64.decodeToByteArray(value);
			by.uncompress();
			var str:String = by.readUTF();
			
			var tmparr:Array = new Array();
			var tmparr2:Array=str.split(";");
			for (i = 0; i < tmparr2.length; i++) 
			{
				tmparr.push(new Array());
				tmparr[i]=(tmparr2[i].split(","));
			}
			
			var tmp:Node;
			var outArr:Array = new Array();
			for (i = 0; i < tmparr.length; i++) 
			{
				outArr.push(new Array());
				for (m = 0; m < tmparr.length; m++) 
				{
					tmp = new Node(i, m);
					tmp.walkable = (tmparr[i][m]==1) ? true : false;
					outArr[i].push(tmp);
				}
			}
			
			return outArr;
		}
		
	}

}