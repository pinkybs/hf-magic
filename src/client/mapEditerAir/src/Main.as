package 
{
	import br.com.stimuli.loading.BulkLoader;
	import br.com.stimuli.loading.loadingtypes.LoadingItem;
	import com.adobe.serialization.json.JSON;
	import com.brokenfunction.json.decodeJson;
	import flash.display.Sprite;
	import flash.display.StageAlign;
	import flash.display.StageScaleMode;
	import flash.events.Event;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.system.ApplicationDomain;
	import flash.system.LoaderContext;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.manager.SwfURLManager;
	import happyfish.model.SwfLoader;
	import happyfish.scene.iso.IsoUtil;
	
	/**
	 * ...
	 * @author jj
	 */
	public class Main extends Sprite 
	{
		public var gridView:GridWorld;
		public var data:Object;
		public var editer:EditerView;
		public var mapSprite:Sprite;
		public var mapview:MapView;
		
		public function Main():void 
		{
			stage.align = StageAlign.TOP_LEFT;
			stage.scaleMode = StageScaleMode.NO_SCALE;
			
			SwfURLManager.getInstance().setValue([]);
			InterfaceURLManager.getInstance().staticHost = "";
			
			mapSprite = new Sprite();
			addChild(mapSprite);
			editer = new EditerView(this);
			addChild(editer);
			
			mapview = new MapView(this);
			mapSprite.addChild(mapview);
			
			gridView = new GridWorld();
			mapSprite.addChild(gridView);
			
			var control:EditerControl = new EditerControl(this);
			
			SwfLoader.getInstance().loaderContext = new LoaderContext(false, ApplicationDomain.currentDomain);
			
			
			loadData();
		}
		
		private function loadData():void
		{
			var loader:URLLoader = new URLLoader(new URLRequest("data.txt"));
			loader.addEventListener(Event.COMPLETE, loadData_complete);
		}
		
		private function loadData_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, loadData_complete);
			
			data = decodeJson(e.target.data);
			
			editer.setMapList(data);
			
			loadBgSwf();
		}
		
		
		
		public function loadBgSwf():void
		{
			var loader:BulkLoader = SwfLoader.getInstance().addGroup("loadSwf", data.bgswfs);
			loader.addEventListener(Event.COMPLETE, loadBgSwf_complete);
		}
		
		private function loadBgSwf_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, loadBgSwf_complete);
			
			editer.addStr("文件加载完成");
		}
	}
	
}