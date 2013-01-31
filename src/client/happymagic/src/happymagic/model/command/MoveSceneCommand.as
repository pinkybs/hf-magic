package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	/**
	 * ...
	 * @author jj
	 */
	public class MoveSceneCommand extends BaseDataCommand
	{
		
		public function MoveSceneCommand() 
		{
			
		}
		
		public function moveScene(sceneId:uint):void {
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("moveScene"), { sceneId:sceneId } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}