package happymagic.model.command 
{
	import com.adobe.serialization.json.JSON;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DataManager;
	import happymagic.model.MagicUrlLoader;
	import happymagic.model.vo.DecorClassVo;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.StudentVo;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.grid.person.Student;
	/**
	 * ...
	 * @author Beck
	 */
	public class FriendsHomeCommand extends BaseDataCommand
	{
		
		public function FriendsHomeCommand() 
		{

		}
		
		public function load($uid:String):void {
			
			createLoad();
			
			createRequest(InterfaceURLManager.getInstance().getUrl("friendsHome"), { uid:$uid } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
	}

}