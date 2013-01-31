package happymagic.display.view.ui.personMsg 
{
	import flash.events.Event;
	import flash.text.TextFieldAutoSize;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	import happyfish.scene.world.grid.IsoItem;
	/**
	 * ...
	 * @author jj
	 */
	public class PersonMsgView extends personMsgUi
	{
		private var target:IsoItem;
		private var closeId:uint;
		private var hasInit:Boolean;
		private var callback:Function;
		
		public function PersonMsgView(_target:IsoItem,str:String,__time:uint,_callback:Function=null) 
		{
			callback = _callback;
			mouseChildren = false;
			mouseEnabled = false;
			
			target = _target;
			txt.autoSize = TextFieldAutoSize.LEFT;
			txt.wordWrap = true;
			
			bg.visible = false;
			setData(str,__time,callback);
			
			
		}
		
		public function setData(value:String, time:uint,_callback:Function=null):void {
			if (!hasInit) {
				y = - target.view.container.height+25;
				hasInit = true;
			}
			callback = _callback;
			txt.htmlText = value;
			
			
			if (closeId) 
			{
				clearTimeout(closeId);
				closeId = 0;
			}
			closeId = setTimeout(closeMe, time);
			
			target.view.container.addChild(this);
			
			txtChange();
		}
		
		private function txtChange(e:Event=null):void 
		{
			var textWidth:Number = Math.max(txt.textWidth,txt.width);
			txt.x = -textWidth / 2;
			txt.y = -txt.textHeight - 20;
			
			bg.width = txt.textWidth + 30;
			bg.height = txt.textHeight + 30;
			//bg.x = txt.x + txt.textWidth/2 - 4;
			bg.visible = true;
			
			//x = target.view.container.x;
			
			
			
		}
		
		public function closeMe():void
		{
			closeId = 0;
			
			if (parent) 
			{
				parent.removeChild(this);
			}
			
			if (callback!=null) 
			{
				callback.apply();
			}
			
			PersonMsgManager.getInstance().delMsg(target.view.name);
		}
		
		
		
	}

}