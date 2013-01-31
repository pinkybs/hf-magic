package happyfish.actModule.giftGetAct.commond 
{
	import com.brokenfunction.json.encodeJson;
	import flash.events.Event;
	import happyfish.actModule.giftGetAct.manager.GiftDomain;
	import happymagic.model.command.BaseDataCommand;
	/**
	 * ...
	 * @author ZC
	 */
	//发布我的愿望
	public class ReleaseMyWishCommond extends BaseDataCommand
	{
		private var strgiftId:String = "";
		
		public function ReleaseMyWishCommond() 
		{
			
		}
		
		
		public function setData(_giftId:Array):void 
		{
            strgiftId = _giftId.join("-");
						
			createLoad();
			createRequest(GiftDomain.getInstance().getInterfaceUrl("ReleaseMyWish"), {giftId :strgiftId } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
			
		}		
	}

}