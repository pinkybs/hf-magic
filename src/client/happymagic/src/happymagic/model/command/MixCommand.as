package happymagic.model.command 
{
	import flash.events.Event;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.ResultVo;
	/**
	 * ...
	 * @author jj
	 */
	public class MixCommand extends BaseDataCommand
	{
		
		public function MixCommand() 
		{
			piaoMsg = false;
		}
		
		
		public function mix(mix_mid:uint, nums:uint):void {
			
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("mix"),{mix_mid:mix_mid,nums:nums});
			
			loader.load(request);	
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
	}

}