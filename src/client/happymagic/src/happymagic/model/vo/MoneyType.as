package happymagic.model.vo 
{
	import happyfish.manager.local.LocaleWords;
	/**
	 * ...
	 * @author jj
	 */
	public class MoneyType
	{
		//火魔法
		public static const COIN:uint = 1;
		
		public static const GEM:uint = 2;
		
		public static const COIN_NAME:String = "crystal_coin";
		public static const GEM_NAME:String = "crystal_gem";
		
		public static const typeNameArr:Array = [COIN_NAME, GEM_NAME];
		public function MoneyType() 
		{
			
		}
		
		public static function getTypeName(type:uint):String {
			return LocaleWords.getInstance().getWord(MoneyType.typeNameArr[type-1]);
		}
		
		/**
		 * 返回数据内价格类型(默认为只有一个货币价)
		 * @param	obj
		 * @return
		 */
		public static function getPriceType(obj:Object):uint {
			if (obj.coin) return COIN;
			if (obj.gem) return GEM;
			
			return null;
		}
		
		/**
		 * 返回数据内价格值(默认为只有一个货币价)
		 * @param	obj
		 * @return
		 */
		public static function getPriceNum(obj:Object):uint {
			if (obj.coin) return obj.coin;
			if (obj.gem) return obj.gem;
			
			return null;
		}
	}

}