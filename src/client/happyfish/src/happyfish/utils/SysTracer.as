package happyfish.utils 
{
	import flash.display.Stage;
	import flash.events.KeyboardEvent;
	import flash.text.TextField;
	import flash.ui.Keyboard;
	import happymagic.manager.PublicDomain;
	/**
	 * ...
	 * @author jj
	 */
	public class SysTracer
	{
		public static var _systxt:TextField;
		
		public function SysTracer() 
		{
			
		}
		
		public static function init(stage:Stage):void {
			stage.addEventListener(KeyboardEvent.KEY_DOWN, keydownFun);
		}
		
		static private function keydownFun(e:KeyboardEvent):void 
		{
			if (e.ctrlKey && e.altKey && e.shiftKey && e.keyCode==Keyboard.F12) 
			{
				var txt:TextField = getSystxt();
				txt.visible = !txt.visible;
				txt.parent.addChild(txt);
			}
		}
		
		public static function systrace(...rest):void {
			trace(rest);
			var txt:TextField = getSystxt();
			if (txt) 
			{
				txt.appendText(rest.join(" ") + "\n");
				txt.scrollV = txt.maxScrollH;
			}
			
			
		}
		
		public static function getSystxt():TextField {
			
			if (!_systxt) 
			{
				_systxt = PublicDomain.getInstance().getVar("sysTextField") as TextField;
			}
			
			return _systxt;
		}
		
	}

}