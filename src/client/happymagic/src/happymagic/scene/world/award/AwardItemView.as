package happymagic.scene.world.award 
{
	import com.friendsofed.isometric.Point3D;
	import com.greensock.easing.Circ;
	import com.greensock.TweenMax;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import flash.filters.GlowFilter;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.text.TextField;
	import flash.utils.setTimeout;
	import happyfish.cacher.CacheSprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.vo.ModuleStateType;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.WorldState;
	import happyfish.scene.world.WorldView;
	import happyfish.utils.CustomTools;
	import happyfish.utils.display.ScaleControl;
	import happymagic.display.view.MainInfoView;
	import happymagic.display.view.MenuView;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.manager.DisplayManager;
	
	/**
	 * ...
	 * @author jj
	 */
	public class AwardItemView extends IsoItem
	{
		
		public static var inSceneItemNum:uint;
		
		private var toP:Point;
		public var num:uint;
		private var _layer:int;
		
	
		public function AwardItemView($data:Object, $worldState:WorldState,__callBack:Function=null) 
		{
			_bodyCompleteCallBack = __callBack;
			super($data, $worldState);
			physics = true;
			typeName = "AwardItem";
			_layer = WorldView.LAYER_FLYING;
			
			view.container.sortPriority = -4;
			
			//view.container.addEventListener(MouseEvent.CLICK, onClick);
			view.container.addEventListener(MouseEvent.MOUSE_OVER, onMouseOver);
			view.container.addEventListener(MouseEvent.MOUSE_MOVE, onMouseOverMove);
			
			view.container.buttonMode = true;
			view.container.mouseChildren = false;
			//view.container.mouseEnabled = false;
			
			inSceneItemNum++;
		}
		
		public function out():void
		{
			physics = false;
			if (alive) 
			{
				
				inSceneItemNum--;
				
				view.container.mouseEnabled = false;
				alive = false;
				var tmpP:Point;
				var menuview:MenuView = DisplayManager.uiSprite.getModule("menu") as MenuView;
				if (data.type == AwardType.ITEM) 
				{
					
					if (menuview.state==ModuleStateType.SHOWING) 
					{
						tmpP = menuview.getBtnPoint("itemBtn");
						tmpP = view.parent.globalToLocal(tmpP);
						TweenMax.to(_view.container, .6, {screenX:tmpP.x,screenY:tmpP.y,scaleX:.1,scaleY:.1,alpha:0, onComplete:out_complete, onCompleteParams:[tmpP] } );
					}else {
						TweenMax.to(_view.container, .6, {screenY:"-20",alpha:0, onComplete:out_complete} );
					}
					
				}else if (data.type == AwardType.DECOR) {
					
					if (menuview.state==ModuleStateType.SHOWING) 
					{
						tmpP = menuview.getBtnPoint("diyBtn");
						tmpP = view.parent.globalToLocal(tmpP);
						TweenMax.to(_view.container, .6, {screenX:tmpP.x,screenY:tmpP.y,scaleX:.1,scaleY:.1,alpha:0, onComplete:out_complete, onCompleteParams:[tmpP] } );
					}else {
						TweenMax.to(_view.container, .6, {screenY:"-20",alpha:0, onComplete:out_complete} );
					}
				}else if (data.type == AwardType.OTHER) {
					
				}else {
					tmpP = new Point(view.container.screenX, view.container.screenY);
					tmpP = view.container.parent.localToGlobal(tmpP);
					//tmpP=_view.container.parent.globalToLocal(tmpP);
					EventManager.getInstance().showPiaoStr(data.type, data.num.toString(), tmpP, true, true);
					
					var maininfo:MainInfoView = DisplayManager.uiSprite.getModule(ModuleDict.MODULE_MAININFO) as MainInfoView;
					var toP:Point = maininfo.getValuePosition(data.type);
					toP = _view.container.parent.globalToLocal(toP);
					//TweenMax.to(_view.container, .3, { y:"-15", autoAlpha:0, onComplete:out_complete, ease:Circ.easeOut, onCompleteParams:[tmpP] } );
					TweenMax.to(_view.container, .6, {screenX:toP.x,screenY:toP.y,scaleX:.1,scaleY:.1,alpha:0, onComplete:out_complete, onCompleteParams:[tmpP] } );
					
				}
			}
		}
		
		override public function landed():void 
		{
			physics = false;
			setTimeout(out,7000);
		}
		
		private function out_complete(showP:Point=null):void
		{
			//var maininfo:MainInfoView = DisplayManager.uiSprite.getModule(ModuleDict.MODULE_MAININFO) as MainInfoView;
			//maininfo.flashValue(data.type,data.num);
			
			_view.parent.removeIsoChild(_view);
          
			_worldState.world.removeItem(this);
			
		}
		
		override protected function makeView():IsoSprite 
		{
			this._view = new IsoSprite(this.layer);
			this.asset = new CacheSprite(false);
			asset.bodyComplete_callback = view_complete;
			this.asset.className = this._data.class_name;
			
			_view.container.addChild(this.asset);
			
			//var numTxt:TextField = new TextField();
			//numTxt.autoSize = "center";
			//numTxt.mouseEnabled = false;
			//numTxt.selectable = false;
			//numTxt.cacheAsBitmap = true;
			//numTxt.textColor = 0xffffff;
			//numTxt.defaultTextFormat.bold = true;
			//numTxt.filters = [new GlowFilter(0x000000, 1, 2, 2)];
			//numTxt.text = "x"+data.num;
			//numTxt.x = -numTxt.width / 2;
			//numTxt.y = -numTxt.height;
			//_view.container.addChild(numTxt);
			
			
			var pos:Point3D = new Point3D(_data.x, _data.y, _data.z);
			this._view.setPos(pos);
			
            return this._view;
		}
		
		override protected function view_complete():void
		{
			super.view_complete();
			
			
			//asset.bitmap_movie_mc.smoothing = true;
			//ScaleControl.size(asset, 40, 40, new Rectangle( -20, -20, 40, 40));
			
			//var shadowBg:MovieClip = new awardItemBg();
			//shadowBg.cacheAsBitmap = true;
			//view.container.addChildAt(shadowBg, 0);
			
			view.container.vy = CustomTools.customInt(-10,-20);
			view.container.vx = CustomTools.customInt(-10,10);
			view.container.vz = CustomTools.customInt( -10, 10);
			
			physics = true;
		}
		
		protected function set layer($layer:int):void
		{
			this._layer = $layer;
		}
		
		protected function get layer():int
		{
			return this._layer;
		}
	}

}