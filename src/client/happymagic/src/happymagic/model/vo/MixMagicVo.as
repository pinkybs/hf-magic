package happymagic.model.vo 
{
	import com.adobe.serialization.json.JSON;
	/**
	 * ...
	 * @author jj
	 */
	public class MixMagicVo
	{
		public var mix_mid:uint;
		public var name:String;
		public var type:uint;
		public var d_id:uint;
		public var coin:uint;
		public var gem:uint;
		public var decorId:Array;
		public var itemId:Array;
		public var needLevel:uint;
		public var mp:uint;
		public var exp:uint;
		
		public function MixMagicVo() 
		{
			
		}
		
		public function setData(obj:Object):MixMagicVo {
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