package happyfish.actModule.giftGetAct.event 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class GiftGetActEvent extends Event 
	{
		public static const SELECT:String = "select"; //赠送礼物中选礼物所发送的事件
		public static const SENDCOMPLETE:String = "GiftGetSendComplete";//赠送完成后
		public static const RECEIVEGIFTCOMPLETE:String = "receivegiftcomplete";//接收礼物中选中忽略或者接收所发送的事件
		public static const RECEIVEGIFTLOOPBACK:String = "receivegiftloopback";//接收礼物中选中回赠时所发送的事件
		public static const RECEIVEGIFTLOOPBACKCOMPLETE:String = "receivegiftloopbackcomplete";//接收礼物中选中回赠完成后所发送的事件
		public static const SATISFYFRIENDREQUEST:String = "satisfyfriendrequest";//满足好友请求后所发送的事件
		public static const CLOSE:String = "close";//关闭整个模块
		public static const CLOSE_NUMBERSHOW:String = " GiftGetActClose_Mumber"; //关闭按钮上的数字显示		
		
		public function GiftGetActEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new GiftGetActEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("SelectItemEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}