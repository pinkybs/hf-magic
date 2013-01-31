package happymagic.actModule.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.actModule.model.vo.HappyMagicDMVo;
	import happymagic.manager.DataManager;
	import happymagic.model.command.BaseDataCommand;
	/**
	 * ...
	 * @author zc
	 */
	public class HappyMagicDMCommand extends BaseDataCommand
	{
		
		public function HappyMagicDMCommand() 
		{
			
		}

		public function setData():void 
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("HappyMagicDM"));
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			 
			var happyMagicDMVo:HappyMagicDMVo;
			
			if (objdata.happymagicdm)
			{
				happyMagicDMVo = new HappyMagicDMVo();
				happyMagicDMVo.setData(objdata.happymagicdm);				
			}
			
			DataManager.getInstance().setVar("happyMagicDMVo", happyMagicDMVo);
			
			commandComplete();
			
		}		
		
	}

}