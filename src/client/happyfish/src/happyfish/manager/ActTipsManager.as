package happyfish.manager 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.geom.Point;
	import happymagic.manager.DisplayManager;
	
	/**
	 * ...
	 * @author jj
	 */
	public class ActTipsManager 
	{
		
		public function ActTipsManager(access:Private) 
		{
			
			if (access != null)
			{	
				if (instance == null)
				{				
					instance = this;
				}
			}
			else
			{	
				throw new Error( "ActTipsManager"+"单例" );
			}
		}
		
		/**
		 * 显示指向标记
		 * @param	p	STAGE全局坐标
		 */
		public function showActTips(p:Point, rotation:Number = 0, container:DisplayObjectContainer = null,scalex:int = 1,scaley:int = 1):void {
			if (!tipsIcon) 
			{
				tipsIcon = new actTipsIconUi();
			}
			
			tipsIcon.x = p.x;
			tipsIcon.y = p.y;
			tipsIcon.rotation = rotation;
			
			if (container) {
				container.addChild(tipsIcon);
			}else {
				DisplayManager.uiSprite.addChild(tipsIcon);
			}
		}
		
		public function hideActTips():void {
			if (tipsIcon) 
			{
				if (tipsIcon.parent) 
				{
					tipsIcon.parent.removeChild(tipsIcon);
				}
			}
		}
		
		public function set visible(value:Boolean):void {
			if (tipsIcon) 
			{
				tipsIcon.visible = value;
			}
		}
		
		public static function getInstance():ActTipsManager
		{
			if (instance == null)
			{
				instance = new ActTipsManager( new Private() );
			}
			return instance;
		}
		
		//设置光圈的缩放
		public function SetHaloScaleXY(_Scalex:Number,_Scaley:Number ):void
		{
			tipsIcon.halo.scaleX = _Scalex;
			tipsIcon.halo.scaleY = _Scaley;			
		}
		
		private static var instance:ActTipsManager;
		private var tipsIcon:actTipsIconUi;
		
	}
	
}
class Private {}