package happymagic.scene.world.control 
{
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.utils.setTimeout;
	import happyfish.feed.Command.FeedControlCommond;
	import happyfish.feed.FeedType;
	import happyfish.manager.EventManager;
	import happyfish.scene.astar.Grid;
	import happyfish.scene.astar.Node;
	import happyfish.scene.astar.NodesUtil;
	import happyfish.scene.camera.CameraControl;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.WorldView;
	import happyfish.utils.display.McShower;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	/**
	 * 房间升级时动画表现
	 * @author slamjj
	 */
	public class RoomUpgradeMvCommand 
	{
		private var roomUpMvArr:Array;
		
		public function RoomUpgradeMvCommand() 
		{
			EventManager.getInstance().addEventListener(SceneEvent.WALL_COMPLETE, sceneLevelUpMv);
		}
		
		/**
		 * 房屋升级表现动画
		 * @param	e
		 */
		public function sceneLevelUpMv(e:SceneEvent=null):void 
		{
			if(e){
				e.target.removeEventListener(SceneEvent.WALL_COMPLETE, sceneLevelUpMv);
			}
			
			//整理需要表现烟雾动画的格子
			var nodes:Array = new Array();
			var grid:Rectangle= DataManager.getInstance().worldState.roomRect;
			var xnum:uint = grid.width-1;
			var ynum:uint = grid.height-1;
			var i:int;
			for (i = 0; i < xnum; i++) 
			{
				nodes.push(new Node(grid.x+i, grid.y+ynum));
			}
			
			for (i = ynum; i > 0; i--) 
			{
				nodes.push(new Node(grid.x+xnum, grid.y+i));
			}
			var tmpmv:McShower;
			//创建所有烟动画
			roomUpMvArr = new Array();
			for (var j:int = 0; j < nodes.length; j++) 
			{
				var item:Node = nodes[j];
				tmpmv = new McShower(roomUpgradeMv,
					DataManager.getInstance().worldState.view.isoView.camera, null, null, null, null, true, false);
				var p:Point = NodesUtil.getNodePosition(item);
				p = DataManager.getInstance().worldState.view.isoView.getLayer(WorldView.LAYER_REALTIME_SORT).localToGlobal(p);
				p = DataManager.getInstance().worldState.view.isoView.camera.globalToLocal(p);
				//p.y += IsoUtil.TILE_SIZE * (DataManager.getInstance().worldState.roomRect.width);
				tmpmv.x = p.x;
				tmpmv.y = p.y;
				roomUpMvArr.push(tmpmv);
			}
			
			item = new Node(grid.x+xnum, grid.y+ynum);
			//item = nodes[Math.floor(nodes.length / 2)];
			p = NodesUtil.getNodePosition(item);
			//p.y+= DataManager.getInstance().worldState.view.isoView.sceneY+(IsoUtil.TILE_SIZE*(DataManager.getInstance().worldState.roomRect.width));
			//p = new Point( -1, 11);
			p = DataManager.getInstance().worldState.view.isoView.getLayer(0).localToGlobal(p);
			p = DataManager.getInstance().worldState.view.isoView.camera.parent.globalToLocal(p);
			CameraControl.getInstance().centerTweenToPoint(p, DataManager.getInstance().worldState.view.isoView.camera);
			
			setTimeout(startPlay,500);
			
			
		}
		
		private function startPlay():void 
		{
			var tmpmv:McShower;
			for (var k:int = 0; k < roomUpMvArr.length; k++) 
			{
				tmpmv = roomUpMvArr[k];
				setTimeout(tmpmv.startPlay,k*100);
			}
			var feedCommand:FeedControlCommond = new FeedControlCommond();
			feedCommand.isExist(FeedType.CLASSROOMEXTEND);
			
			setTimeout(feedCommand.clickrun,(roomUpMvArr.length-1)*1000);
			
			roomUpMvArr = null;
		}
	}

}