package happymagic.model.vo 
{
	import com.adobe.serialization.json.JSON;
	import com.brokenfunction.json.decodeJson;
	import com.brokenfunction.json.encodeJson;
	import happyfish.modules.gift.interfaces.IGiftUserVo;
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author Beck
	 */
	public class UserVo implements IGiftUserVo
	{
		private var _uid:String;
		private var _name:String;
		private var _face:String;
		public var avatar:uint;
		private var _className:String;
		private var _level:uint;
		public var roomLevel:uint=1;
		public var exp:int;
		public var max_exp:int;
		public var gem:int;
		public var mp:int;
		public var max_mp:int;
		public var door_limit:int;
		public var desk_limit:int;
		public var tile_x_length:int;
		public var tile_z_length:int;
		public var students_limit:int = 10;
		
		public var trans_time:int;
		public var trans_mid:uint;
		public var trans_className:String;
		public var canTrans_time:int;
		//还有多少秒回复魔法
		public var replyMp_time:int;
		//回复的百分比
		public var replyMpPer:uint;
		//回复一次的最大时间
		public var replyMpTime:int;
		public var popularity:uint;
		public var currentSceneId:uint;
		
		//三种水晶
		public var coin:int;
		
		public var eat_limit:int;
		
		//好友的排序index
		public var index:uint;
		
		//[int] -1 表示领完了 0 是无奖励  1是连续一天登陆
	    public var signDay:int;
		
		//判断是否为粉丝
	    public var isfans:Boolean;
		//还剩分享可获奖励的次数		
		private var _feedNum:int;
		//领取礼包的数量
		public var signAwardNumber:String;
		//这个人还能不能送他礼物 
		private var _giftAble:Boolean;
		//你能不能对他发请求
		private var _giftRequestAble:Boolean;
		//未收过的礼物数量
		private var _giftNum:uint;
		//你收到的请求数量
		private var _giftRequestNum:uint;
		
		public var x:uint;
		public var y:uint;
		
		public function UserVo() 
		{
			
		}
		
		public function setData(obj:Object):UserVo 
		{
			for (var name:String in obj) 
			{
				this[name] = obj[name];
			}
			if (avatar) 
			{
				className = DataManager.getInstance().getAvatarVo(avatar).className;
			}
			if (trans_mid) 
			{
				trans_className= DataManager.getInstance().getAvatarVo(trans_mid).className;
			}
			if (!roomLevel) 
			{
				roomLevel = 1;
			}
			students_limit = DataManager.getInstance().getRoomLevel(roomLevel).student_limit;
				desk_limit = DataManager.getInstance().getRoomLevel(roomLevel).desk_limit;
			return this;
		}
		
		public function clone():UserVo {
			var tmp:UserVo = new UserVo();
			tmp.setData(decodeJson(JSON.encode(this)));
			return tmp;
		}
		
		public function toString():String {
			var str:String;
			str = "uid:" + uid+" ";
			str += "name:" + name+" ";
			str += "level:" + level+" ";
			str += "face:" + face+" ";
			
			return str;
		}
		
		public function get uid():String 
		{
			return _uid;
		}
		
		public function set uid(value:String):void 
		{
			_uid = value;
		}
		
		public function get name():String 
		{
			return _name;
		}
		
		public function set name(value:String):void 
		{
			_name = value;
		}
		
		public function get face():String 
		{
			return _face;
		}
		
		public function set face(value:String):void 
		{
			_face = value;
		}
		
		public function get giftAble():Boolean 
		{
			return _giftAble;
		}
		
		public function set giftAble(value:Boolean):void 
		{
			_giftAble = value;
		}
		
		public function get giftRequestAble():Boolean 
		{
			return _giftRequestAble;
		}
		
		public function set giftRequestAble(value:Boolean):void 
		{
			_giftRequestAble = value;
		}
		
		public function get giftNum():uint 
		{
			return _giftNum;
		}
		
		public function set giftNum(value:uint):void 
		{
			_giftNum = value;
		}
		
		public function get giftRequestNum():uint 
		{
			return _giftRequestNum;
		}
		
		public function set giftRequestNum(value:uint):void 
		{
			_giftRequestNum = value;
		}
		
		public function get level():uint 
		{
			return _level;
		}
		
		public function set level(value:uint):void 
		{
			_level = value;
		}
		
		public function get className():String 
		{
			return _className;
		}
		
		public function set className(value:String):void 
		{
			_className = value;
		}
		
		public function get feedNum():int 
		{
			return _feedNum;
		}
		
		public function set feedNum(value:int):void 
		{
			_feedNum = value;
		}
	}

}