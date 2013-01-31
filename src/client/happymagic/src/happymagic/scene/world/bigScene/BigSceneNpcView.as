package happymagic.scene.world.bigScene 
{
	import com.friendsofed.isometric.Point3D;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import happyfish.cacher.CacheSprite;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.grid.Person;
	import happyfish.scene.world.WorldState;
	import happyfish.scene.world.WorldView;
	import happymagic.scene.world.control.AvatarCommand;
	
	/**
	 * ...
	 * @author jj
	 */
	public class BigSceneNpcView extends Person
	{
		
		public function BigSceneNpcView($data:Object, $worldState:WorldState,__callBack:Function=null) 
		{
			super($data, $worldState, __callBack);
		}
		
		/**
		 * 开始闲逛
		 */
		override public function fiddle():void
		{
			
			var node:Node = _worldState.getCustomOutRoomWalkAbleNode();
			
			var point3d:Point3D = new Point3D(node.x, 0, node.y);
			
			this.addCommand( new AvatarCommand(point3d, fiddleWaitFun));
		}
		
		protected function clickFun(e:MouseEvent):void 
		{
			return;
		}
		
	}

}