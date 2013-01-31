package happyfish.manager.local
{
	import happyfish.utils.StringTools;
	
	/**
	 * ...
	 * @author jj
	 */
	public class LocaleWords
	{
		private var words:Object;
		public var checkCode1:String="<!";
		public var checkCode2:String="@>";
		public function LocaleWords(access:Private) 
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
				throw new Error( "LocaleChs"+"单例" );
			}
		}
		
		public static function getInstance():LocaleWords
		{
			if (instance == null)
			{
				instance = new LocaleWords( new Private() );
				
			}
			return instance;
		}
		
		public function setValue(value:Object):void {
			words = value;
			
			for (var name:String in words) 
			{
				try {
					this[name] = words[name];
				}catch (e:Error) {
					trace("目标不存在:",name, words[name] );
				}
				
			}
		}
		
		public function getWord(wordName:String, ...args):String {
			var str:String;
			if (words[wordName]) 
				{
					str=words[wordName];
					var tmpstr:String;
					for (var i:int = 1; i < args.length+1; i++) 
					{
						tmpstr = checkCode1 + i.toString() + checkCode2;
						str = StringTools.replace(str, tmpstr, args[i-1]);
					}
					return str;
				}
			
			return wordName;
		}
		
		public function conectWords(str:String,args:Array):String {
			var tmpstr:String;
			for (var i:int = 0; i < args.length; i++) 
			{
				tmpstr = checkCode1 + (i+1).toString() + checkCode2;
				str = StringTools.replace(str, tmpstr, args[i]);
			}
			return str;
		}
		
		private static var instance:LocaleWords;
		
		
	}
	
}
class Private {}