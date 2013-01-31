package happymagic.model.vo 
{
	import com.adobe.serialization.json.JSON;
	import com.adobe.utils.ArrayUtil;
	import com.brokenfunction.json.decodeJson;
	import com.brokenfunction.json.encodeJson;
	import happyfish.scene.iso.IsoUtil;
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author Beck
	 */
	public class DecorVo extends DecorClassVo
	{
		public var id:int;
		public var x:int;
		public var y:int;
		public var z:int;
		public var mirror:int;
		//哪个背包 0:场景中 1:背包中
		public var bag_type:int;
		private var _num:int;
		
		public var door_left_time:int = 5;
		public var door_left_students_num:int = 0;
		
		public var ids:Array=new Array();
		
		public function DecorVo() 
		{
			super();
		}
		
		public function setValue(obj:Object):DecorVo {
			for (var name:String in obj) 
			{
				this[name] = obj[name];
			}
			
			//增加5秒,防止与后端时间偏差产生的问题
			if (door_left_time>0) 
			{
				door_left_time += 5;
			}
			
			x += IsoUtil.roomStart;
			z += IsoUtil.roomStart;
			
			var decor_class_vo:DecorClassVo = DataManager.getInstance().getDecorClassByDid(this.d_id);
			setClass(decor_class_vo);
			
			if (type==DecorType.FLOOR || type==DecorType.WALL_PAPER) {
				ids.push(obj.id);
			}else {
				num = 1;
				ids.push(obj.id);
			}
			
			
			
			return this;
		}
		
		public function createDefaultObj($d_id:int, $x:int, $z:int):void
		{
			var obj:Object = { };
			obj.id = 0;
			obj.bag_type = 0;
			obj.x = $x;
			obj.y = 0;
			obj.z = $z;
			obj.d_id = $d_id;
			obj.num = 1;

			this.setValue(obj);
		}
		
		public function setClass($decor_class_vo:DecorClassVo):void
		{
			if ($decor_class_vo) 
			{
				var tmpobj:Object = decodeJson(JSON.encode($decor_class_vo));
				for (var name2:String in tmpobj) 
				{
					this[name2] = tmpobj[name2];
				}
			}
			
			//this.size_x = $decor_class_vo.size_x;
			//this.size_y = $decor_class_vo.size_y;
			//this.size_z = $decor_class_vo.size_z;
			//this.type = $decor_class_vo.type;
			//this.class_name = $decor_class_vo.class_name;
			//this.name = $decor_class_vo.name;
			//this.door_guest_limit = $decor_class_vo.door_guest_limit;
			//this.door_refresh_time = $decor_class_vo.door_refresh_time;
			//level = $decor_class_vo.level;
		}
		
		public function add(value:DecorVo):void {
			if (value.d_id==d_id) 
			{
				num += value.num;
				ids = value.ids.concat(ids);
				id = ids[0];
			}
		}
		
		/**
		 * 移除一个或一组物品
		 * @param	value
		 */
		public function remove(value:DecorVo):void {
			for (var i:int = 0; i < value.ids.length; i++) 
			{
				removeByDid(value.ids[i]);
			}
		}
		
		/**
		 * 移除指定DID的物品
		 * @param	did
		 */
		private function removeByDid(did:uint):void {
			for (var j:int = 0; j < ids.length; j++) 
			{
				if (did == ids[j]) 
				{
					num--;
					ids.splice(j, 1);
					return;
				}
			}
		}
		
		public function delNum(delnum:uint = 1):Array {
			_num -= delnum;
			_num = Math.max(0, _num);
			
			var arr:Array
			//判断是否地板和墙纸
			if (type==DecorType.FLOOR || type==DecorType.WALL_PAPER) 
			{
				//地板\墙纸是只有一个ID的
				arr = [];
			}else {
				//删除一个ID
				arr = ids.splice(0, delnum);
				if (_num>0) 
				{
					id = ids[0];
				}
			}
			
			
			return arr;
		}
		
		public function get num():int { return _num; }
		
		public function set num(value:int):void 
		{
			_num = value;
		}
		
		public function clone():DecorVo {
			var tmp:DecorVo = new DecorVo();
			
			var tmpobj:Object = decodeJson(JSON.encode(this));
			for (var name2:String in tmpobj) 
			{
				tmp[name2] = tmpobj[name2];
			}
			return tmp;
		}
		
	}

}