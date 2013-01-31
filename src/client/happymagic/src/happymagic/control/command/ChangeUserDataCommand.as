package happymagic.control.command 
{
	import happymagic.manager.DataManager;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.grid.person.Player;
	import happymagic.scene.world.MagicWorld;
	/**
	 * ...
	 * @author ...
	 */
	public class ChangeUserDataCommand 
	{
		private var user:Object;
		
		public function ChangeUserDataCommand(userdata:Object) 
		{
			user = userdata;
			
			var player:Player;
			var targetUserData:UserVo;
			if (DataManager.getInstance().currentUser.uid==user.uid) 
			{
				player = (DataManager.getInstance().worldState.world as MagicWorld).player;
				targetUserData = DataManager.getInstance().currentUser;
				
			}else if (DataManager.getInstance().curSceneUser.uid==user.uid) 
			{
				player = (DataManager.getInstance().worldState.world as MagicWorld).scenePlayer;
				targetUserData = DataManager.getInstance().curSceneUser;
			}
			
			for (var name:String in user) 
			{
				if (targetUserData.hasOwnProperty(name)) 
				{
					targetUserData[name] = user[name];
					player.userVo[name] = user[name];
				}
			}
			
			
			//形象更改
			if (user.avatar) 
			{
				targetUserData.className = DataManager.getInstance().getAvatarVo(user.avatar).className;
				player.userVo.className = targetUserData.className;
			}
			if (user.trans_mid) 
			{
				targetUserData.trans_className = DataManager.getInstance().getAvatarVo(user.trans_mid).className;
				player.userVo.trans_className = targetUserData.trans_className;
			}
			
			player.refreshView();
			player.initTransTimer();
		}
		
	}

}