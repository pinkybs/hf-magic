package happymagic.display.view.worldMap 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import happyfish.display.view.IconView;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.utils.display.BtnStateControl;
	import happyfish.utils.display.FiltersDomain;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.display.view.worldMap.events.WorldMapEvent;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.SceneState;
	import happymagic.model.vo.SceneVo;
	/**
	 * ...
	 * @author jj
	 */
	public class WorldMapSceneIconView
	{
		public var icon:IconView;
		private var container:DisplayObjectContainer;
		private var offPoint:Point;
		public var data:SceneVo;
		
		public function WorldMapSceneIconView(_sceneVo:SceneVo,_container:DisplayObjectContainer,_offPoint:Point=null) 
		{
			if (_offPoint) 
			{
				offPoint = _offPoint;
			}else {
				offPoint = new Point();
			}
			
			data = _sceneVo;
			container = _container;
			
			loadIcon();
			
		}
		
		public function loadIcon():void {
			icon = new IconView();
			icon.setData(data.className);
			icon.x = data.x+offPoint.x;
			icon.y = data.y+offPoint.y;
			container.addChild(icon);
			
			if (data.state == SceneState.UNSHOW) {
				BtnStateControl.setBtnState(icon, false);
				var unLockIcon:MovieClip = new sceneUnlockIcon();
				unLockIcon.x = icon.x;
				unLockIcon.y = icon.y-30;
				container.addChild(unLockIcon);
			}
			
			icon.addEventListener(MouseEvent.MOUSE_OVER, overFun);
			icon.addEventListener(MouseEvent.MOUSE_OUT, outFun);
			icon.addEventListener(MouseEvent.CLICK, clickFun);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			//排除新手村(默认场景)
			//if (data.sceneId!=1000001) 
			//{
				//icon.dispatchEvent(new WorldMapEvent(WorldMapEvent.SCENEICON_CLICK,this));
			//}else {
				//EventManager.getInstance().showPiaoStr(PiaoMsgType.TYPE_BAD_STRING, LocaleWords.getInstance().getWord("xinshoucuen"));
			//}
			icon.dispatchEvent(new WorldMapEvent(WorldMapEvent.SCENEICON_CLICK,this));
		}
		
		private function outFun(e:MouseEvent):void 
		{
			icon.filters = [];
		}
		
		private function overFun(e:MouseEvent):void 
		{
			icon.filters = [FiltersDomain.yellowGlow];
		}
		
	}

}