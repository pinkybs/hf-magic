package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author jj
	 */
	public class DiaryVo extends BasicVo
	{
		public var id:uint;
		public var type:uint;
		public var icon:uint;
		public var content:String;
		public var createTime:Date;
		public function DiaryVo() 
		{
			
		}
		
		override public function setData(obj:Object):BasicVo 
		{
			for (var name:String in obj) 
			{
				if ( this.hasOwnProperty(name)) 
				{
					if (name=="createTime") 
					{
						createTime = new Date();
						createTime.setTime(obj[name]);
					}else {
						this[name] = obj[name];
					}
					
				}
			}
			
			
			
			createTime = new Date();
			createTime.setTime(obj.createTime*1000);
			
			return this;
		}
		
	}

}