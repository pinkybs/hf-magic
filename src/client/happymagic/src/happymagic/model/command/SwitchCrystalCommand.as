package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	/**
	 * ...
	 * @author jj
	 */
	public class SwitchCrystalCommand extends BaseDataCommand
	{
		
		public function SwitchCrystalCommand() 
		{
			
		}
		
		/**
		 * 
		 * @param	num
		 * @param	type	对方水晶的类别
		 * @param	uid		对方的uid
		 */
		public function switchCrystal(num:uint, type:uint,uid:String):void {
			createLoad();
			
			createRequest(InterfaceURLManager.getInstance().getUrl("switchCrystal"), { num:num, type:type, uid:uid } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}