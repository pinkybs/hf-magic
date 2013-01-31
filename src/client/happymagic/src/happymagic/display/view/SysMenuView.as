package happymagic.display.view 
{
	import flash.display.MovieClip;
	import flash.display.StageDisplayState;
	import flash.events.MouseEvent;
	import happyfish.display.view.UISprite;
	import happyfish.manager.BgMusicManager;
	import happyfish.manager.ShareObjectManager;
	import happyfish.manager.SoundEffectManager;
	/**
	 * ...
	 * @author jj
	 */
	public class SysMenuView extends UISprite
	{
		private var iview:sysMenuUi;
		private var fullScreen:Boolean;
		
		public function SysMenuView() 
		{
			super();
			
			iview = new sysMenuUi();
			_view=iview as MovieClip;
			
			
			iview.addEventListener(MouseEvent.CLICK, clickFun, true);
			
		}
		
		override public function init():void 
		{
			iview.bgSoundCloseBtn.visible = ShareObjectManager.getInstance().bgSound;
			iview.bgSoundOpenBtn.visible = !ShareObjectManager.getInstance().bgSound;
			
			iview.soundCloseBtn.visible = ShareObjectManager.getInstance().soundEffect;
			iview.soundOpenBtn.visible = !ShareObjectManager.getInstance().soundEffect;
			
			iview.unFullSceenBtn.visible = (iview.stage.displayState == StageDisplayState.FULL_SCREEN);
			iview.fullSceenBtn.visible = !(iview.stage.displayState == StageDisplayState.FULL_SCREEN);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case iview.bgSoundOpenBtn:
					ShareObjectManager.getInstance().bgSound = true;
					BgMusicManager.getInstance().soundFlag = true;
				break;
				
				case iview.bgSoundCloseBtn:
					ShareObjectManager.getInstance().bgSound = false;
					BgMusicManager.getInstance().soundFlag = false;
				break;
				
				case iview.soundOpenBtn:
					ShareObjectManager.getInstance().soundEffect = true;
				break;
				
				case iview.soundCloseBtn:
					ShareObjectManager.getInstance().soundEffect = false;
				break;
				
				case iview.fullSceenBtn:
					iview.stage.displayState = StageDisplayState.FULL_SCREEN;
					fullScreen = true;
				break;
				
				case iview.unFullSceenBtn:
					iview.stage.displayState = StageDisplayState.NORMAL;
					fullScreen = false;
				break;
			}
			init();
		}
		
	}

}