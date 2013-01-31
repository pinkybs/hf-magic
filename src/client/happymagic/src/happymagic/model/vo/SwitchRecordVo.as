package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author jj
	 */
	public class SwitchRecordVo extends BasicVo
	{
		public var uid:uint;
		public var uname:String;
		//对方的水晶类型
		public var crystalType:uint;
		public var num:uint;
		public var time:Date;
		//记录状态 0:已领  1:未领
		public var status:uint;
		public function SwitchRecordVo() 
		{
			
		}
		
		override public function setData(obj:Object):BasicVo 
		{
			for (var name:String in obj) 
			{
				if ( this.hasOwnProperty(name)) 
				{
					if (name=="time") 
					{
						
						time = new Date();
						time.setTime(obj.time);
					}else {
						this[name] = obj[name];
					}
					
				}
			}
			return this;
		}
		
	}

}