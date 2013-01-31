package happyfish.display.ui 
{
	import com.greensock.TweenMax;
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.TimerEvent;
	import flash.geom.Point;
	import flash.utils.Timer;
	/**
	 * ...
	 * @author zc
	 */
	
	 //动态滚动条的使用流程
	public class ChangeScrollBar 
	{
		public var mc1:DisplayObjectContainer;//前置屏幕
		public var mc2:DisplayObjectContainer;//后置屏幕
		public var iview:DisplayObjectContainer;//主界面
		private var mc1Point:Point;//MC1的位置
		private var mc2Point:Point;//MC2的位置
		private var timer:Timer;
		private var direction:int;//1 代表从左到右 2代表从右到左 3代表从上到下 4代表从下到上
		public var data:Array;
		public var sum:int;
		//_uview  在_uview的显示列表 interval 滚动的间隔 默认是1000
		public function ChangeScrollBar(_view:DisplayObjectContainer, _mc1:DisplayObjectContainer,_mc2:DisplayObjectContainer, _maskmc1:DisplayObjectContainer, _maskmc2:DisplayObjectContainer,interval:int = 1000) 
		{
			timer = new Timer(interval);
			timer.addEventListener(TimerEvent.TIMER, timerControl);
			
			iview = _view;
			 
			mc1 = _mc1;
			mc2 = _mc2;
			
			iview.addChild(mc1);
			iview.addChild(mc2);
			
			//设置遮罩层
            mc1.mask = _maskmc1;	
			mc2.mask = _maskmc2;				
			
			sum = 0;
		}
		
		private function timerControl(e:TimerEvent):void 
		{
              setData();	  
		}
		
		//设置主界面_view 
		//滚动屏幕 mc 
		//遮罩层 maskMC
		//方向 _direction 1代表从左到右 2代表从右到左 3代表从上到下 4代表从下到上	
		public function init(_data:Array,_mc1Point:Point,_mc2Point:Point):void
		{
			mc1Point = _mc1Point;
			mc2Point = _mc2Point;	
			
			data = _data;
			
			mc1.x = mc1Point.x;
			mc1.y = mc1Point.y;
			
			mc2.x = mc2Point.x;
			mc2.y = mc2Point.y;			 
		}
		
		//子类继承后 修改数据
		public function setData():void
		{			 
			if (data.length == 1)
			{
				return;
			}
            if (sum % 2 == 0)
			{
               TweenMax.to(this.mc1, 1.0, { x:mc2Point.x, y:mc2Point.y ,onComplete:endmovecomplete} );		   
			}
			else
			{
               TweenMax.to(this.mc2, 1.0, { x:mc2Point.x, y:mc2Point.y ,onComplete:endmovecomplete} );			
			}
			

		}
		
		private function endmovecomplete():void 
		{
            if (sum % 2 == 0)
			{
               TweenMax.to(this.mc2, 1.0, { x:mc1Point.x, y:mc1Point.y,onComplete:endmovecomplete2} );					
			}
			else
			{
               TweenMax.to(this.mc1, 1.0, { x:mc1Point.x, y:mc1Point.y,onComplete:endmovecomplete2} );					
			}
		}
		
		private function endmovecomplete2():void 
		{
			 sum++;
			 
			 if (sum >= data.length)
			 {
				 sum = 0;
			 }			
		}
		
		//开始滚动
		public function start():void
		{
			timer.start();
		}		
	}

}