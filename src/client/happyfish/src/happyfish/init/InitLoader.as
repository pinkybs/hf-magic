package happyfish.init 
{
	import br.com.stimuli.loading.BulkLoader;
	import br.com.stimuli.loading.BulkProgressEvent;
	import br.com.stimuli.loading.loadingtypes.LoadingItem;
	import com.brokenfunction.json.decodeJson;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.display.StageAlign;
	import flash.display.StageScaleMode;
	import flash.events.Event;
	import flash.net.URLRequest;
	import flash.system.ApplicationDomain;
	import flash.system.LoaderContext;
	import flash.system.Security;
	import flash.text.TextField;
	import flash.ui.ContextMenu;
	import flash.ui.ContextMenuItem;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.model.SwfLoader;
	import happyfish.model.UrlConnecter;
	import happyfish.utils.SysTracer;
	import happymagic.manager.PublicDomain;
	
	/**
	 * 初始化外壳文件基类
	 * 	流程:
	 * 		1. addToStage				新项目要实现
	 * 		2. loadWords				加载语言包
	 * 		3. loadWords_complete		语言包加载完成
	 * 		4. loadInitUi				加载loading时的UI	
	 * 		5. loadInitUi_complete		loading时ui加载完成,子类可实现: loadingUi的创建,第一次进游戏时的动画或人物创建之类流程,最后进入初始数据加载
	 * 		6. loadInitData				初始数据加载
	 * 		7. loadInitData_complete	初始数据加载完成,创建初始加载文件列表,记录接口地址域名前缀,子类实现: 调用loadInitSwfs
	 * 		8. loadInitSwfs				加载初始素材文件
	 * 		9. loadInitSwfs_progress	加载过程中的处理事件
	 * 		10. loadInitSwfs_complete	加载完成,子类实现: 调用initMainByClass
	 * 		11. initMainByClass			记录外壳文件clear文件到PublicDomain内,子类实现:创建主应用类实例,并加入场景
	 * 		12. clearLoader				子类实现: 清除外壳文件内不必要的数据,停上片头动画等
	 * 
	 * @author slamjj
	 */
	public class InitLoader extends Sprite
	{
		private var firstLoadingTxt:TextField;
		private var cleared:Boolean;
		
		//初始化参数
		//版本
		public var ver:String;
		//平台
		public var snsType:String;
		//是否DEBUG模式
		public var debug:Boolean;
		//是否本地
		public var isLocal:Boolean;
		//接口域名
		public var interfaceHost:String;
		//SWF文件地址域名
		public var staticHost:String;
		//语言包地址
		public var localWords:String;
		//初始化接口
		public var initInterface:String;
		//LOADING时显示动画
		public var initUi:String;
		//初始数据
		public var initData:Object;
		//当前加载百分比
		public var swfLoadedPer:uint;
		
		//当前loading的百分比
		protected var currentLoadingPer:Number;
		
		//loading时显示的动画
		protected var loadingMovie:MovieClip;
		
		//urlConecter类扩展类变量,各项目会不同,但基类都是UrlConecter类
		protected var urlLoaderClass:Class;
		
		//加载时显示的tips数组json
		public var tipsStr:String;
		public var tipsArr:Array;
		
		//********************
		
		public function InitLoader() 
		{
			
			addEventListener(Event.ADDED_TO_STAGE, addToStage);
			
			//Security.allowDomain("*");
			
		}
		
		protected function addToStage(e:Event):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, addToStage);
			
			//场景对齐方式和不缩放设置
			stage.align = StageAlign.TOP_LEFT;
			stage.scaleMode = StageScaleMode.NO_SCALE;
			
			//清除右键菜单
			var myMenu:ContextMenu = new ContextMenu();
			myMenu.hideBuiltInItems();
			if (loaderInfo.parameters["ver"]) 
			{
				myMenu.customItems.push(new ContextMenuItem("ver:"+loaderInfo.parameters["ver"]));
			}
			parent.contextMenu = myMenu;
			
			//准备好loaderContent
			PublicDomain.getInstance().loaderContext = new LoaderContext(false, ApplicationDomain.currentDomain);
			PublicDomain.getInstance().appDomain = ApplicationDomain.currentDomain;
			//TODO 实现定义统一的loader里laoderContent
			
			//创建系统调试信息消息框
			firstLoadingTxt = new TextField();
			//firstLoadingTxt.mouseEnabled = false;
			firstLoadingTxt.width = 500;
			firstLoadingTxt.height = 400;
			firstLoadingTxt.background = true;
			
			firstLoadingTxt.x = stage.stageWidth / 2 - firstLoadingTxt.width / 2;
			firstLoadingTxt.y = stage.stageHeight / 2 - firstLoadingTxt.height / 2;
			
			firstLoadingTxt.visible = false;
			addChild(firstLoadingTxt);
			
			PublicDomain.getInstance().setVar("sysTextField", firstLoadingTxt);
			
			//systracer的初始化
			SysTracer.init(stage);
			
			//获取初始化信息
			for (var name:String in loaderInfo.parameters) 
			{
				if (this.hasOwnProperty(name)) 
				{
					this[name] = loaderInfo.parameters[name];
				}
			}
			
			//设置SwfLoader内默认应用域
			SwfLoader.getInstance().loaderContext = PublicDomain.getInstance().loaderContext;	
			
			//TODO 调用loadWords
		}
		
		protected function loadWords(wordUrl:String):void {
			var wordsLoader:UrlConnecter = new urlLoaderClass() as UrlConnecter;
			wordsLoader.addEventListener(Event.COMPLETE, loadWords_complete);
			trace("wordUrl:",wordUrl);
			trace("local:",loaderInfo.url);
			wordsLoader.load(new URLRequest(wordUrl));
		}
		
		protected function loadWords_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, loadWords_complete);
			//trace(e.target.data);
			
			//TODO 子类实现语言包的存储
			
			//子类实现下一步动作
			//一般就是加载initUi
		}
		
		/**
		 * 显示tips
		 */
		protected function showTips():void {
			
		}
		
		/**
		 * 开始加载Ui
		 */
		protected function loadInitUi(uiUrl:String):void
		{
			var loader:LoadingItem = SwfLoader.getInstance().add(uiUrl);
			loader.addEventListener(Event.COMPLETE, loadInitUi_complete);
		}
		
		protected function loadInitUi_complete(e:Event=null):void 
		{
			if(e) e.target.removeEventListener(Event.COMPLETE, loadInitUi_complete);
			
			//TODO 子类实现下一步动作
		}
		
		protected function loadInitData(initUrl:String):void {
			var initLoader:UrlConnecter = new UrlConnecter() as UrlConnecter;
			initLoader.addEventListener(Event.COMPLETE, loadInitData_complete);
			initLoader.load(new URLRequest(initUrl));
		}
		
		protected function loadInitData_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, loadInitData_complete);
			
			initData = decodeJson(e.target.data);
			
			//初始加载列表
			for (var i:int = 0; i < initData.initSwf.length; i++) 
			{
				initData.initSwf[i] = staticHost + initData.initSwf[i];
			}
			
			
			//TODO 子类实现下一步动作
		}
		
		protected function loadInitSwfs(urls:Array):void {
			var initLoader:BulkLoader = SwfLoader.getInstance().addGroup("loadInitSwfs", urls);
			initLoader.addEventListener(BulkProgressEvent.COMPLETE, loadInitSwfs_complete);
			initLoader.addEventListener(BulkProgressEvent.PROGRESS, loadInitSwfs_progress);
		}
		
		protected function loadInitSwfs_progress(e:BulkProgressEvent):void 
		{
			swfLoadedPer = Math.floor(e.weightPercent * 100);
		}
		
		protected function loadInitSwfs_complete(e:BulkProgressEvent):void 
		{
			e.target.removeEventListener(BulkProgressEvent.COMPLETE, loadInitSwfs_complete);
			e.target.removeEventListener(BulkProgressEvent.PROGRESS, loadInitSwfs_progress);
			
			//TODO 子类实现下一步动作
			
		}
		
		protected function initMainByClass(className:String):void {
			
			PublicDomain.getInstance().setVar("clearLoader", clearLoader);
			
			//结束loader
			//clearLoader();
		}
		
		/**
		 * 结束initLoader的活动
		 */
		public function clearLoader():void {
			if (cleared) 
			{
				return;
			}
			cleared = true;
		}
		
	}

}