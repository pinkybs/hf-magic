package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author zc
	 */
	public class SignAwardVo extends BasicVo
	{
		
		public var awards:Array;
		public var day:int;
		public var fansaward:Array;
		
		public function SignAwardVo() 
		{
			
		}
		
		public function setVaule(obj:Object):SignAwardVo
		{
			awards = new Array();
			fansaward = new Array();
			var i:int = 0;
			for (var name:String in obj) 
			{
				if ( this.hasOwnProperty(name)) 
				{
					if (name=="awards") 
					{
						for ( i = 0; i < obj.awards.length; i++) 
						{
							awards.push(new ConditionVo().setData(obj.awards[i]));
						}
					}
					else if (name == "fansaward")
					{
					    for (i = 0; i < obj.fansaward.length; i++) 
						{
							fansaward.push(new ConditionVo().setData(obj.fansaward[i]));
						}
					}
					else 
					{
						this[name] = obj[name];
					}
				}
			}
			return this;
		}
			
	}
}