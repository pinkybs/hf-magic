package happymagic.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DataManager;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.vo.RehandlingVo;
	/**
	 * ...
	 * @author zc
	 */
	public class RehandlingInitstaticCommand extends BaseDataCommand
	{
		
		public function RehandlingInitstaticCommand() 
		{
			
		}

		public function setData():void 
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("RehandlingInitstatic"));
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
            super.load_complete(e);			
			var temp:Array = new Array();
			var i:int;
			if (objdata.rehandlingVo) 
			{
				for (i = 0; i < objdata.rehandlingVo.length; i++) 
				{
					temp.push(new RehandlingVo().setData(objdata.rehandlingVo[i]));
				}
			}
			DataManager.getInstance().setVar("RehandlingInitstatic", temp);
			
			commandComplete();
		}		
	}

}