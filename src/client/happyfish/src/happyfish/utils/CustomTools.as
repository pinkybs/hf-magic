package happyfish.utils 
{
	/**
	 * ...
	 * @author jj
	 */
	public class CustomTools
	{
		
		public function CustomTools() 
		{
			
		}
		
		public static function customInt(min:int,max:int):int {
			return Math.floor(Math.random() * (max - min)) + min;
		}
		
		public static function customFromArray(arr:Array, except:*= null,del:Boolean=false):*{
			if (arr.length <= 0) return null;
			var tmpIndex:uint = Math.floor(Math.random() * arr.length);
			var tmp:*;
			tmp = arr[tmpIndex];
			
			if (except) 
			{
				if (tmp===except) 
				{
					tmpIndex++;
				}
			}
			tmp = arr[tmpIndex];
			
			if(del) arr.splice(tmpIndex, 1);
			return tmp;
		}
		
	}

}