package happymagic.scene.world.control 
{
	import flash.events.Event;
	import happyfish.manager.EventManager;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.FriendsHomeCommand;
	/**
	 * ...
	 * @author Beck
	 */
	public class FriendsHome
	{
		private static var single:FriendsHome;
		private var uid:String;

		public function FriendsHome($uid:String) 
		{
			uid = $uid;
			
			DisplayManager.uiSprite.showSceneOutMv(init);
		}
		
		public function init():void
		{
			
			//清除房间的数据
			DataManager.getInstance().decorList = {};
			DataManager.getInstance().floorList = [];
			DataManager.getInstance().wallList = [];
			DataManager.getInstance().enemys = [];
			
			//发起请求
			var f_command:FriendsHomeCommand = new FriendsHomeCommand();
			f_command.addEventListener(Event.COMPLETE, loadCompleted);
			f_command.load(uid);
		}
		
		private function loadCompleted(e:Event):void
		{
			e.target.removeEventListener(Event.COMPLETE, loadCompleted);
			
			//清除此场景
			DataManager.getInstance().worldState.world.clear();
			
			var world_data:Object = new Object();
			
			world_data['decorList'] = DataManager.getInstance().decorList;
			world_data['floorList'] = DataManager.getInstance().floorList;
			world_data['wallList'] = DataManager.getInstance().wallList;
			world_data['userInfo'] = DataManager.getInstance().curSceneUser;
			world_data['studentsList'] = DataManager.getInstance().getStudentsInRoom();
			DataManager.getInstance().worldState.world.create(world_data, true);
		}
	}

}