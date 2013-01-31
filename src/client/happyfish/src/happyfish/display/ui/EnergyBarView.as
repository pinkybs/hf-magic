package happyfish.display.ui 
{
	import com.greensock.TweenLite;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.TimerEvent;
	import flash.geom.Point;
	import flash.utils.Timer;
	import happyfish.display.view.PerBarView;
	/**
	 * ...
	 * @author zc
	 */
	//能量条的组件类
	public class EnergyBarView 
	{
		
		public var iview:MovieClip;
		private var maxNum:uint;
		private var magicBarMax:uint        //当前移动到什么数值 例如  100/300 100代表magicBarMax 300代表_maxNum
		private var magicbar:PerBarView;
		private var width:uint;             //能量条的宽度
		private var moveitemX:uint;         //那个跟能量条一起动的组件的X坐标
		private var num:uint;               //计数器
		private var movenum:uint;           //
		private var fuction:Function;
		//参数_point          显示能量条的坐标
		//参数_width          能量条的宽度
		public function EnergyBarView(initiativeView:MovieClip, passivityView:MovieClip, _point:Point, _width:uint)
		{
			initiativeView.addChild(passivityView);		
			iview = passivityView;
			iview["MaxmpQuan"].visible = false;
			
			num = 0;
			iview.x = _point.x;
			iview.y = _point.y;	
			width = _width;	
			movenum = 0;
		}
		

		//_max           组件里的最大数值
		//_magicBarMax   能量条的最大值
		//_moveDistance  移动的距离
		//_fuction       回调函数
		//boolenoughlevel 是否满级
		public function setData(_maxNum:uint, _magicBarMax:uint, _moveDistance:Number, _fuction:Function = null, boolenoughlevel:Boolean = false):void
		{
			maxNum = _maxNum;
			magicBarMax = _magicBarMax;
			fuction = _fuction;
			
			if (boolenoughlevel)
			{
			    iview["MaxNum"].text = "MAX";	
				iview["MaxmpQuan"].visible = true;
			}
			else
			{
				iview["MaxNum"].text = String(maxNum);	
			}

			
			magicbar = new PerBarView(iview["magicBar"], width);			
			magicbar.minW = 0;
			magicbar.maxValue = maxNum;
			
			moveitemX = _moveDistance + iview["moveitem"].x;
		}
		
		// 能量条的动作开始
		public function start():void
		{			
			magicbar.setData(magicBarMax);
			TweenLite.to(iview["moveitem"], 1, { x:moveitemX,onComplete:fuction} );
			timerstart();
		}
		
		//同步微调数值差
		//在start开始之前设置
		public function set moveitemx(_x:Number):void
		{
			moveitemX += _x;
		}

		private function timerstart():void
		{
			var time:Timer = new Timer(10, 20);
			time.addEventListener(TimerEvent.TIMER, timeTimer);
			time.start();
		}
		
		private function timeTimer(e:TimerEvent):void 
		{ 
			if (num < 6)
			{
			    movenum += magicBarMax / 10 ;		
			}
			else
			{
		        movenum += magicBarMax / 20 ;				
			}

			
			if (movenum > magicBarMax)
			{
				movenum = magicBarMax;
			}
			iview["moveitem"]["movenum"].text  = String(movenum);
			num++;
		}		
	}

}