package happyfish.scene.world.control 
{
	import com.friendsofed.isometric.IsoObject;
	import com.friendsofed.isometric.IsoWorld;
	import flash.events.Event;
	import happyfish.scene.iso.IsoLayer;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.WorldState;
	import happyfish.scene.world.WorldView;
	import happyfish.utils.SysTracer;
	import happymagic.scene.world.MagicState;
	/**
	 * ...
	 * @author jj
	 */
	public class IsoPhysicsControl
	{
		private var state:WorldState;
		private var gravity:Number;
		private var friction:Number;
		private var bounce:Number;
		private var isoObjects:Array;
		private var items:Array;
		
		
		public function IsoPhysicsControl() 
		{
			
		}
		
		/**
		 * 初始化物理控制
		 * @param	_state	
		 * @param	_gravity	重力
		 * @param	_friction	摩擦力
		 * @param	_bounce		反弹
		 */
		public function initPhysics(_state:WorldState,_gravity:Number=1,_friction:Number=.9,_bounce:Number=-.7):void {
			state = _state;
			gravity = _gravity;
			friction = _friction;
			bounce = _bounce;
			
			state.view.addEventListener(Event.ENTER_FRAME, checkPhysics);
			
		}
		
		private function checkPhysics(e:Event):void 
		{
			//return;
			
			//var num:uint;
			var tmp:IsoItem;
			if (!items) 
			{
				items = (state.view.isoView.getLayer(WorldView.LAYER_FLYING) as IsoLayer).objects;
			}
			
			for (var i:int = 0; i < items.length; i++) 
			{
				tmp = (items[i] as IsoSprite).isoItem;
				if (tmp) 
				{
					if (tmp.physics) 
					{
						//num++;
						physicsFun(tmp);
					}
				}
			}
			//SysTracer.systrace("physics:",num);
		}
		
		public function physicsFun(target:IsoItem):void {
			var box:IsoObject = target.view.container;
			if (box.vx<.01 && box.vy<.01 && box.vz<.01 && box.y==0) 
			{
				//行动停止了
				target.landed();
				return;
			}
			box.vy += gravity;//重力加速度
			box.x += box.vx;
			box.y += box.vy;
			box.z += box.vz;
			
			//反弹
			//if(box.x > (state.grid.numCols-1)*IsoUtil.TILE_SIZE)
			//{
				//box.x = (state.grid.numCols-1)*IsoUtil.TILE_SIZE;
				//box.vx *= bounce;//反弹
			//}
			//else if(box.x < 0)
			//{
				//box.x = 0;
				//box.vx *= bounce;
			//}
			//if(box.z > (state.grid.numRows-1)*IsoUtil.TILE_SIZE)
			//{
				//box.z = (state.grid.numRows-1)*IsoUtil.TILE_SIZE;
				//box.vz *= bounce;
			//}
			//else if(box.z < 0)
			//{
				//box.z = 0;
				//box.vz *= bounce;
			//}
			
			//高度上的反弹
			if(box.y > 0)
			{
				box.y = 0;
				box.vy *= bounce;
			}
			
			//摩擦力
			box.vx *= friction;
			box.vy *= friction;
			box.vz *= friction;
			
			//if (box.vx<.2 && box.vy<.2 && box.vz<.2 && box.y==0) 
			//{
				//行动停止了
				//target.landed();
				//
				//box.vx = box.vy = box.vz = box.y = 0;
			//}
			/*//影子坐标同步
			shadow.x = box.x;
			shadow.z = box.z;
			
			filter.blurX = filter.blurY = -box.y * .25;
			shadow.filters = [filter];*/
		}
		
	}

}