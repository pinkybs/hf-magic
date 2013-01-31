package happyfish.model.vo 
{
	import com.adobe.serialization.json.JSON;
	import com.brokenfunction.json.decodeJson;
	import com.brokenfunction.json.encodeJson;
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author jj
	 */
	public class GuidesVo extends GuidesClassVo
	{
		public var state:uint;
		public function GuidesVo() 
		{
			
		}
		
		public function setValue(obj:Object):GuidesVo {
			gid = obj.gid;
			
			var tmpguides:GuidesClassVo = DataManager.getInstance().getGuidesClass(gid);
			if (tmpguides) 
			{
				var tmpobj:Object = decodeJson(JSON.encode(tmpguides));
			}
			
			
			setData(tmpobj);
			
			state = obj.state;
			
			return this;
		}
		
	}

}