package happymagic.display.view.ui 
{
	import happyfish.display.ui.FaceView;
	import happyfish.manager.local.LocaleWords;
	import happymagic.model.vo.UserVo;
	/**
	 * ...
	 * @author jj
	 */
	public class UserFaceView extends userFaceUi
	{
		private var data:UserVo;
		private var face:FaceView;
		
		public function UserFaceView() 
		{
			
		}
		
		public function setData(user:UserVo):void {
			data = user;
			
			levelTxt.text = LocaleWords.getInstance().getWord("lv", data.level.toString());
			nameTxt.text = data.name;
			
			face = new FaceView(33);
			face.x = 11;
			face.y = 8;
			addChild(face);
			face.loadFace(data.face);
		}
		
	}

}