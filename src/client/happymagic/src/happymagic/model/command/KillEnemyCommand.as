package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	/**
	 * ...
	 * @author jj
	 */
	public class KillEnemyCommand extends BaseDataCommand
	{
		
		public function KillEnemyCommand() 
		{
			takeResult = false;
		}
		
		public function kill(enemyId:String):void {
			createLoad();
			
			createRequest(InterfaceURLManager.getInstance().getUrl("killEnemy"), { enemyId:enemyId } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}