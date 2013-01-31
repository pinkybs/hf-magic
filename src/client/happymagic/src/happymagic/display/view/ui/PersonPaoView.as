package happymagic.display.view.ui 
{
	import flash.display.Sprite;
	import flash.geom.Rectangle;
	import happyfish.display.view.IconView;
	import happyfish.scene.world.grid.IsoItem;
	/**
	 * 人物头顶的心情泡
	 * @author jj
	 */
	public class PersonPaoView extends Sprite
	{
		private var target:IsoItem;
		private var iconClass:String;
		private var bubble:IconView;
		private var bg:ui_tanchupao;
		
		public function PersonPaoView(_target:IsoItem,_iconClass:String,showPao:Boolean=false,size:Number=26 ) 
		{
			target = _target;
			iconClass = _iconClass;
			
			mouseEnabled = mouseChildren = false;
			
			if (showPao) 
			{
				bg = new ui_tanchupao();
				addChild(bg);
			}
			if (bg) 
			{
				bubble = new IconView(size, size, new Rectangle( -44, -75, 86, 86));
			}else {
				bubble = new IconView(size,size, new Rectangle( -44, -75+20, 86, 86));
			}
			
			bubble.setData(iconClass);
			addChild(bubble);
			
			
			
			target.view.container.addChild(this);
			
			initPosition();
			
			
		}
		
		public function initPosition():void {
			if (bg) 
			{
				y = target.asset.getBounds(target.asset.parent).top+15;
			}else {
				y = target.asset.getBounds(target.asset.parent).top+10;
			}
			
		}
		
		public function remove():void {
			if (parent) 
			{
				parent.removeChild(this);
			}
			target = null;
		}
		
	}

}