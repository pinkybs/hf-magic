package happymagic.actModule.signAward.Commond 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DataManager;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.vo.SignAwardVo;
	/**
	 * ...
	 * @author ZC 
	 */
	public class SignAwardInitStaticCommand extends BaseDataCommand
	{
		
		public function SignAwardInitStaticCommand() 
		{

		}
		
		
		public function init():void
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl('SignAwardInitStatic') );
			loader.load(request);
		}
		
		
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			var arr:Array = new Array();
			
			if (objdata.signAwardClass)
			{
				
				for (var i:int = 0; i < objdata.signAwardClass.length; i++) 
					{
						arr.push(new SignAwardVo().setVaule(objdata.signAwardClass[i]));
					}
				DataManager.getInstance().signAwardClass = arr;					
			}
		
						
			commandComplete();
		}
		
		
	}

}