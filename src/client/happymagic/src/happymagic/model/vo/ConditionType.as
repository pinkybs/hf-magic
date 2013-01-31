package happymagic.model.vo 
{
	/**
	 * ...
	 * @author jj
	 */
	public class ConditionType
	{
		public static const ITEM:uint = 1;
		public static const DECOR:uint = 2;
		public static const USER:uint = 3;
		public static const MAGIC_CLASS:uint = 4;
		public static const MIX:uint = 5;
		public static const TRANS:uint = 6;
		public static const SCENE_UPGRADE:uint = 7;
		
		public static const USER_COIN:String = "coin";
		public static const USER_GEM:String = "gmoney";
		public static const USER_EXP:String = "exp";
		public static const USER_MP:String = "mp";
		//public static const USER_POPULARITY:String = "popularity";
		public static const USER_STUDENT_LIMIT:String = "student_limit";
		public static const USER_MP_MAX:String = "mpMax";
		public static const USER_DESK_LIMIT:String = "deskLimit";
		
		
		public static const idArr:Object = {coin:1,gmoney:2,exp:3,popularity:4};
		public function ConditionType() 
		{
			
		}
		
		public static function StringToInt(str:String):uint {
			return idArr[str];
		}
		
	}

}