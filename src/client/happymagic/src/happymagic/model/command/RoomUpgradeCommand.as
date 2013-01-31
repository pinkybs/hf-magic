package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.control.TakeResultVoControl;
	import happymagic.model.vo.ResultVo;
	/**
	 * ...
	 * @author jj
	 */
	public class RoomUpgradeCommand extends BaseDataCommand
	{
		
		public function RoomUpgradeCommand() 
		{
			takeResult=false;
		}
		
		public function upgrade(id:uint,type:uint):void {
			createLoad();
			
			createRequest(InterfaceURLManager.getInstance().getUrl("roomUp"), { id:id,type:type } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			if (data.result) 
			{
				if (!(data.result as ResultVo).isSuccess) 
				{
					TakeResultVoControl.getInstance().take(data.result, piaoMsg, piaoPoint);
				}
			}
			
			commandComplete();
		}
		
	}

}