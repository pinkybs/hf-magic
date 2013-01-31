package happymagic.display.view.rehandling 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.view.UISprite;
	import happyfish.manager.module.AlginType;
	import happymagic.command.RehandlingInitCommand;
	import happymagic.command.RehandlingInitstaticCommand;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	/**
	 * ...
	 * @author zc
	 */
	public class RehandlingView extends UISprite
	{
		private var iview:RehandlingViewUi;
		public function RehandlingView() 
		{
			_view = new RehandlingViewUi();
			
			iview = _view as RehandlingViewUi;
			iview.addEventListener(MouseEvent.MOUSE_OVER, mouseover);
			iview.addEventListener(MouseEvent.CLICK, clickrun);			
			iview.addEventListener(MouseEvent.MOUSE_OUT, mouseout);			
			
		}
		
		private function mouseout(e:MouseEvent):void 
		{
			iview.clothes.scaleX = 1.0;
			iview.clothes.scaleY = 1.0;	
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			var rehandlingInitstaticCommand:RehandlingInitstaticCommand = new RehandlingInitstaticCommand();
			rehandlingInitstaticCommand.setData();
			rehandlingInitstaticCommand.addEventListener(Event.COMPLETE, rehandlingInitstaticCommandComplete);
		}
		
		private function rehandlingInitstaticCommandComplete(e:Event):void 
		{
		   e.target.removeEventListener(Event.COMPLETE, rehandlingInitstaticCommandComplete);
		   
		   var rehandlingInitCommand:RehandlingInitCommand = new RehandlingInitCommand();
		   rehandlingInitCommand.setData();
		   rehandlingInitCommand.addEventListener(Event.COMPLETE, rehandlingInitCommandCommandComplete);
		   		   
		}
		
		private function rehandlingInitCommandCommandComplete(e:Event):void 
		{
		   e.target.removeEventListener(Event.COMPLETE, rehandlingInitCommandCommandComplete);
		   
		   var rehandlingInfoView:RehandlingInfoView = DisplayManager.uiSprite.addModule(RehandlingDict.MODULE_REHANDLINGINFO, RehandlingDict.MODULE_REHANDLINGINFO_CLASS, false, AlginType.CENTER, 10, -70) as RehandlingInfoView;					 
		   DisplayManager.uiSprite.setBg(rehandlingInfoView);					 
		   rehandlingInfoView.setData();		   
		}
		
		private function mouseover(e:MouseEvent):void 
		{
			iview.clothes.scaleX = 1.1;
			iview.clothes.scaleY = 1.1;			
		}
		
	}

}