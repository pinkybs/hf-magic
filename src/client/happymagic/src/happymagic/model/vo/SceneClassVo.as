package happymagic.model.vo 
{
	import com.brokenfunction.json.decodeJson;
	import flash.geom.Point;
	import happyfish.model.vo.BasicVo;
	import happyfish.scene.astar.Node;
	import happyfish.scene.personAction.PersonActionVo;
	import happyfish.scene.utils.NodesByteTools;
	/**
	 * ...
	 * @author jj
	 */
	public class SceneClassVo extends BasicVo
	{
		public var sceneId:uint;
		public var name:String;
		public var content:String;
		//在大地图上显示的地图图标素材类名
		public var className:String;
		//场景背景图
		public var bg:String;
		public var x:int;
		public var y:int;
		public var mp:uint;
		public var needs1:Array;
		public var needs2:Array;
		//解锁需要等级
		public var needLevel:uint;
		public var bgSound:String;
		public var enemy_xy:Array;
		public var nodeStr:String;
		public var nodes:Array;
		//入口列表
		public var entrances:Array;
		public var actions:Array;
		public function SceneClassVo() 
		{
			
		}
		
		override public function setData(obj:Object):BasicVo 
		{
			var i:int;
			for (var name:String in obj) 
			{
				if ( this.hasOwnProperty(name)) 
				{
					if (name=="needs1") {
						needs1 = new Array();
						for (i = 0; i < obj[name].length; i++) 
						{
							needs1.push(new ConditionVo().setData(obj[name][i]));
						}
					}else if (name=="needs2") {
						needs2 = new Array();
						for (i = 0; i < obj[name].length; i++) 
						{
							needs2.push(new ConditionVo().setData(obj[name][i]));
						}
					}else if (name == "enemy_xy") {
						if (obj.enemy_xy) 
						{
							enemy_xy = new Array();
							for (i = 0; i < obj[name].length; i++) 
							{
								enemy_xy.push(new Point(obj[name][i][0], obj[name][i][1]));
							}
						}
					}else {
						this[name] = obj[name];
					}
					
				}
			}
			//临时 增加NPC行为数据
			//addActions();
			return this;
		}
		
		private function addActions():void {
			//var str:String = "[[{\"type\":\"requestMeet\",\"num\":2},{\"type\":\"showMood\",\"iconClass\":\"pao_chat\",\"showTime\":10000},{\"type\":\"showMood\",\"iconClass\":\"pao_heart\",\"showTime\":1000}]]";
			var str:String = "[[{\"type\":\"roundRoom\"},{\"type\":\"outScene\"},{\"type\":\"hide\",\"showTime\":10000}],[{\"type\":\"toNode\"},{\"type\":\"showMood\",\"iconClass\":\"pao_flash\",\"showTime\":2000}],[{\"type\":\"toNode\"},{\"type\":\"showMood\",\"iconClass\":\"pao_heart\",\"showTime\":1000}],[{\"type\":\"toNode\",\"targetNodeArr\":[33,19],\"towardsNodeArr\":[33,18]},{\"type\":\"showMood\",\"iconClass\":\"pao_heart\",\"showTime\":1000}],[{\"type\":\"toNode\",\"targetNodeArr\":[38,21],\"towardsNodeArr\":[38,20]},{\"type\":\"showMood\",\"iconClass\":\"pao_chat\",\"showTime\":3000}],[{\"type\":\"toNode\",\"targetNodeArr\":[19,32]},{\"type\":\"showMood\",\"iconClass\":\"pao_heart\",\"showTime\":3000}],[{\"type\":\"toNode\",\"targetNodeArr\":[41,19]},{\"type\":\"showMood\",\"iconClass\":\"pao_heart\",\"showTime\":3000}],[{\"type\":\"toNode\",\"targetNodeArr\":[21,30]},{\"type\":\"showMood\",\"iconClass\":\"pao_chat\",\"showTime\":3000}]]";
			var obj:Array = decodeJson(str);
			actions = new Array();
			for (var i:int = 0; i < obj.length; i++) 
			{
				actions.push(new Array());
				for (var m:int = 0; m < obj[i].length; m++) 
				{
					actions[i].push(new PersonActionVo().setData(obj[i][m]));
				}
				
			}
		}
		
		public function getEntrancesNode():Array {
			var _entrances:Array = new Array();
			for (var i:int = 0; i < entrances.length; i++) 
			{
				_entrances.push(new Node(entrances[i][0], entrances[i][1]));
			}
			return _entrances;
		}
	}

}