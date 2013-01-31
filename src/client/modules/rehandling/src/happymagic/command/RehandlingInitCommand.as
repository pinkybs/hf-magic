package happymagic.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DataManager;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.vo.RehandlingStateVo;
	/**
	 * ...
	 * @author zc
	 */
	public class RehandlingInitCommand extends BaseDataCommand
	{
		
		public function RehandlingInitCommand() 
		{
			
		}

		public function setData():void 
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("RehandlingInit"));
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
            super.load_complete(e);			
			var temp:Array = new Array();
			var i:int;
			if (objdata.rehandlingStateVo) 
			{
				for (i = 0; i < objdata.rehandlingStateVo.length; i++) 
				{
					temp.push(new RehandlingStateVo().setData(objdata.rehandlingStateVo[i]));
				}
			}
			DataManager.getInstance().setVar("RehandlingInit", temp);
			
			commandComplete();
		}		
	}

}