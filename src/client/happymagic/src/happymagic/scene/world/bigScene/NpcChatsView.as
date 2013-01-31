package happymagic.scene.world.bigScene 
{
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.utils.display.AlginControl;
	
	import happyfish.display.view.IconView;
	import happyfish.display.view.UISprite;
	
	import happymagic.model.vo.NpcVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class NpcChatsView extends UISprite
	{
		private var iview:npcChatsUi;
		private var data:NpcVo;
		private var npcIcon:IconView;
		private var chats:Array;
		private var currentChatIndex:uint;
		private var txtRect:Rectangle;
		private var npcIconRect:Rectangle;
		
		public function NpcChatsView() 
		{
			super();
			_view = new npcChatsUi();
			
			txtRect = new Rectangle( -102, -73, 207, 127);
			npcIconRect = new Rectangle( -147, 60);
			
			iview = _view as npcChatsUi;
			iview.addEventListener(MouseEvent.CLICK, clickFun,true);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case iview.closeBtn:
					
					closeMe();
				break;
				
				case iview.continueBtn:
					randomChat();
				break;
			}
		}
		
		private function randomChat():void
		{
			var toIndex:uint = Math.floor(Math.random() * (chats.length - 1));
			if (toIndex==currentChatIndex && chats.length>1) 
			{
				if (chats[toIndex + 1]) {
					toIndex++;
				}else {
					toIndex--;
				}
			}
			
			currentChatIndex = toIndex;
			initTxt();
		}
		
		public function setData(npc:NpcVo):void {
			data = npc;
			
			chats = npc.chats.split("||");
			
			currentChatIndex = 0;
			
			if (npcIcon) {
				iview.removeChild(npcIcon);
			}
			npcIcon = new IconView(82, 180, npcIconRect);
			iview.addChild(npcIcon);
			npcIcon.setData(data.class_name);
			
			if (chats.length<2) 
			{
				iview.continueBtn.visible = false;
			}else {
				iview.continueBtn.visible = true;
			}
			
			randomChat();
		}
		
		private function initTxt():void
		{
			iview.txt.text = chats[currentChatIndex];
			AlginControl.alginTxtInRect(iview.txt, txtRect);
		}
		
		override public function closeMe(del:Boolean = false):void 
		{
			clear();
			super.closeMe(del);
		}
		
		private function clear():void 
		{
			data = null;
			chats = null;
			currentChatIndex = 0;
			if (npcIcon) {
				iview.removeChild(npcIcon);
				npcIcon = null;
			}
		}
	}

}