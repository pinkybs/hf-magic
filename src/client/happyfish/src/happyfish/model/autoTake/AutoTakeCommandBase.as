package happyfish.model.autoTake 
{
	/**
	 * 返回数据自动处理的基类
	 * 子类名称有强制规定
	 * 例："Take"+数据变量名+"Command"
	 * @author slamjj
	 */
	public class AutoTakeCommandBase 
	{
		
		public function AutoTakeCommandBase(dataContainer:Object, value:*) 
		{
			
		}
		
		/**
		 * 接收并处理数据
		 * @param	dataContainer	数据容器，用来保存处理好的数据，一般是DataCommandBase里的data变量
		 * @param	value	需处理的数据
		 */
		public function take(dataContainer:Object, value:*):void{
			
		}
		
	}

}