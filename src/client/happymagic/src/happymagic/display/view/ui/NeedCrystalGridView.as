package happymagic.display.view.ui 
{
	import flash.display.Sprite;
	import flash.geom.Rectangle;
	import happymagic.model.vo.MoneyType;
	import xrope.GridLayout;
	import xrope.HBoxLayout;
	
	/**
	 * ...
	 * @author jj
	 */
	public class NeedCrystalGridView extends Sprite
	{
		private var coin:uint;
		private var gem:uint;
		private var maxRect:Rectangle;
		public var layouter:HBoxLayout;
		
		public function NeedCrystalGridView(_rect:Rectangle) 
		{
			maxRect = _rect;
			x = maxRect.x;
			y = maxRect.y;
			layouter = new HBoxLayout(this, maxRect.width, maxRect.height, 0, 0);
			layouter.useBounds = true;
			layouter.lineAlign = "C";
		}
		
		public function add(...args):void {
			for (var i:int = 0; i < args.length; i++) 
			{
				layouter.add(args[i]);
			}	
		}
		
		public function layout():void {
			layouter.layout();
		}
		
		public function addOneType(type:uint,num:int):void {
			
		}
		
		public function setData(_coin:uint,_gem:uint):void {
			coin = _coin;
			gem = _gem;
			
			var arr:Array = new Array();
			if (coin) 
			{
				add(new NeedCrystalLabelView(MoneyType.COIN, coin));
			}
			
			if (gem) 
			{
				add(new NeedCrystalLabelView(MoneyType.GEM, gem));
			}
			
			layout();
		}
		
	}

}