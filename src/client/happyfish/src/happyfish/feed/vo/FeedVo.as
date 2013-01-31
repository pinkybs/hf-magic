package happyfish.feed.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author jj
	 */
	public class FeedVo extends BasicVo
	{
		public var id:uint;//发送请求的ID
		public var value:String; //Feed内容
		
		public function FeedVo() 
		{
			
		}
		
	}

}