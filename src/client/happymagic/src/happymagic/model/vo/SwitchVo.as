package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author jj
	 */
	public class SwitchVo extends BasicVo
	{
		
		public var switchList:Array;
		//当前可领水晶  内有red blue green三值
		public var crystals:Object;
		public var currentPutNum:int;
		public function SwitchVo() 
		{
			
		}
		
		public function setValue(value:Object):SwitchVo
		{
			switchList = new Array();
			for (var i:int = 0; i < value.switchList.length; i++) 
			{
				switchList.push(new SwitchRecordVo().setData(value.switchList[i]));
			}
			
			crystals = value.crystals;
			
			currentPutNum=value.currentPutNum;
			
			
			return this;
		}
		
		public function get canGet():Boolean {
			return (crystals.red || crystals.blue || crystals.green);
		}
		
		public function clear():void {
			crystals.red = crystals.blue = crystals.green = 0;
			switchList = new Array();
		}
	}

}