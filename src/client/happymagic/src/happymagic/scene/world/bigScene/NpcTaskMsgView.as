package happymagic.scene.world.bigScene 
{
	/**
	 * ...
	 * @author jj
	 */
	public class NpcTaskMsgView extends taskStateIcon
	{
		
		public function NpcTaskMsgView() 
		{
			gotoAndStop(1);
		}
		
		public function setData(value:uint):void {
			if (value==0) 
			{
				gotoAndStop(1);
			}else if (value==1) 
			{
				gotoAndStop(2);
			}
		}
		
	}

}