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
	public class EnemyVo extends EnemyClassVo
	{
		public var enemyId:String;
		public var x:Number;
		public var y:Number;
		public var z:Number;
		public var curHp:int;
		public function EnemyVo() 
		{
			
		}
		
		public function setValue(value:Object):EnemyVo {
			enemyId = value.enemyId;
			enemyCid = value.enemyCid;
			
			var obj:EnemyClassVo = DataManager.getInstance().getEnemyClass(value.enemyCid);
			var tmpobj:Object = decodeJson(JSON.encode(obj));
			setData(tmpobj);
			
			
			curHp = hp;
			
			return this;
		}
		
	}

}