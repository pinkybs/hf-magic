package happymagic.utils 
{
	/**
	 * 桌子上的请求队列管理
	 * @author Beck
	 */
	public class RequestQueue
	{
		
		public static const TYPE_PICKDECOR:String = "pickDecorIds";
		public static const TYPE_INTERRUPTDECOR:String = "interruptDecor";
		
		
		private static var single:RequestQueue;
		
		
		
		public var pickDecorIds:Array = []; 
		public var interruptDecorIds:Array = [];
		public function RequestQueue() 
		{
			
		}
		
		public static function getInstance():RequestQueue
		{
			if (single === null) {
				single = new RequestQueue;
			}
			return single;
		}
		
		public function add($key:String, $vl:int):void
		{
			if ($key == TYPE_PICKDECOR) {
				if(this.pickDecorIds.indexOf($vl) == -1) {
					this.pickDecorIds.push($vl);
				}
			} else if ($key == TYPE_INTERRUPTDECOR) {
				if(this.interruptDecorIds.indexOf($vl) == -1) {
					this.interruptDecorIds.push($vl);
				}
			}
		}
		
		/**
		 * 清除捡钱或处理中断队列中第一条
		 * @param	$key
		 */
		public function delOne($key:String):* {
			if ($key == TYPE_PICKDECOR) {
				return pickDecorIds.shift();
			} else if ($key == TYPE_INTERRUPTDECOR) {
				return interruptDecorIds.shift();
			}
		}
		
		/**
		 * 清空捡钱或处理中断队列
		 * @param	$key
		 */
		public function unset($key:String):void
		{
			if ($key == TYPE_PICKDECOR) {
				this.pickDecorIds = [];
			} else if ($key == TYPE_INTERRUPTDECOR) {
				this.interruptDecorIds = [];
			}
		}
		
	}

}