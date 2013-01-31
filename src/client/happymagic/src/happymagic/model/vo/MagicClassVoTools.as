package happymagic.model.vo 
{
	/**
	 * ...
	 * @author jj
	 */
	public class MagicClassVoTools
	{
		
		public function MagicClassVoTools() 
		{
			
		}
		
		public static function getLearnCrystal(vo:MagicClassVo):uint {
			switch (vo.magic_type) 
			{
				case MagicType.RED:
				return vo.learn_red;
				break;
				
				case MagicType.BLUE:
				return vo.learn_blue;
				break;
				
				case MagicType.GREEN:
				return vo.learn_green;
				break;
				
				default:
				return 0;
				break;
			}
		}
		
		public static function getCrystal(vo:MagicClassVo):int {
			
			switch (vo.magic_type) 
			{
				case MagicType.RED:
					return vo.red;
				break;
				
				case MagicType.BLUE:
					return vo.blue;
				break;
				
				case MagicType.GREEN:
					return vo.green;
				break;
				
				default:
					return -1;
				break;
			}
		}
	}

}