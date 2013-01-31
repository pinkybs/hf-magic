package happymagic.model.vo 
{
	/**
	 * ...
	 * @author Beck
	 */
	public class StudentStateType
	{
		//闲逛
		public static const FIDDLE:int = 0;
		//在位子上未开始学习
		public static const NOTEACH:uint = 1;
		//学习中
		public static const STUDYING:uint = 2;
		//学习结束,未领奖励
		public static const TEACHOVER:uint = 3;
		//中断
		public static const INTERRUPT:uint = 4;
		
		public function StudentStateType() 
		{
			
		}
		
	}

}