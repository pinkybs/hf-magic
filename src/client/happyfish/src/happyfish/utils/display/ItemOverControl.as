package happyfish.utils.display 
{
	import com.greensock.TweenLite;
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.filters.GlowFilter;
	import flash.utils.Dictionary;
	
	/**
	 * ...
	 * @author jj
	 */
	public class ItemOverControl 
	{
		
		public function ItemOverControl(access:Private) 
		{
			
			if (access != null)
			{	
				if (instance == null)
				{				
					instance = this;
					targetList = new Dictionary();
				}
			}
			else
			{	
				throw new Error( "ItemOverControl"+"单例" );
			}
		}
		
		public static function getInstance():ItemOverControl
		{
			if (instance == null)
			{
				instance = new ItemOverControl( new Private() );
			}
			return instance;
		}
		
		/**
		 * 侦听target的over事件
		 * @param	target
		 * @param	over	over时调用的方法 需MouseEvent参数
		 * @param	out		out时调用的方法 需MouseEvent参数
		 * @param	parmas	是否调用回调时带上mouseEvent参数
		 */
		public function addOverItem(target:DisplayObjectContainer, over:Function = null, out:Function = null,params:Boolean=false):void {
			targetList[target] = { x:target.x, y:target.y,over:over,out:out,params:params,hasRecord:false };
			target.addEventListener(MouseEvent.MOUSE_OVER, overFun);
			target.addEventListener(MouseEvent.MOUSE_OUT, outFun);
			target.addEventListener(Event.REMOVED_FROM_STAGE, removeFromStageFun);
		}
		
		private function removeFromStageFun(e:Event):void 
		{
			removeOverItem(e.target as DisplayObjectContainer);
		}
		
		
        public function hideGlow(e:MouseEvent) : void
        {
            e.target.filters = [];
            return;
        }
		
        public function showGlow(e:MouseEvent) : void
        {
            e.target.filters = [new GlowFilter(16776960, 1, 5, 5, 10, 1, false, false)];
            return;
        }
		
		private function outFun(e:MouseEvent):void 
		{
			if (targetList[e.target].out) 
			{
				if (targetList[e.target].params) 
				{
					(targetList[e.target].out as Function).call(null,e);
				}else {
					targetList[e.target].out.call();
				}
			}else {
				TweenLite.killTweensOf(e.target);
				TweenLite.to(e.target, .3, { y: targetList[e.target].y } );
			}
			
		}
		
		private function overFun(e:MouseEvent):void 
		{
			if (targetList[e.target].over) 
			{
				if (targetList[e.target].params) 
				{
					(targetList[e.target].over as Function).call(null,e);
				}else {
					targetList[e.target].over.call();
				}
				
			}else {
				if (!targetList[e.target].hasRecord) {
					targetList[e.target].y = e.target.y;
					targetList[e.target].hasRecord = true;
				}
				TweenLite.killTweensOf(e.target);
				TweenLite.to(e.target, .3, { y: targetList[e.target].y - 6 } );
			}
			
		}
		
		public function removeOverItem(target:DisplayObjectContainer):void {
			target.removeEventListener(MouseEvent.MOUSE_OVER, overFun);
			target.removeEventListener(MouseEvent.MOUSE_OUT, outFun);
			target.removeEventListener(Event.REMOVED_FROM_STAGE, removeFromStageFun);
			
			delete targetList[target];
		}
		
		
		private static var instance:ItemOverControl;
		private var targetList:Dictionary;
		
	}
	
}
class Private {}