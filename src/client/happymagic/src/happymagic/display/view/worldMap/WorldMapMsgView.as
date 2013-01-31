package happymagic.display.view.worldMap 
{
	import flash.display.Sprite;
	import flash.geom.Rectangle;
	import happyfish.display.ui.FaceView;
	import happyfish.display.view.IconView;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.UserVo;
	/**
	 * ...
	 * @author jj
	 */
	public class WorldMapMsgView extends Sprite
	{
		private var bg:worldMapMsgUi;
		public var data:UserVo;
		private var face:FaceView;
		private var userBody:IconView;
		
		public function WorldMapMsgView() 
		{
			bg = new worldMapMsgUi();
			addChild(bg);
		}
		
		public function setData(value:UserVo):void {
			data = value;
			loadUserIcon();
			//loadFace();
		}
		
		private function loadUserIcon():void 
		{
			if (!userBody) 
			{
				userBody = new IconView(45, 45, new Rectangle( 1, -85, 45, 45));
				userBody.setData(data.className);
				addChild(userBody);
			}
		}
		
		public function loadFace():void {
			if (!face) 
			{
				face = new FaceView();
				face.x = -3;
				face.y = -90;
				addChild(face);
				//face.loadFace(data.face);
				face.loadFace("http://hdn.xnimg.cn/photos/hdn421/20100422/1430/tiny_s0Up_138492c019116.jpg");
			}
		}
		
		public function set mirro(value:Boolean):void
		{
			if (value) {
				bg.scaleX = -1;
			}else {
				bg.scaleX = 1;
			}
		}
		
	}

}