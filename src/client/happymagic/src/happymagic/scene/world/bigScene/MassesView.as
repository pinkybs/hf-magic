package happymagic.scene.world.bigScene 
{
	import com.friendsofed.isometric.Point3D;
	import flash.events.MouseEvent;
	import flash.utils.setTimeout;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.world.grid.Person;
	import happyfish.scene.world.WorldState;
	import happymagic.scene.world.control.AvatarCommand;
	/**
	 * ...
	 * @author jj
	 */
	public class MassesView extends Person
	{
		
		public function MassesView($data:Object, $worldState:WorldState,__callBack:Function=null) 
		{
			super($data, $worldState, __callBack);
			typeName = "Masses";
			
			_drawFrame = 4;
			_speed = 1;
		}
		
		override protected function makeView():IsoSprite 
		{
			super.makeView();
			
			
			view.container.addEventListener(MouseEvent.CLICK, this.onClick);
			
			return this._view;
		}
		
		override protected function view_complete():void 
		{
			
			//view.container.visible = true;
			super.view_complete();
			setTimeout(startFiddle,1000);
			
			//startFiddle();
		}
		
		private function startFiddle():void {
			//view.container.visible = true;
			fiddle();
		}
		
		
		/**
		 * 开始闲逛
		 */
		override public function fiddle():void
		{
			
			var node:Node = _worldState.getCustomOutRoomWalkAbleNode();
			//var node:Node = _worldState.getCustomOutRoomWalkAbleNode();
			
			var point3d:Point3D = new Point3D(node.x, 0, node.y);
			//var point3d:Point3D = new Point3D(36, 0, 36);
			
			this.addCommand( new AvatarCommand(point3d, fiddle_stop));
		}
		
		/**
		 * 闲逛到位后,做出表现,然后再次闲逛
		 */
		public function fiddle_stop():void
		{
			fiddle();
		}
		
	}

}