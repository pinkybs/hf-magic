package happymagic.display.view.magicBook 
{
	import adobe.utils.CustomActions;
	import com.greensock.easing.Back;
	import com.greensock.easing.Cubic;
	import com.greensock.easing.Linear;
	import com.greensock.TweenLite;
	import com.greensock.TweenMax;
	import flash.display.MovieClip;
	import flash.geom.Point;
	import flash.utils.setTimeout;
	import happyfish.display.ui.RoundLayout;
	import happyfish.display.view.IconView;
	import happyfish.utils.display.CameraSharkControl;
	import happyfish.utils.display.McShower;
	import happymagic.display.view.ui.NeedIconView;
	import happymagic.manager.DisplayManager;
	/**
	 * ...
	 * @author jj
	 */
	public class MixMvControl
	{
		private var needItemsLayout:RoundLayout;
		private var itemIcon:IconView;
		private var callBack:Function;
		private var itemPoint:Point;
		private var needItemNum:uint;
		private var mc:MixMagicViewUi;
		private var last:Boolean;//提示 当前这个物品是不是最后一个掉进容器的
		
		public function MixMvControl(_needItemsLayout:RoundLayout,_itemIcon:IconView,_itemPoint:Point,_callBack:Function,_mc:MixMagicViewUi) 
		{
			needItemsLayout = _needItemsLayout;
			itemIcon = _itemIcon;
			callBack = _callBack;
			mc = _mc;
			
			itemPoint = _itemPoint;
			needItemNum = needItemsLayout.objects.length;
			last = false;
			
		}
		
		public function start():void {
			//材料缩小效果
			TweenLite.to(itemIcon, 0.5, {x:itemIcon.x, y:itemIcon.y, scaleX:0, scaleY:0,onComplete:itemIn,ease:Back.easeIn});
			//材料进入		
		}
		
		private function itemIn():void
		{
            for (var i:int = 0; needItemNum > 0; needItemNum-- )  
			{				
				if (needItemNum == 1)
				{
					last = true;
				}
				tweenNeedItem(needItemsLayout.objects[needItemNum - 1], i * 0.2);
				i++;
			}
			
		}
		
		private function tweenNeedItem(item:NeedIconView,delay:Number):void
		{
			
			if (last)
			{
				TweenLite.to(item, 0.5, { delay:delay, scaleX:0, scaleY:0,alpha:0,y:itemPoint.y, x:itemPoint.x, onComplete:tweenNeedItem_complete, onCompleteParams:[item],ease:Back.easeIn } );
			}
			else
			{
				TweenLite.to(item, 0.5, { delay:delay, scaleX:0, scaleY:0,alpha:0,y:itemPoint.y, x:itemPoint.x, onCompleteParams:[item],ease:Back.easeIn } );
			}

			
		}
		
		private function tweenNeedItem_complete(item:NeedIconView):void
		{
			item.visible = false;
			//播放光效果
		    flashplayguang();
            shakeEffect();
		}
		
		//播放发光的效果
		private function flashplayguang():void
		{
			//1，动画名字 2，放在那个容器里,,,第5参数是回调函数
			var flashMv:McShower = new McShower(lightplay, DisplayManager.uiSprite, null, null);
			var p:Point = new Point(-230, 30);
			flashMv.setMcScaleXY(1.0, 1.0);
			p = itemIcon.parent.localToGlobal(p);
			flashMv.x = p.x;
			flashMv.y = p.y;            
		}
		
		//播放星星爆炸的效果
		private function flashplayBombStar():void
		{
			//1，动画名字 2，放在那个容器里,,,第5参数是回调函数
			var flashMv:McShower = new McShower(BombStar, DisplayManager.uiSprite, null, null, itemmove);
			flashMv.setMcScaleXY(2.5, 2.5);
			var p:Point = new Point(90, 210);
			p = itemIcon.parent.localToGlobal(p);
			flashMv.x = p.x;
			flashMv.y = p.y;
		}
		
		//发光以后表现震动效果		//
		private function shakeEffect():void
		{
			CameraSharkControl.shark(mc.tong1, 5, 1166, flashplayBombStar);
		}
		
		//物品从熔炉里出来的效果
		private function itemmove():void
		{
			itemIcon.y = 180;
			itemIcon.scaleX = 1;
			itemIcon.scaleY = 1;
			
            TweenLite.to(itemIcon, 1.0, {x:itemIcon.x, y:itemIcon.y - 90, scaleX:1, scaleY:1} );
			TweenMax.to(itemIcon, 0.6, { delay:1.0, x:itemIcon.x, y:itemIcon.y - 90, scaleX:1.2, scaleY:1.2, tint:0xffffff, yoyo:true, repeat:1} );
			TweenLite.to(itemIcon, 0.7, { delay:1.6, x:itemIcon.x, y:itemIcon.y - 90, scaleX:1.2, scaleY:1.2, alpha:0, onComplete:callBack,ease:Back.easeOut } );
			//到此动画流程结束
		}
				
	}

}