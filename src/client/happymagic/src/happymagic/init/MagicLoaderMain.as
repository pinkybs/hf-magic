package happymagic.init
{
	import br.com.stimuli.loading.BulkProgressEvent;
	import br.com.stimuli.loading.loadingtypes.LoadingItem;
	import com.adobe.serialization.json.JSON;
	import com.brokenfunction.json.decodeJson;
	import com.greensock.TweenLite;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.external.ExternalInterface;
	import flash.text.TextField;
	import happyfish.cacher.SwfClassCache;
	import happyfish.init.InitLoader;
	import happyfish.init.LoadingTipsControl;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.SwfURLManager;
	import happyfish.model.SwfLoader;
	import happyfish.utils.HtmlTextTools;
	import happymagic.display.view.PianTouView;
	import happymagic.manager.PublicDomain;
	import happymagic.model.MagicUrlLoader;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class MagicLoaderMain extends InitLoader
	{
		public var loadingMc:loadingUi;
		
		public var swfLoaded:Boolean;
		public var createPlayerFinished:Boolean;
		
		//tips controler
		public var tipsControl:LoadingTipsControl;
		
		public var createModule:String;
		public var createUrl:String;
		public var piantou:String;
		public var pianTouView:PianTouView;
		public var createLoadMc:Sprite;
		public var pianTouReady:Boolean;
		
		public function MagicLoaderMain():void 
		{
			
		}
		
		override protected function addToStage(e:Event):void 
		{
			super.addToStage(e);
			
			//默认字体与字号设置
			HtmlTextTools.defaultFont = "宋体";
			HtmlTextTools.defaultSize = 12;
			
			//定义数据请求类
			urlLoaderClass = MagicUrlLoader;
			
			start();
		}
		
		public function start():void {
			
			//临时测试用的修改数据
			//isLocal = true;
			
			if (isLocal) 
			{
				initInterface = "data/initData.txt";
				localWords = "data/localeWord.txt";
				initUi = "loading1.swf";
				//initInterface = "data/initData.txt";
				//initInterface = "data/initData_renren2.txt";
				//createModule = "createPlayer.swf";
				//createUrl = "";
			}
			
			PublicDomain.getInstance().ver = ver;
			PublicDomain.getInstance().debug = debug;
			PublicDomain.getInstance().isLocal = isLocal;
			PublicDomain.getInstance().snsType = snsType;
			PublicDomain.getInstance().initUi = initUi;
			PublicDomain.getInstance().createModule = createModule;
			PublicDomain.getInstance().createUrl = createUrl;
			PublicDomain.getInstance().piantou = piantou;	
			
			//创建loading界面
			loadingMc = new loadingUi();
			loadingMc.x = stage.stageWidth / 2;
			loadingMc.y = stage.stageHeight / 2;
			//设置当前加载百分比
			currentLoadingPer = 1;
			
			//控制loading进度条的表现
			loadingMc.scrollMc.gotoAndStop(1);
			loadingMc.scrollMc.addEventListener(Event.ENTER_FRAME, checkLoadingPer);
			addChild(loadingMc);
			
			//加载tips
			//tips整理
			if (!tipsStr) 
			{
				tipsStr = "变化咒可以对你和好友使用,但对学生无效||在魔法小屋中多摆放一些家具,会增加魔法值上限||魔法上限越高,回复的点数也越多||多和村民们聊聊,可以获得很多有用的信息||水晶是合成魔法的基础材料，可以在商店中购买获得||在使用合成魔法时，如果发现某个材料不足，直接点击他就可购买了||小屋是通过添置家具增加魔法值上限来升级的||长时间不收取学费，学费就会变成石头哦||使用食品和饮料可以恢复魔法值||在魔法书中可以学到更多的魔法，越高级的魔法收入就越多||小屋每升一级都会吸引新的学生来你的教室学习魔法||一个好汉三个帮，拥有越多的好友，就能得到越多的帮助哦||使用不同的变化咒可以获得不同的特殊材料，所以......多多变化吧！||魔法师乐乐是伟大的大魔导师奥兹巴的爱徒，她为什么主动来帮助我们呢？||全屏游戏可以看到更多的景色||完成任务可以获得更多的经验和奖励||迎接新学生钱，请先把空位上的学费收走";
			}
			
			tipsArr = tipsStr.split("||");
			
			//加载语言包
			loadWords(localWords);
		}
		
		private function checkLoadingPer(e:Event):void 
		{
			if (loadingMc.scrollMc.currentFrame<currentLoadingPer) 
			{
				loadingMc.scrollMc.nextFrame();
			}
		}
		
		override protected function showTips():void 
		{
			super.showTips();
			
			tipsControl = new LoadingTipsControl(loadingMc.tipsTxt, tipsArr);
			tipsControl.start();
		}
		
		override protected function loadWords(wordUrl:String):void 
		{
			super.loadWords(wordUrl);
			
			//加载百分比
			currentLoadingPer = 10;
		}
		
		override protected function loadWords_complete(e:Event):void 
		{
			super.loadWords_complete(e);
			
			var words:Object = decodeJson(e.target.data);
			LocaleWords.getInstance().setValue(words);
			
			currentLoadingPer = 15;
			
			//加载片头
			loadInitUi(initUi);
			
			//开始显示tips
			showTips();
		}
		
		override protected function loadInitUi(uiUrl:String):void 
		{
			if (uiUrl) 
			{
				loadingStr = LocaleWords.getInstance().getWord("loadingState2");
				super.loadInitUi(uiUrl);
			}else {
				loadInitUi_complete();
			}
		}
		
		override protected function loadInitUi_complete(e:Event=null):void 
		{
			
			super.loadInitUi_complete(e);
			
			currentLoadingPer = 20;
			
			//播放片头
			loadingMovie = (e.target as LoadingItem).content;
			loadingMovie.x = -loadingMovie.width / 2;
			loadingMovie.y = -loadingMovie.height / 2;
			loadingMovie.addEventListener(Event.ENTER_FRAME, checkLoadingPlay);
			loadingMc.addChildAt(loadingMovie,1);
			
		}
		
		private function checkLoadingPlay(e:Event):void 
		{
			if ((e.target as MovieClip).currentFrame==(e.target as MovieClip).totalFrames) 
			{
				e.target.removeEventListener(Event.ENTER_FRAME, checkLoadingPlay);
				if (piantou) 
				{
					loadPianTou();
				}else if (createModule) 
				{
					loadCreatePlayer();
				}else {
					createPlayerFinished = true;
					//开始加载配置文件
					loadingStr = LocaleWords.getInstance().getWord("loadingState2");
					loadInitData(initInterface);
				}
			}
		}
		
		private function loadPianTou():void
		{
			var loader:LoadingItem = SwfLoader.getInstance().add(piantou);
			loader.addEventListener(Event.COMPLETE, loadPianTou_complete);
		}
		
		private function loadPianTou_complete(e:Event):void 
		{
			pianTouView = new PianTouView((e.target as LoadingItem).content);
			addChild(pianTouView);
			pianTouView.addEventListener(Event.COMPLETE, pianTou_complete);
			
			//关闭页面loading
			if (ExternalInterface.available) 
			{
				ExternalInterface.call("hideLoading");
			}
			
			if (createModule) 
			{
				loadCreatePlayer();
			}else {
				createPlayerFinished = true;
				//开始加载配置文件
				loadingStr = LocaleWords.getInstance().getWord("loadingState2");
				loadInitData(initInterface);
			}
		}
		
		private function pianTou_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, pianTou_complete);
			pianTouReady = true;
			startCreatePlayer();
		}
		
		private function startCreatePlayer():void
		{
			if (pianTouReady) 
			{
				addChild(createLoadMc);
			}
		}
		
		
		private function loadCreatePlayer():void
		{
			loadingStr = LocaleWords.getInstance().getWord("loadingState3");
			
			var loader:LoadingItem = SwfLoader.getInstance().load(createModule);
			loader.addEventListener(Event.COMPLETE, loadCreatePlayer_complete);
		}
		
		private function loadCreatePlayer_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, loadCreatePlayer_complete);
			
			createLoadMc = (e.target as LoadingItem).content;
			createLoadMc.addEventListener(Event.COMPLETE, createPlayer_finished);
			startCreatePlayer();
			
			//继续加载数据
			loadInitData(initInterface);
		}
		
		private function createPlayer_finished(e:Event):void 
		{
			createPlayerFinished = true;
			checkIntoInitMain();
		}
		
		override protected function loadInitData_complete(e:Event):void 
		{
			super.loadInitData_complete(e);
			
			//设置接口配置
			InterfaceURLManager.getInstance().staticHost = staticHost;
			InterfaceURLManager.getInstance().interfaceHost = interfaceHost;
			InterfaceURLManager.getInstance().urls = initData.interfaces;
			
			//配置swf文件列表
			SwfURLManager.getInstance().setValue(initData.otherSwf);
			
			//游戏数据
			PublicDomain.getInstance().setVar("gameData", initData.gameData);
			
			//背景音乐
			PublicDomain.getInstance().setVar("bgMusic", initData.bgMusic);
			
			//module列表
			PublicDomain.getInstance().setVar("moduleData", initData.modules);
			
			currentLoadingPer = 40;
			
			loadingStr = LocaleWords.getInstance().getWord("loadingState4");
			//加载所有初始素材文件
			loadInitSwfs(initData.initSwf);
		}
		
		override protected function loadInitSwfs_progress(e:BulkProgressEvent):void 
		{
			//super.loadInitSwfs_progress(e);
			
			currentLoadingPer = 40 + Math.floor(e.weightPercent * 60);
			
		}
		
		override protected function loadInitSwfs_complete(e:BulkProgressEvent):void 
		{
			super.loadInitSwfs_complete(e);
			
			currentLoadingPer = 100;
			
			loadingStr = LocaleWords.getInstance().getWord("loadingState5");
			
			swfLoaded = true;
			
			checkIntoInitMain();
		}
		
		/**
		 * 检查是否可进入主程初始化
		 */
		private function checkIntoInitMain():void {
			if (swfLoaded && createPlayerFinished) 
			{
				//初始化主模块
				initMainByClass(initData.mainClass);
			}
		}
		
		override protected function initMainByClass(className:String):void 
		{
			super.initMainByClass(className);
			
			var mainClass:Class = SwfClassCache.getInstance().getClass(className);
			
			var main:Sprite = new mainClass() as Sprite;
			
			addChild(main);
		}
		
		public function set loadingStr(value:String):void {
			loadingMc.loadingTxt.text = value;
		}
		
		override public function clearLoader():void 
		{
			super.clearLoader();
			
			//关闭loading动画
			loadingMc.scrollMc.removeEventListener(Event.ENTER_FRAME, checkLoadingPer);
			loadingMc.parent.removeChild(loadingMc);
			loadingMc = null;
			
			//关闭tips显示
			tipsControl.stop();
			tipsControl = null;
			
		}
	}
	
}