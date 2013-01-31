package happyfish.cacher 
{
	import flash.geom.Rectangle;
	import flash.utils.getQualifiedClassName;
	import happyfish.cacher.bitmapMc.cacher.BitmapCacher;
	import happyfish.cacher.bitmapMc.display.BitmapMovieMc;
	import happyfish.cacher.bitmapMc.events.BitmapCacherEvent;
	import flash.display.Sprite;
	import flash.events.Event;
	import happyfish.events.SwfClassCacheEvent;
	import happyfish.manager.InterfaceURLManager;
	/**
	 * 位图缓存的显示对象
	 * @author Beck
	 * 
	 */
	public class CacheSprite extends Sprite
	{
		private var _className:String;
		private var mc_class:Class;
		public var bitmap_movie_mc:BitmapMovieMc;
		public static const DEBUG:Boolean = true;
		public var cache:ClassCache;
		
		//加载开关,类只响就加载完成一次,加载完成后标示不再响应同样的类加载返回事件
		private var single_flg:Boolean = true;
		private var bitmap_mc_single_flg:Boolean = true;
		
		private var scale_flg:Boolean = false;
		
		private var deafult_play_flg:Boolean = false;
		
		private var _scaleWidth:int;
		private var _scaleHeight:int;
		
		private var _callback:String;
		private var _param:*;
		private var drawFrame:uint;
		public var bodyComplete_callback:Function;
		public var bodyComplete_params:Array;
		
		/**
		 * 
		 * @param	$deafult_play_flg
		 * @param	_drawFrame	播放速度帧数
		 */
		public function CacheSprite($deafult_play_flg:Boolean = false,_drawFrame:uint=1) 
		{
			mouseChildren = false;
			
			drawFrame = _drawFrame;
			this.cache = ClassCache.getInstance();
			
			this.deafult_play_flg = $deafult_play_flg;
		}
		
		/**
		 * 调用bitmap_movie_mc:BitmapMovieMc的方法,如果bitmap_movie_mc还未建立,
		 * 就侦听它的完成事件,再调用这个方法
		 * @param	func
		 * @param	param
		 */
		public function callFunc(func:String, param:*):void
		{
			_callback = func;
			_param = param;
			if (this.bitmap_movie_mc) {
				this.bitmap_movie_mc[func](param);
			}
		}
		
		/**
		 * 该缓存对象的源类名
		 */
		public function get className() : String
		{
			return _className;
		}
		
		/**
		 * 该缓存对象的源类名
		 */
        public function set className($className:String) : void
        {
            this._className = $className;
			
			var cache:SwfClassCache = SwfClassCache.getInstance();
			if (cache.hasClass(this._className))
			{
				this.addClassChild(this._className);
			} else {
				cache.addEventListener(SwfClassCacheEvent.COMPLETE, onAssetLoaded);
				cache.loadClass(_className);
				
			}
            return;
        } 
		
		/**
		 * 载入完毕处理
		 * @param	e
		 */
		protected function onAssetLoaded(e:SwfClassCacheEvent) : void
		{
			//判断是否是需要的派发
			if (e.className==_className && !e.hasClass) 
			{
				this.single_flg = false;
				cache.removeEventListener(SwfClassCacheEvent.COMPLETE, onAssetLoaded);
				//className = "student.1.301";
				return;
			}
			if (cache.hasClass(this._className) && this.single_flg) {
				this.single_flg = false;
				addClassChild(this._className);
				cache.removeEventListener(SwfClassCacheEvent.COMPLETE, onAssetLoaded);
			}
		}
		
		/**
		 * 获取到class,并加入bitmap_mc处理队列
		 * @param	className
		 */
		private function addClassChild(className:String):void
		{
			var cache:ClassCache = ClassCache.getInstance();
			this.mc_class = cache.getClass(className);
			
			//创建获取这个class的位图序列
			var bitmap_cache:BitmapCacher = BitmapCacher.getInstance();
			var bitmap_movie_mc_tmp:BitmapMovieMc = bitmap_cache.getBitmapMovieMc(this.mc_class);
			
			//返回NULL说明还未创建过该类的位图缓存,正在创建中
			if (bitmap_movie_mc_tmp === null) {
				//开始监听
				bitmap_cache.addEventListener(BitmapCacherEvent.BITMAP_CACHE_COMPLETED, bitmapCacheLoaded);
			} else {
				this.bitmap_movie_mc = bitmap_movie_mc_tmp;
				this.addContainer(this.bitmap_movie_mc);
				
				//body_complete();
			}
		}
		
		private function body_complete():void
		{
			if (bodyComplete_callback!=null) 
			{
				bodyComplete_callback.apply();
				bodyComplete_callback = null;
			}
		}
		
		/**
		 * bitmap cache 处理完成
		 * @param	e
		 */
		private function bitmapCacheLoaded(e:BitmapCacherEvent):void
		{
			//trace(e.class_name);
			if (this.bitmap_mc_single_flg && getQualifiedClassName(this.mc_class) === e.class_name) {
				bitmap_mc_single_flg = false;
				var bitmap_cache:BitmapCacher = BitmapCacher.getInstance();
				bitmap_cache.removeEventListener(BitmapCacherEvent.BITMAP_CACHE_COMPLETED, bitmapCacheLoaded);
				//取出bitmapMc
				this.bitmap_movie_mc = bitmap_cache.getBitmapMovieMc(this.mc_class);

				this.addContainer(this.bitmap_movie_mc);
				
				
			}
		}
		
		private function addContainer($bitmap_movie_mc:BitmapMovieMc = null):void
		{
			if ($bitmap_movie_mc === null) {
				$bitmap_movie_mc = this.bitmap_movie_mc;
			}
			
			//偏移坐标
			$bitmap_movie_mc.x = 0;
			$bitmap_movie_mc.y = 0;

			this.addChild($bitmap_movie_mc);
			//$bitmap_movie_mc.gotoAndPlayLabels('e');
			
			if (this.deafult_play_flg === true) {
				$bitmap_movie_mc.play();
			}
			
			
			//判断是否缩放
			if (this.scale_flg == true) {
				this.setScale();
			}
			
			body_complete();
			
			//派发完成事件
			//dispatchEvent(new BitmapCacherEvent(BitmapCacherEvent.SPRITE_CACHE_COMPLETED, getQualifiedClassName(this.mc_class)));
		}
		
		/**
		 * 设置缩放
		 */
		public function setScale():void
		{
			var iconrect:Rectangle = this.bitmap_movie_mc.getBounds(this.bitmap_movie_mc);
			
			var iconScale:Number;
			var wScale:Number = this._scaleWidth / iconrect.width;
			var hScale:Number = this._scaleHeight / iconrect.height;
			if (wScale>hScale) 
			{
				iconScale = hScale;
			}else {
				iconScale = wScale;
			}
			
			this.bitmap_movie_mc.smoothing = true;
			this.bitmap_movie_mc.scaleX=
			this.bitmap_movie_mc.scaleY = iconScale;

			this.bitmap_movie_mc.setSuperX(0);
			this.bitmap_movie_mc.setSuperY(8);
		}
		
		public function setScaleFlg($b:Boolean = true, $width:int = 70, $height:int = 70):void
		{
			this.scale_flg = $b;
			
			this._scaleWidth = $width;
			this._scaleHeight = $height;
		}
		
		override public function set width($w:Number):void
		{
			this.bitmap_movie_mc.width = $w;
		}
		
		override public function set height($h:Number):void
		{
			this.bitmap_movie_mc.width = $h;
		}
		
	}

}