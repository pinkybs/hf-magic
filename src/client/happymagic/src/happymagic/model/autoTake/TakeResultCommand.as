package happymagic.model.autoTake 
{
	import flash.geom.Point;
	import happyfish.model.autoTake.AutoTakeCommandBase;
	import happymagic.model.control.TakeResultVoControl;
	import happymagic.model.vo.ResultVo;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class TakeResultCommand extends AutoTakeCommandBase 
	{
		public var takeResult:Boolean = true;
		//是否用飘屏显示错误信息
		public var piaoMsg:Boolean = true;
		public var piaoPoint:Point;
		
		public function TakeResultCommand() 
		{
			
		}
		
		override public function take(dataContainer:Object, value:*):void 
		{
			dataContainer.result = new ResultVo().setValue(value);
			if(takeResult || !dataContainer.result.isSuccess) TakeResultVoControl.getInstance().take(dataContainer.result, piaoMsg, piaoPoint);
		}
		
	}

}