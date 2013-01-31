package happyfish.manager.actModule.vo 
{
	import happyfish.model.vo.BasicVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class ActVo extends BasicVo
	{
		public var actName:String;
		public var state:uint;
		public var initIndex:uint;
		
		public var menuType:uint;
		public var menuIndex:uint;
		public var menuUrl:String;
		public var menuClass:String;
		public var menuLink:String;
		public var menuData:Object;
		
		public var menuJs:String;
		
		public var moduleUrl:String;//moudle1
		public var backModuleUrl:String;//一开始要加载的时候才有数据//moudel2
		public var moduleData:Object;
		
		public var needAct:String;//有没有前序
		public function ActVo() 
		{
			
		}
		
	}

}