package happymagic.model.vo 
{
	import com.adobe.serialization.json.JSON;
	import com.brokenfunction.json.decodeJson;
	import com.brokenfunction.json.encodeJson;
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author jj
	 */
	public class ItemVo extends ItemClassVo
	{
		public var id:uint;
		public var num:uint=1;
		
		public function ItemVo() 
		{
			
		}
		
		public function setValue(obj:Object):ItemVo 
		{
			var itemClass:ItemClassVo=DataManager.getInstance().getItemClassByIid(obj.i_id);
			var name:String;
			
			var tmpobj:Object = decodeJson(JSON.encode(itemClass));
			for (name in tmpobj) 
			{
				this[name] = tmpobj[name];
			}
			
			for (name in obj) 
			{
				if (name!="i_id") 
				{
					this[name] = obj[name];
				}
			}
			
			return this;
		}
		
	}

}