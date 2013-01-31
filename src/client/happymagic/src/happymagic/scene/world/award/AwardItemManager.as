package happymagic.scene.world.award 
{
	import com.friendsofed.isometric.Point3D;
	import flash.geom.Point;
	import flash.utils.setTimeout;
	import happyfish.scene.world.WorldState;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.model.vo.ResultVo;
	import happymagic.scene.world.MagicWorld;
	
	/**
	 * ...
	 * @author jj
	 */
	public class AwardItemManager 
	{
	
		public function AwardItemManager(access:Private) 
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
				throw new Error( "AwardItemManager"+"单例" );
			}
		}
		
		public function init(_worldState:WorldState):void {
			worldState = _worldState;
		}
		
		/**
		 * 创建一批奖励对象到场景中
		 * @param	value	type:奖励种类	num:数量	point:出现位置[Point3D]
		 */
		public function addAwards(value:Array):void {
			for (var i:int = 0; i < value.length; i++) 
			{
				setTimeout(addOneAwards, 30 * i, value[i]);
			}
		}
		
		/**
		 * 表现result里获得的东西
		 * @param	value	resultVo
		 * @param	items	道具
		 * @param	point	表现的ISO坐标
		 */
		public function addAwardsByResultVo(value:ResultVo, items:Array, point:Point3D):void {
			var i:int;
			
			var tmparr:Array = new Array();
			if (value.coin) tmparr.push( { type:AwardType.COIN, num:value.coin, point:point } );
			if (value.gem) tmparr.push( { type:AwardType.GEM, num:value.gem, point:point } );
			if (value.exp) tmparr.push( { type:AwardType.EXP, num:value.exp, point:point } );
			
			for (i = 0; i < items.length; i++) 
			{
				tmparr.push( { type:AwardType.ITEM, num:1, point:point, id:items[i].id } );
			}
			
			for (i = 0; i < tmparr.length; i++) 
			{
				setTimeout(addOneAwards, 30 * i, tmparr[i]);
			}
		}
		
		private function addOneAwards(value:Object):void {
			var i:int;
			if (value.type==AwardType.ITEM || value.type==AwardType.DECOR) 
			{
				//创建道具
				(worldState.world as MagicWorld).createAwardItem(value.type, value.num, value.point,value.id);
			}else {
				if (value.type == AwardType.COIN) 
				{
					//按1/10/100分割
					var tmpnum:int;
					var _num:int = value.num;
					var arr:Array = new Array();
					tmpnum = Math.floor(_num/100);
					for (i = 0; i < tmpnum; i++) 
					{
						arr.push(100);
					}
					_num = _num % 100;
					tmpnum = Math.floor(_num/10);
					for (i = 0; i < tmpnum; i++) 
					{
						arr.push(10);
					}
					_num = _num % 10;
					for (i = 0; i < _num; i++) 
					{
						arr.push(1);
					}
					
					
					for (i = 0; i < arr.length; i++) 
					{
						if (arr[i]>0) 
						{
							setTimeout((worldState.world as MagicWorld).createAwardItem,30*i,value.type,arr[i],value.point);
						}
					}
				}else {
					(worldState.world as MagicWorld).createAwardItem(value.type, value.num, value.point);
				}
				
			}
			
		}
		
		public static function getInstance():AwardItemManager
		{
			if (instance == null)
			{
				instance = new AwardItemManager( new Private() );
			}
			return instance;
		}
		
		
		private static var instance:AwardItemManager;
		private var worldState:WorldState;
		
	}
	
}
class Private {}