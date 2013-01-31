package happymagic.actModule.prizes.model 
{
	import happyfish.model.vo.BasicVo;
	
	/**
	 * ...
	 * @author 
	 */
	public class PrizesAwardVo extends BasicVo 
	{
		public var id:uint;
		public var price:uint;
		public var state:uint; //0 为未领过 1为已领过
		public var index:uint;
		public function PrizesAwardVo() 
		{
			
		}
		
	}

}