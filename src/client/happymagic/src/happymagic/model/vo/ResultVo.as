package happymagic.model.vo 
{
	/**
	 * ...
	 * @author jj
	 */
	public class ResultVo
	{
		public var status:int;
		public var content:String;
		public var levelUP:Boolean;
		public var roomLevelUp:int;
		public var coin:int;
		public var gem:int;
		public var exp:int;
		public var mp:int;
		
		public static const SUCCESS:int = 1;
		public function ResultVo() 
		{
			
		}
		
		public function setValue(value:Object):ResultVo {
			for (var name:String in value) 
			{
				this[name] = value[name];
			}
			return this;
		}
		
		public function get isSuccess():Boolean {
			return status == SUCCESS;
		}
		
		public function clone():ResultVo {
			var tmp:ResultVo=new ResultVo();
			tmp.status = status;
			tmp.content = content;
			tmp.levelUP = levelUP;
			tmp.coin = coin;
			tmp.gem = gem;
			tmp.exp = exp;
			tmp.mp = mp;
			return tmp;
		}
	}

}