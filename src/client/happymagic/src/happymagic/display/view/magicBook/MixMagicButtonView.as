package happymagic.display.view.magicBook 
{
	import com.greensock.easing.Back;
	import com.greensock.TweenLite;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.ui.RoundLayout;
	import happyfish.manager.EventManager;
	import happymagic.display.view.magicBook.event.MixMagicEvent;
	import happymagic.display.view.ui.NeedIconView;
	/**
	 * ...
	 * @author ZC
	 */
	public class MixMagicButtonView extends Sprite
	{
		private var mixview:MixMagicInfoView;
		private var view:MixMagicButtonUI;
		private var itemdata:*;
		private var data:Array;
		public static const BUY:uint = 1;
		public static const BLAG:uint = 2;
		public static const MIX:uint = 3;
		public static const CANCEL :uint = 4;
		private var itemListLayout:RoundLayout;
		public function MixMagicButtonView() 
		{
			
			view = new MixMagicButtonUI();
			this.addChild(view);
			itemListLayout = new RoundLayout(52);
			itemListLayout.setRotation(180);
			view.addEventListener(MouseEvent.CLICK, clickfun);
			view.blag.visible = false;
			view.buy.visible = false;
			view.cancel.visible = false;
			view.mix.visible = false;
			view.addEventListener(Event.ADDED_TO_STAGE, addetostage);
			
		}
		
		private function addetostage(e:Event):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, addetostage);
			stage.addEventListener(MouseEvent.CLICK, stageclickfun);
		}
		
		private function stageclickfun(e:MouseEvent):void 
		{

			if (e.target is NeedIconView)
			{
				return
			}
			
			switch(e.target.name)
			{
				case "buy":
				case "blag":
				case "cancel":
				case "mix":
				return;
				break;
			}
			
			if (mixview.contains(this))
			{
				close();
			}
		}
		
		private function clickfun(e:MouseEvent):void 
		{
			switch(e.target.name)
			 {
				 case "buy":
				    dispatchEvent(new MixMagicEvent(MixMagicEvent.BUY, itemdata));
				    close();
				 break;
				 
				 case "blag":
			        EventManager.getInstance().dispatchEvent(new Event("giftActEventStart"));
					close();
				 break;
				 
				 case "cancel":
				    close();
				 break;
				 
				 case "mix":
				    dispatchEvent(new MixMagicEvent(MixMagicEvent.MIX, itemdata));
				    close();
				 break;
			 }
		}
		
		public function setData(_data:Array,_mixview:MixMagicInfoView,_itemdata:*):void
		{
			itemdata = _itemdata;
			mixview = _mixview;
			var i:uint = 0;
			data = _data;
			for (i = 0; i < data.length; i++ )
			{
			   processingData(i);			
			}
			
			itemListLayout.TweenFrom(40, 130,.6);
		}
		
		private function processingData(_i:int):void
		{
			switch(data[_i])
			{
				case BUY:
				    itemListLayout.addObjects(view.buy);
				    view.addChild(view.buy);
					view.buy.visible = true;
				break;
				
				case BLAG:
				    itemListLayout.addObjects(view.blag);
				    view.addChild(view.blag);
					view.blag.visible = true;				
				break;
				
				case MIX:
				    itemListLayout.addObjects(view.mix);
				    view.addChild(view.mix);
					view.mix.visible = true;				
				break;
				
				case CANCEL:
				    itemListLayout.addObjects(view.cancel);
				    view.addChild(view.cancel);
					view.cancel.visible = true;				
				break;
			}
		}
		
		public function close():void
		{
			TweenLite.to(this, 0.3, {x:this.x, y:this.y, scaleX:0, scaleY:0, ease:Back.easeIn,onComplete:clear});
		}
		
		private function clear():void
		{
		    while (this.numChildren>0) 
			{
				this.removeChildAt(0);
			}
			itemListLayout.clear();
			if (mixview.contains(this))
			{
			    mixview.removeChild(this);				
			}

			
			
		}
	}

}