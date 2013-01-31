package happymagic.model 
{
	import flash.events.Event;
	import flash.events.IOErrorEvent;
	import flash.events.SecurityErrorEvent;
	import flash.net.URLLoader;
	import flash.utils.setTimeout;
	import happyfish.events.UrlConnectEvent;
	import happyfish.manager.local.LocaleWords;
	import happyfish.model.UrlConnecter;
	import happyfish.utils.SysTracer;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class MagicUrlLoader extends UrlConnecter
	{
		
		public function MagicUrlLoader() 
		{
			
		}
		
		override public function load_complete(e:Event):void 
		{
			SysTracer.systrace("load_complete");
			clearListener();
			clearTimer();
			
			if ((e.target as URLLoader).data) 
			{
				data = e.target.data;
			}
			
			if (data == "-100" || data == "系统繁忙或服务器错误，请稍后再试。" || data == "Server Error(-100)" )
			{
				//接口错误
				dispatchError(UrlConnectEvent.SYS_ERROR);
				//显示错误弹出窗
				if (takeError && eventManager) 
				{
					eventManager["showSysMsg"](LocaleWords.getInstance().getWord("UrlConnectError"));
				}
			}else {
				dispatchEvent(e);
			}
			
		}
		
		override protected function load_ioError(e:IOErrorEvent):void 
		{
			super.load_ioError(e);
			
			//接口错误
			dispatchError(UrlConnectEvent.IO_ERROR);
			
			if (takeError && eventManager) 
			{
				eventManager["showSysMsg"](LocaleWords.getInstance().getWord("UrlConnectError"));
			}
		}
		
		override protected function load_SecurityError(e:SecurityErrorEvent):void 
		{
			super.load_SecurityError(e);
			
			if (takeError && eventManager) 
			{
				eventManager["showSysMsg"](LocaleWords.getInstance().getWord("UrlConnectError"));
			}
			
			//接口错误
			dispatchError(UrlConnectEvent.SECURITY_ERROR);
		}
		
		override protected function loadOutTime():void 
		{
			super.loadOutTime();
			
			if (takeError && eventManager) 
			{
				eventManager["showSysMsg"](LocaleWords.getInstance().getWord("UrlConnectError"));
			}
			
			if (retry) 
			{
				setTimeout(load, 500, requestbak);
			}else {
				loader.close();
				dispatchError(UrlConnectEvent.OUT_TIME);
			}
		}
		
	}

}