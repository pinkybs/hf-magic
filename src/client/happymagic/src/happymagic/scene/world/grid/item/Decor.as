package happymagic.scene.world.grid.item 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.events.GameMouseEvent;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.grid.SolidObject;
	import happyfish.scene.world.WorldState;
	import happymagic.model.vo.DecorVo;
	
	/**
	 * ...
	 * @author Beck Xu
	 */
	public class Decor extends SolidObject
	{
		public var decorVo:DecorVo;
		public function Decor($data:Object, $worldState:WorldState,__callBack:Function=null) 
		{
			_bodyCompleteCallBack = __callBack;
			super($data, $worldState);
			decorVo = $data as DecorVo;
			typeName = "Decor";
			//鼠标事件
            view.container.addEventListener(MouseEvent.ROLL_OVER, this.onMouseOver);
            view.container.addEventListener(MouseEvent.ROLL_OUT, this.onMouseOut);
            view.container.addEventListener(MouseEvent.MOUSE_MOVE, this.onMouseOverMove);
            view.container.addEventListener(MouseEvent.CLICK, this.onClick);
		}
		
		//override protected function view_complete():void 
		//{
			//super.view_complete();
			//
			//
			//view.container.y = -150;
			//view.container.vy = -500;
			//view.container.physics = true;
			//_worldState.physicsControl.physicsFun(this);
			//_worldState.view.addIsoChild(view);
		//}
	}

}