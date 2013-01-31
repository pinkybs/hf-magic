package happyfish.utils.display 
{
	import com.greensock.data.GlowFilterVars;
	import flash.filters.ColorMatrixFilter;
	import flash.filters.GlowFilter;
	/**
	 * ...
	 * @author slamjj
	 */
	public class FiltersDomain
	{
		static private var _grayFilter:ColorMatrixFilter;
		static private var redGlowFilter:GlowFilter;
		static private var blueGlowFilter:GlowFilter;
		static private var yellowGlowFilter:GlowFilter;
		static private var textGlowFilter:GlowFilter;
		static private var contrastFilter:ColorMatrixFilter;//对比度为-100
		
		public function FiltersDomain() 
		{
			
		}
		
		public static function get textGlow():GlowFilter {
			if (!textGlowFilter) 
			{
				textGlowFilter = new GlowFilter(0x000000, .8, 2, 2, 2, 1);
			}
			return textGlowFilter;
		}
		
		public static function get grayFilter():ColorMatrixFilter {
			if(!_grayFilter){
				var red:Number = 0.3086;
				var green:Number = 0.694;
				var blue:Number = 0.0820;
				_grayFilter = new ColorMatrixFilter([red, green, blue, 0, 0, red, green, blue, 0, 0, red, green, blue, 0, 0, 0, 0, 0, 1, 0]);
			}
			return _grayFilter;
		}
		
		public static function get redGlow():GlowFilter {
			if (!redGlowFilter) 
			{
				redGlowFilter=new GlowFilter(0xff0000, 1, 10, 10, 2, 1,true);
			}
			return redGlowFilter;
		}
		
		public static function get yellowGlow():GlowFilter {
			if (!yellowGlowFilter) 
			{
				yellowGlowFilter=new GlowFilter(16776960, 1, 5, 5, 10, 1, false, false);
			}
			return yellowGlowFilter;
		}
		
		public static function get blueGlow():GlowFilter {
			if (!blueGlowFilter) 
			{
				blueGlowFilter=new GlowFilter(0x1F5795, 1, 5, 5, 2, 1,true);
			}
			return blueGlowFilter;
		}
		public static function get _contrastFilter():ColorMatrixFilter {
			if (!contrastFilter) 
			{
				var n:Number = 0;
				var x:Number = 128*(1 - n);
				contrastFilter = new ColorMatrixFilter([n, 0, 0, 0,x, 0, n, 0, 0, x, 0, 0, n, 0, x, 0, 0, 0, 1, 0]);
			}
			return contrastFilter;
		}
		
	}

}