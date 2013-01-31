package happymagic.actModule.signAward.View 
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import happyfish.display.view.UISprite;
	/**
	 * ...
	 * @author zc
	 */
	public class SignAwardFanView extends UISprite
	{
		private var iview:SignAwardFansViewUi;
		
		public function SignAwardFanView() 
		{
			_view = new SignAwardFansViewUi();
			iview = _view as SignAwardFansViewUi;
			iview.addEventListener(MouseEvent.CLICK, clickrun);			
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
			{
				case "closebtn":
				case "closebtn1":
				    closeMe(true);
				break;
			}
		}
		
	}

}