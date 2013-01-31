package happymagic.display.view.decorMirrro 
{
	import com.greensock.TweenLite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.grid.SolidObject;
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author slamjj
	 */
	public class MirroMenu extends mirroButton 
	{
		private var target:IsoItem;
		private var hideId:Number;
		
		public function MirroMenu() 
		{
			scaleX = scaleY = 0;
			
			addEventListener(MouseEvent.MOUSE_OVER, overFun);
			addEventListener(MouseEvent.MOUSE_OUT, outFun);
			addEventListener(MouseEvent.CLICK, clickFun);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			if (target) {
				DataManager.getInstance().worldState.world.removeToGrid(target);
				if (target.mirror==0) 
				{
					target.mirror = 1;
					target.setMirro(target.mirror);
					target.rorate(true);
				}else {
					target.mirror = 0;
					target.setMirro(target.mirror);
					target.rorate(true);
				}
				DataManager.getInstance().worldState.world.addToGrid(target);
				
				//标示此地是否可以放置
				target.saveAble = target.positionIsValid();
				
				DataManager.getInstance().recordChangeData(target);
			}
		}
		
		private function outFun(e:MouseEvent):void 
		{
			clearTarget();
		}
		
		private function overFun(e:MouseEvent):void 
		{
			if (hideId) clearTimeout(hideId);
		}
		
		public function setTarget(_target:IsoItem):void {
			if (hideId) clearTimeout(hideId);
			if (_target!=target) 
			{
				scaleX = scaleY = 0;
				TweenLite.to(this, .3, { scaleX:1, scaleY:1 } );
			}
			
			target = _target;
			if (!hasEventListener(Event.ENTER_FRAME)) 
			{
				addEventListener(Event.ENTER_FRAME, followTarget);
			}
			
		}
		
		private function followTarget(e:Event):void 
		{
			x = target.view.container.screenX;
			y = target.view.container.screenY -target.view.container.height;
		}
		
		public function hideMenu():void {
			hideId = 0;
			target = null;
			if (hasEventListener(Event.ENTER_FRAME)) 
			{
				removeEventListener(Event.ENTER_FRAME, followTarget);
			}
			TweenLite.to(this, .3, { scaleX:0, scaleY:0,onComplete:hide_complete } );
			
		}
		
		private function hide_complete():void 
		{
			if (parent) parent.removeChild(this);
		}
		
		public function clearTarget():void {
			if (hideId) clearTimeout(hideId);
			hideId=setTimeout(hideMenu, 500);
		}
	}

}