package createPlayer
{
	import com.brokenfunction.json.decodeJson;
	import com.greensock.easing.Expo;
	import com.greensock.TweenLite;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import flash.net.URLRequest;
	import flash.net.URLVariables;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	import happyfish.utils.display.McShower;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.PublicDomain;
	import happymagic.model.MagicUrlLoader;

	/**
	 * ...
	 * @author jj
	 */
	public class CreatePlayerMain extends Sprite 
	{
		private var view:createPlayerUi;
		private var currentAvatarId:Number;
		private var avatarNum:Number=4;
		private var currentMagicId:Number;
		private var showTimeId:uint;
		public function CreatePlayerMain():void 
		{
			if (stage) init();
			else addEventListener(Event.ADDED_TO_STAGE, init);
		}

		private function init(e:Event = null):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, init);
			// entry point
			view = new createPlayerUi();
			addChild(view);
			view.addEventListener(MouseEvent.CLICK, clickFun, true);
			
			view.avatarMc.mask = view.avatarMask;
			currentAvatarId = 1;
			view.avatarMc.gotoAndStop(currentAvatarId);
			view.changeAvatarMC.stop();
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target.name) 
			{
				case "prevbtn":
				prevAvatar();
				break;
				
				case "nextbtn":
				nextAvatar();
				break;
				
				case "yesBtn":
				saveCreate();
				break;
			}
		}
		
		private function saveCreate():void
		{
			var createLoader:MagicUrlLoader = new MagicUrlLoader();
			createLoader.addEventListener(Event.COMPLETE, saveCreate_complete);
			
			var request:URLRequest = new URLRequest(PublicDomain.getInstance().createUrl);
			request.method = "POST";
			
			var vars:URLVariables = new URLVariables();
			vars.avatarId = currentAvatarId + 800;	
			
			request.data = vars;
			
			createLoader.load(request);
			
			
			//dispatchEvent(new Event(Event.COMPLETE));
					//parent.removeChild(this);
		}
		
		private function saveCreate_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, saveCreate_complete);
			trace("saveCreate_complete:",e.target.data);
			var obj:Object = decodeJson(e.target.data);
			
			if (obj.result) 
			{
				if (obj.result.status==1) 
				{
					dispatchEvent(new Event(Event.COMPLETE));
					parent.removeChild(this);
				}
			}
			
		}
		
		//private function changeAvatar(add:Boolean=true):void {
			//if(add) currentAvatarId++;
			//if (currentAvatarId>avatarNum) 
			//{
				//currentAvatarId = 1;
			//}
			//
			//var tmp:MovieClip;
			//for (var i:int = 1; i <= avatarNum; i++) 
			//{
				//tmp = view.avatarMc["avatar_" + i];
				//if (i!=currentAvatarId) 
				//{
					//TweenLite.to(tmp, 1, { x:65, ease:Expo.easeOut  } );
				//}else {
					//TweenLite.to(tmp, 1, { x:200, ease:Expo.easeOut } );
				//}
				//
			//}
		//}
		
		private function prevAvatar():void
		{
			   currentAvatarId--;
			  if (currentAvatarId <= 0)
			  {
				currentAvatarId = avatarNum;
			  }
			
			   flashplayStart();
			   view.mouseChildren = false;
			   if (showTimeId)
			   {
				   clearTimeout(showTimeId)
			   }
			   showTimeId = setTimeout(flashplayend,500);	
			
		}
		
		private function nextAvatar():void
		{
			   currentAvatarId++;
			   if (currentAvatarId>avatarNum) 
			   {
				currentAvatarId = 1;
			   }
				
			   flashplayStart();
			   view.mouseChildren = false;
			   if (showTimeId)
			   {
				   clearTimeout(showTimeId)
			   }
               showTimeId = setTimeout(flashplayend,500);			
			

		}
		
		
		//播放切换人物时的动效
		private function flashplayStart():void
		{
          view.changeAvatarMC.gotoAndPlay(1);
		}
		
		//播放完成切换人物
		private function flashplayend():void
		{
		  view.avatarMc.gotoAndStop(currentAvatarId);
		  view.mouseChildren = true;
		}
		
	}

}