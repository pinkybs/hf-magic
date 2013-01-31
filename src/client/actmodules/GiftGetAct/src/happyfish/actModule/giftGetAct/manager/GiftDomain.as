package happyfish.actModule.giftGetAct.manager 
{
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.display.DisplayObjectContainer;
	import flash.display.Sprite;
	import flash.display.Stage;
	import flash.events.Event;
	import happyfish.actModule.giftGetAct.interfaces.IGiftDomain;
	import happyfish.actModule.giftGetAct.model.vo.GiftFriendUserVo;
	import happyfish.actModule.giftGetAct.model.vo.GiftRequestVo;
	import happyfish.actModule.giftGetAct.model.vo.GiftUserVo;
	import happyfish.actModule.giftGetAct.model.vo.GiftVo;
	import happyfish.display.ui.FaceView;
	import happyfish.display.ui.Tooltips;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.interfaces.IModule;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happyfish.modules.gift.interfaces.IGiftUserVo;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.display.view.SysMsgView;
	import happymagic.display.view.ui.AwardResultView;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.UserVo;
	/**
	 * ...
	 * @author zc
	 */
	
	 //根据项目不同 请修改数据
	public class GiftDomain implements IGiftDomain
	{
		private static var instance:GiftDomain;
		//private var customObj:Object = new Object();
		private var interfaceUrl:Object;
		private var _stage:Stage;
		
		public function GiftDomain(access:Private) 
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
				throw new Error( "GiftDomain"+"单例" );
			}			
		}
			
		public static function getInstance():GiftDomain
		{
			if (instance == null)
			{
				instance = new GiftDomain( new Private() );
			}
			return instance;
		}
				
		//获取好友列表
		public function get friends():Array
		{
		   return GiftDomain.getInstance().getVar("giftFriendUser");
		}
		
		//设置好友列表
		public function set friends(arr:Array):void
		{
			GiftDomain.getInstance().setVar("giftFriendUser",arr);
		}
		
		//设置一个动态数据  name 名字 val 内容
		public function setVar(name:String,val:*):void {
			DataManager.getInstance().setVar(name, val);
		}
		
		//根据名字获取动态数据
		public function getVar(name:String):* {
			return DataManager.getInstance().getVar(name);
		}
		
		//根据UID 返回一个好友数据IUserVo
		public function getFriendUserVo(_uid:String):GiftFriendUserVo
		{
			var friends:Array = GiftDomain.getInstance().getVar("giftFriendUser");	
			for (var i :int = 0; i < friends.length; i++ )
			{
				if (friends[i].uid == _uid)
				{
					return friends[i] as GiftFriendUserVo;
				}
			}
			return null;
		}
		
		//获取主角的数据
		public function get currentUser():IGiftUserVo
		{
			return DataManager.getInstance().currentUser;
		}
				
		public function addModule(module:ModuleVo):IModule
		{
			var temp:IModule = ModuleManager.getInstance().addModule(module)
			ModuleManager.getInstance().showModule(module.name);
			return temp;
		}
		
		//设置背景变黑
		//target 窗口对象
		public function setBg(target:IModule):void {
			if (target) 
			{
				ModuleManager.getInstance().setModuleBg(target.name,createMaskBg());
			}
		}
		
		//设置背景还原
		//target 窗口对象
		public function hideBg(target:IModule):void
		{
			ModuleManager.getInstance().closeModuleBg(target.name);
		}
		
	    public function createMaskBg():Sprite {
			var bd:BitmapData = new BitmapData(_stage.width, _stage.height, false, 0x000000);
			
			//bd.draw(stage);
			var bt:Bitmap = new Bitmap(bd);
			bt.alpha = .5;
			var mat:Array = [  1, 0, 0, 0, -50, 
							   0, 1, 0, 0, -50, 
							   0, 0, 1, 0, -50, 
							   0, 0, 0, 1, 0 ];
			var bg:Sprite = new Sprite();
			bg.addChild(bt);
			return bg;
		}
		
		//系统提示框
		public function showSysMsg(msg:String):void
		{
		   EventManager.getInstance().showSysMsg(msg, SysMsgView.TYPE_MSG);
		}
		
		//飘屏提示框（msg 是内容）
		public function showPiaoStr(str:String):void
		{
			EventManager.getInstance().showPiaoStr(PiaoMsgType.TYPE_BAD_STRING, str);
		}
		
		//根据名字获取语言包的内容
		public function getWord(name:String):String
		{
			return LocaleWords.getInstance().getWord("name");
		}
		
		//设置语言包
		//根据每个项目的不同设置不同的语言包
		public function setWord(name:String, value:String):void
		{
			
		}
		
		//设置接口地址
		public function setInterfaceUrl(name:String,url:String):void
		{
			interfaceUrl[name] = url;
		}
		
		//根据名字获取接口地址
		public function getInterfaceUrl(name:String):String
		{
			
			return InterfaceURLManager.getInstance().getUrl(name);
			
		}
		
		//设置舞台
		public function  set stage(value:Stage):void
		{
			_stage = value;
		}
		
		//显示奖励界面
		public function showAwardView(data:Object):void
		{
			var awards:Array = new Array();
			var i:int;
		    if (data.addItem)
				{
					for (i = 0; i < data.addItem.length; i++) 
					{
						awards.push(new ConditionVo().setData( { type:ConditionType.ITEM, id:data.addItem[i].i_id, num:data.addItem[i].num } ));
					}
				}
				
				if (data.addDecorBag)
				{
					for (i = 0; i < data.addDecorBag.length; i++) 
					{
						awards.push(new ConditionVo().setData( { type:ConditionType.DECOR, id:data.addDecorBag[i].d_id, num:data.addDecorBag[i].num } ));
					}
				}	
				
				var moduleVo:ModuleVo = new ModuleVo();
				moduleVo.name = ModuleDict.MODULE_AWARD_RESULT;
			    moduleVo.className = ModuleDict.MODULE_AWARD_RESULT_CLASS;
			    moduleVo.algin = "center";
			    moduleVo.mvTime = 0.5;
			    moduleVo.mvType = "fromCenter";
			    moduleVo.single = true;
				
				var awardwin:AwardResultView = GiftDomain.getInstance().addModule(moduleVo) as AwardResultView;
				awardwin.setData( { name:"礼物奖励", awards:awards } );
				
		}
				
			public function getGiftVo(_giftid:String):GiftVo
			{
				var temparr:Array = GiftDomain.getInstance().getVar("gifts");
				
				for (var i:int = 0; i < temparr.length; i++ )
				{
					if ((temparr[i] as GiftVo).id == _giftid)
					{
						return temparr[i];
					}
				}
				return null;
			}
			
			//修改满足好友列表数据
			//_id:
			public function setgiftRequestVoArr(_id:String):void
			{
				var temparr:Array = GiftDomain.getInstance().getVar("giftRequests");
				
				for (var i:int = 0; i < temparr.length; i++ )
				{
					if ((temparr[i] as GiftRequestVo).id == _id)
					{
						(temparr[i] as GiftRequestVo).hasGet = false;
					}					
				}
				GiftDomain.getInstance().setVar("giftRequests", temparr);
			}

			//获取
			public function getGiftDiaryVoName(_type:uint, _giftCid:uint):String
			{
				switch(_type)
				{
					case 1:
					    return DataManager.getInstance().getItemClassByIid(_giftCid).name;							
					break;
					
					case 2:
					    return DataManager.getInstance().getDecorClassByDid(_giftCid).name;			    
					break;
				}
				
				return null;
			}
			
			
			public function getGiftDiaryVoClassName(_type:uint, _giftCid:uint):String
			{
				switch(_type)
				{
					case 1:
		          		  return DataManager.getInstance().getItemClassByIid(_giftCid).class_name;					    
					break;
					
					case 2:			
					      return DataManager.getInstance().getDecorClassByDid(_giftCid).class_name;		
					break;
				}				
				
				return null;				
			}

			//根据UID查找此玩家有没有送过礼物			
			public function isSendGift(_uid:String):Boolean
			{
				var temparr:Array = GiftDomain.getInstance().getVar("giftFriendUser");
				for (var i:int = 0; i < temparr.length; i++ )
				{
					if ((temparr[i] as GiftFriendUserVo).uid == _uid)
					{
						return (temparr[i] as GiftFriendUserVo).giftAble;
					}
				}
				
				return false;
			}
			
			//图片的地址
			public function showFaceView(_address:String):DisplayObjectContainer
			{
				var faceview:FaceView = new FaceView(50);
				faceview.loadFace(_address);
				if (faceview)
				{
					return faceview;
				}
				
			   return null;
			}
			
			//判断礼物是不是全部接收过了
			public function isFullReceiveGift():Boolean
			{
				
			    var temp:Array = GiftDomain.getInstance().getVar("giftDiarys");		
				
				for (var i:int = 0; i < temp.length; i++ )
				{
					if (!temp[i].hasGet)
					{
						return false;
					}
				}
				
				return true;
			}
			
			//判断礼物是不是全部赠送过了
			public function isFullSendGift():Boolean
			{
			    var temp:Array = GiftDomain.getInstance().getVar("giftFriendUser");	
				
				for (var i:int = 0; i < temp.length; i++ )
				{
					if (temp[i].giftAble)
					{
						return true;
					}
				}
			  
				return false;
			}
			//显示tips
			//_view 有Tips的指定显示对象
			//_content 内容
			public function showTips(_view:DisplayObjectContainer,_content:String):void
			{
			    Tooltips.getInstance().register(_view, _content, Tooltips.getInstance().getBg("defaultBg"));				
			}
			
			public function setGiftUserVo(act:ActVo):void
			{
				var giftUserVo:GiftUserVo = new GiftUserVo();
				giftUserVo.giftNum = act.moduleData.giftNum;
			
				if (giftUserVo.giftNum == 0)
				{
					giftUserVo.isNewGift = false; 
				}
				else
				{
					giftUserVo.isNewGift = true;
				}
			
				GiftDomain.getInstance().setVar("giftUserVo", giftUserVo);	
				GiftDomain.getInstance().setVar("IsNewGift", giftUserVo.isNewGift);					
			}
	}

}

class Private {}