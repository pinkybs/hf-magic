package happyfish.utils
{
	/**
	 * ...
	 * @author slamjj
	 */
	public class DateTools
	{
		
		public function DateTools() 
		{
			
		}
		
		public static function unix2ASDate(val:uint):String {
			var d:Date = new Date(val * 1000);
			return d.getFullYear()+"-"+ (d.getMonth()+1) + "-" + d.getDate();
			//return d.toLocaleString();
		}
		
		public static function getDateString(time:Date,sp:String="/"):String {
			var str:String = "";
			
			str += time.getFullYear().toString()+sp;
			str += String(time.getMonth() + 1)+sp;
			str += time.getDate().toString();
			
			return str;
		}
		
		public static function getTimeString(time:Date, sp:String = ":"):String {
			var str:String = "";
			
			str += time.getHours().toString()+sp;
			str += time.getMinutes().toString()+sp;
			str += time.getSeconds().toString();
			
			return str;
		}
		
		public static function getTimeStringMin(time:Date, sp:String = ":"):String {
			var str:String = "";
			
			str += time.getHours().toString()+sp;
			str += time.getMinutes().toString();
			
			return str;
		}
		
		public static function getHoursTimeStr(time:Date):String {
			var str:String = "";
			
			str += (time.getDay()+time.getHours()).toString()+"小时 ";
			str += time.getMinutes().toString()+"分钟";
			
			return str;
		}
		
		/**
		 * 获得还剩多少时间文字
		 * @param	value	毫秒
		 * @param	showSec
		 * @param	dayStr
		 * @param	hourStr
		 * @param	minStr
		 * @param	secStr
		 * @return
		 */
		public static function getLostTime(value:uint, showSec:Boolean = true, dayStr:String="天", hourStr:String="时", minStr:String="分", secStr:String="秒",allShow:Boolean=false):String {
			
			var day:uint;
			var hour:uint;
			var mins:uint;
			var sec:uint;
			
			
			day = Math.floor(value / (1000 * 60 * 60 * 24));
			value = value % (1000 * 60 * 60 * 24);
			hour = Math.floor(value / (1000 * 60 * 60 ));
			value = value % (1000 * 60 * 60 );
			mins = Math.floor(value / (1000 * 60 ));
			value = value % (1000 * 60);
			sec = Math.floor(value / (1000 ));
			//value = value % (1000);
			
			var str:String="";
			if (day>0) 
			{
				str += day.toString() + dayStr;
			}
			
			if (hour>0) 
			{
				str += hour.toString() + hourStr;
			}
			
			if (mins>0) 
			{
				if ((str.length>0 || allShow )&& hour==0 ) 
				{
					str += "00" + hourStr;
				}
				str += mins.toString() + minStr;
			}
			
			if (!showSec) 
			{
				if ((str.length>0 || allShow ) && mins==0) 
				{
					str += "00" + minStr;
				}
				return str;
			}
			
			if (sec>0) 
			{
				if ((str.length>0 || allShow) && mins==0) 
				{
					str += "00" + minStr;
				}
				str += sec.toString() + secStr;
			}else {
				if ((str.length>0 || allShow) && sec==0) 
				{
					str += "00" + secStr;
				}
			}

			return  str;
		}
	}

}