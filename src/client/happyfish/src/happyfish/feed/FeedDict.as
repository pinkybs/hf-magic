package happyfish.feed 
{
	/**
	 * ...
	 * @author slamjj
	 */
	public class FeedDict 
	{
		private static var feedObj:Object;
		public function FeedDict() 
		{
			
		}
		
		public function init():void {
			feedObj = new Object();
			feedObj["mainInfo"] = 1;
		}
		
		public function getFeedId(value:String):uint {
			if (!feedObj) 
			{
				init();
			}
			return feedObj[value];
		}
	}

}