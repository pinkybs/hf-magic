package happymagic.events 
{
	import flash.geom.Point;
	import happymagic.model.vo.ResultVo;
	/**
	 * ...
	 * @author jj
	 */
	public class UserInfoChangeVo
	{
		public var levelUp:Boolean;
		public var roomLevelUp:int;
		public var coin:int;
		public var gem:int;
		public var exp:int;
		public var mp:int;
		
		public var piao:Boolean;
		public var showPoint:Point;
		
		public var maxMp:int;
		public function UserInfoChangeVo() 
		{
			
		}
		
		public function turnFromResultVo(result:ResultVo):void {
			coin = result.coin;
			gem = result.gem;
			exp = result.exp;
			mp = result.mp;
			piao = true;
			levelUp = result.levelUP;
			roomLevelUp = result.roomLevelUp;
			
		}
		
		public function get isEmpty():Boolean {
			if (coin || gem || exp || mp || maxMp || roomLevelUp || levelUp) 
			{
				return false;
			}else {
				return true;
			}
		}
		
	}

}