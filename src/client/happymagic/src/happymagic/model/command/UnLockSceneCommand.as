package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	/**
	 * ...
	 * @author jj
	 */
	public class UnLockSceneCommand extends BaseDataCommand
	{
		
		public function UnLockSceneCommand() 
		{
			
		}
		
		/**
		 * 解锁场景
		 * @param	sceneId
		 * @param	type	1:水晶解锁  2:宝石解锁
		 */
		public function unLockScene(sceneId:uint,type:uint):void {
			createLoad();
			
			createRequest(InterfaceURLManager.getInstance().getUrl("unlockScene"), { sceneId:sceneId, type:type } );
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}