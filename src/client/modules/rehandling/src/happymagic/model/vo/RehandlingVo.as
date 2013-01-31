package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author zc
	 */
	
	 //换装的静态数据
	public class RehandlingVo extends BasicVo
	{
		
		public var avatarId:uint;
		public var className:String;
		public var name:String;
		public var type:int// 1 是金币 2是宝石
	    public var num:int//消费的数量
		public var isNew:int;//是否为新品
		
		public function RehandlingVo() 
		{
			
		}
		
	}

}