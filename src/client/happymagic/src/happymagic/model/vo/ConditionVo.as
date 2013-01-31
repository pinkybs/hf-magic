package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	import happyfish.task.IConditionVo;
	/**
	 * ...
	 * @author jj
	 */
	public class ConditionVo extends BasicVo implements IConditionVo
	{
		private var _type:uint;
		private var _id:String;
		private var _num:uint;
		private var _currentNum:uint;
		//条件的判断条件: 0为拥有的数量   1为合成的数据
		public var conditionType:uint;
		public function ConditionVo() 
		{
			
		}
		
		public function get isCoin():Boolean {
			if (type==ConditionType.USER && (id==ConditionType.USER_COIN ) ) 
			{
				return true;
			}else {
				return false;
			}
		}
		
		public function set isCoin(value:Boolean):void {
			return;
		}
		
		public function get isGem():Boolean {
			if (type==ConditionType.USER && id==ConditionType.USER_GEM ) 
			{
				return true;
			}else {
				return false;
			}
		}
		public function set isGem(value:Boolean):void {
			return;
		}
		
		public function get type():uint 
		{
			return _type;
		}
		
		public function set type(value:uint):void 
		{
			_type = value;
		}
		
		public function get id():String 
		{
			return _id;
		}
		
		public function set id(value:String):void 
		{
			_id = value;
		}
		
		public function get num():uint 
		{
			return _num;
		}
		
		public function set num(value:uint):void 
		{
			_num = value;
		}
		
		public function get currentNum():uint 
		{
			return _currentNum;
		}
		
		public function set currentNum(value:uint):void 
		{
			_currentNum = value;
		}
		
		
	}

}