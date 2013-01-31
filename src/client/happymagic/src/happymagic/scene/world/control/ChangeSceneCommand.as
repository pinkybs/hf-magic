package happymagic.scene.world.control 
{
	import happymagic.manager.DataManager;
	import happymagic.manager.PublicDomain;
	import happymagic.model.vo.SceneState;
	import happymagic.model.vo.SceneVo;
	import happymagic.scene.world.MagicWorld;
	/**
	 * 移动场景到新的地图(如渔人海湾之类)
	 * @author slamjj
	 */
	public class ChangeSceneCommand 
	{
		
		/**
		 * 
		 * @param	data	场景VO
		 */
		public function ChangeSceneCommand(data:SceneVo) 
		{
			//更换当前场景id
			DataManager.getInstance().currentUser.currentSceneId = data.sceneId;
			
			var tmpworld:MagicWorld = (DataManager.getInstance().worldState.world as MagicWorld);
			//清除此场景
			tmpworld.clear();
				
			var world_data:Object = new Object();
			if (data.sceneId!=PublicDomain.getInstance().getVar("defaultSceneId")) 
			{
				
				world_data['decorList'] = new Object();
				world_data['floorList'] = [];
				world_data['wallList'] = [];
				world_data['userInfo'] = DataManager.getInstance().curSceneUser;
				world_data['studentsList'] = [];
				DataManager.getInstance().worldState.world.create(world_data, true);
			}else {
				
				world_data['decorList'] = DataManager.getInstance().decorList;
				world_data['floorList'] = DataManager.getInstance().floorList;
				world_data['wallList'] = DataManager.getInstance().wallList;
				world_data['userInfo'] = DataManager.getInstance().curSceneUser;
				world_data['studentsList'] = DataManager.getInstance().getStudentsInRoom();
				DataManager.getInstance().worldState.world.create(world_data, true);
			}
			
			
			//更新场景grid数据
			//tmpworld.
			//更换背景图
			tmpworld.loadBigSceneBg();
			//重设NPC\怪等
			tmpworld.bigSceneView.setData(
				DataManager.getInstance().getSceneVoByClass(data.sceneId, SceneState.OPEN),
				DataManager.getInstance().getNpcsBySceneId(data.sceneId),
				DataManager.getInstance().enemys
			);
		}
		
	}

}