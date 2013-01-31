package happymagic.model.command 
{
	import flash.events.Event;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import happyfish.manager.InterfaceURLManager;
	/**
	 * ...
	 * @author jj
	 */
	public class ChangeMagicTypeCommand extends BaseDataCommand
	{
		
		public function ChangeMagicTypeCommand(_callBack:Function=null) 
		{
			super(_callBack);
		}
		
		public function change(type:uint):void 
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("changeMagicType"),{type:type});
			
			loader.load(request);
			
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}