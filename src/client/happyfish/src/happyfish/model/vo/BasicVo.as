package happyfish.model.vo 
{
	import flash.utils.getQualifiedClassName;
	/**
	 * ...
	 * @author Beck
	 */
	public class BasicVo
	{
		
		public function BasicVo() 
		{
			
		}
		
		public function setData(obj:Object):BasicVo {
			for (var name:String in obj) 
			{
				if ( this.hasOwnProperty(name)) 
				{
					this[name] = obj[name];
				}
			}
			return this;
		}
		
	}

}