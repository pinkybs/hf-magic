package happymagic.actModule.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author zc
	 */
	public class HappyMagicDMVo extends BasicVo
	{
		public var mainTitleString:String // 主标题的文字
		public var titleArray:Array;// 副标题的文字 以及连接的论坛地址[["asadasd","http:://"],["asadasd","http:://"]]
		//外部链接的数据
		public var outerJoinArray:Array;//["http:://www.163.com","http:://www.163.com"]
		
		//内部链接的数据
		public var internalConnectionArray:Array//[[btnname,num,id],[btnname,num,id]] btnname 按钮的名字 num 想去第几页  id代表I_ID/D_ID/AverId
		
		public function HappyMagicDMVo() 
		{
			
		}
		
	}

}