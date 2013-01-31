package happymagic.display.view.worldMap 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import happyfish.display.view.UISprite;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happyfish.utils.display.FiltersDomain;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.worldMap.events.WorldMapEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.SceneVo;
	import happymagic.scene.world.bigScene.BigSceneBg;
	
	/**
	 * ...
	 * @author jj
	 */
	public class WorldMap extends UISprite
	{
		private var iview:worldMapUi;
		private var scenes:Array;
		private var bg:String;
		private var currentSceneId:uint;
		private var sceneIconMc:Sprite;
		private var offX:Number=-294;
		private var offY:Number=-233;
		private var userMsg:WorldMapMsgView;
		private var worldMapSceneIcons:Array;
		private var bgview:BigSceneBg;
		
		public function WorldMap() 
		{
			super();
			
			_view = new worldMapUi();
			
			sceneIconMc = new Sprite();
			_view.addChildAt(sceneIconMc,0);
			
			iview = _view as worldMapUi;
			iview.addEventListener(WorldMapEvent.SCENEICON_CLICK, mouseClickFun,true);
			iview.addEventListener(MouseEvent.CLICK, clickFun,true);
			
		}
		
		override public function init():void 
		{
			setData("worldMapBg.1.bg", DataManager.getInstance().scenes, DataManager.getInstance().currentUser.currentSceneId);
		}
		
		private function mouseClickFun(e:WorldMapEvent):void 
		{
			var info:WorldMapSceneInfoView = 
					DisplayManager.uiSprite.addModule(ModuleDict.MODULE_WORLDMAP_SCENEINFO, ModuleDict.MODULE_WORLDMAP_SCENEINFO_CLASS,false, AlginType.CENTER, 0, -10) as WorldMapSceneInfoView;
			info.setData((e.scene).data ); 
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case iview.closeBtn:
					closeMe(true);
				break;
			}
		}
		
		
		public function setData(_bg:String,_scenes:Array,_currentSceneId:uint):void {
			scenes = DataManager.getInstance().scenes;
			bg = _bg;
			currentSceneId = _currentSceneId;
			
			loadBg();
			
			createSceneIcon();
			
			
		}
		
		private function createSceneIcon():void
		{
			while (sceneIconMc.numChildren>0) 
			{
				sceneIconMc.removeChildAt(0);
			}
			
			worldMapSceneIcons = new Array();
			var tmp:WorldMapSceneIconView;
			for (var i:int = 0; i < scenes.length; i++) 
			{
				//tmp = new WorldMapSceneIconView(scenes[i] as SceneVo,sceneIconMc);
				tmp = new WorldMapSceneIconView(scenes[i] as SceneVo, sceneIconMc, new Point(offX, offY));
				worldMapSceneIcons.push(tmp);
			}
			
			createUserMsg();
		}
		
		private function createUserMsg():void
		{
			if (!userMsg) 
			{
				userMsg = new WorldMapMsgView();
			}
			
			userMsg.setData(DataManager.getInstance().currentUser);
			var curSceneIcon:WorldMapSceneIconView;
			for (var i:int = 0; i < worldMapSceneIcons.length; i++) 
			{
				curSceneIcon = worldMapSceneIcons[i];
				
				if (curSceneIcon.data.sceneId==DataManager.getInstance().currentUser.currentSceneId ) 
				{
					curSceneIcon.icon.filters = [FiltersDomain.yellowGlow];
					var tmprect:Rectangle = curSceneIcon.icon.getRect(view.stage);
					//var tmpP:Point = sceneIconMc.local(new Point(curSceneIcon.icon.x, curSceneIcon.icon.y));
					//tmpP = sceneIconMc.globalToLocal(new Point(tmprect.x, tmprect.y));
					if ((curSceneIcon.icon.x + userMsg.width / 2)>iview.width/2 ) 
					{
						//如果超出右边界,就转到左边
						userMsg.mirro = true;
						userMsg.x = curSceneIcon.icon.x - userMsg.width - 20;
						userMsg.y = curSceneIcon.icon.y - 10;
					}else {
						userMsg.mirro = false;
						userMsg.x = curSceneIcon.icon.x + 20;
						userMsg.y = curSceneIcon.icon.y - 10;
					}
				}
			}
			
			sceneIconMc.addChild(userMsg);
			
		}
		
		private function loadBg():void
		{
			if (!bgview) 
			{
				bgview = new BigSceneBg();
				bgview.x = offX;
				bgview.y = offY;
				bgview.loadClass(bg);
				iview.addChildAt(bgview,0);
			}
			
		}
	}

}