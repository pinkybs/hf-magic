package happymagic.display.view.worldMap 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.text.TextField;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.ModuleManager;
	import happyfish.utils.display.BtnStateControl;
	import happyfish.utils.HtmlTextTools;
	import happymagic.display.view.worldMap.events.WorldMapEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.PublicDomain;
	import happymagic.model.command.MoveSceneCommand;
	import happymagic.model.command.UnLockSceneCommand;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.MoneyType;
	import happymagic.model.vo.SceneState;
	import happymagic.model.vo.SceneVo;
	import happymagic.scene.world.bigScene.BigSceneBg;
	import happymagic.scene.world.control.ChangeSceneCommand;
	import happymagic.scene.world.control.FriendsHome;
	import happymagic.scene.world.MagicWorld;
	
	/**
	 * ...
	 * @author jj
	 */
	public class WorldMapSceneInfoView extends UISprite
	{
		private var iview:sceneInfoUi;
		private var data:SceneVo;
		
		public function WorldMapSceneInfoView() 
		{
			super();
			_view = new sceneInfoUi();
			
			iview = _view as sceneInfoUi;
			iview.addEventListener(MouseEvent.CLICK, clickFun,true);
		}
		
		public function setData(_sceneVo:SceneVo):void {
			data = _sceneVo;
			
			iview.contentTxt.text = data.content;
			iview.sceneNameTxt.text = data.name;
			
			if (data.state==SceneState.OPEN) 
			{
				iview.unLockMc.visible = false;
				iview.moveMc.visible = true;
				
				if (DataManager.getInstance().getEnoughMp(data.mp)) 
				{
					HtmlTextTools.setTxtSaveFormat(iview.moveMc.magicNumTxt, data.mp.toString(), 0xffffff);
					BtnStateControl.setBtnState(iview.moveMc.moveSceneBtn, true);
				}else {
					HtmlTextTools.setTxtSaveFormat(iview.moveMc.magicNumTxt, data.mp.toString(), 0xFF0000);
					BtnStateControl.setBtnState(iview.moveMc.moveSceneBtn, false);
				}
			}else if (data.state==SceneState.UNOPEN) 
			{
				iview.unLockMc.visible = true;
				iview.moveMc.visible = false;
				
				iview.unLockMc["crystalNumTxt1"].visible = 
					iview.unLockMc["needCrystalIcon1"].visible = 
					iview.unLockMc["crystalNumTxt2"].visible = 
					iview.unLockMc["needCrystalIcon2"].visible = 
					iview.unLockMc["crystalNumTxt3"].visible = 
					iview.unLockMc["needCrystalIcon3"].visible = 
					iview.unLockMc["needGemIcon"].visible = 
					iview.unLockMc["needGemNumTxt"].visible = false;
				
				var i:int;
				
				var needNum:uint;
				var txtfield:TextField;
				var crystalType:uint;
				
				if (data.needs1) 
				{
					
					var nowNeedIndex:uint=1;
					for ( i= 0; i < data.needs1.length; i++) 
					{
						if (data.needs1[i].isCoin ) 
						{
							
							txtfield = iview.unLockMc["crystalNumTxt" + nowNeedIndex] as TextField;
							needNum = data.needs1[i].num;
							crystalType = ConditionType.StringToInt(data.needs1[i].id);
							if (DataManager.getInstance().getEnouthCrystalType(crystalType,needNum)) 
							{
								HtmlTextTools.setTxtSaveFormat(txtfield, needNum.toString(), 0x000000);
								BtnStateControl.setBtnState(iview.unLockMc.unLockBtn, true);
							}else {
								HtmlTextTools.setTxtSaveFormat(txtfield, needNum.toString(), 0xff0000);
								BtnStateControl.setBtnState(iview.unLockMc.unLockBtn, false);
							}
							iview.unLockMc["needCrystalIcon" + nowNeedIndex].gotoAndStop(crystalType);
							
							iview.unLockMc["needCrystalIcon" + nowNeedIndex].visible = true;
							txtfield.visible = true;
							nowNeedIndex++;
						}
					}
					
					
					if (DataManager.getInstance().currentUser.level<data.needLevel) 
					{
						HtmlTextTools.setTxtSaveFormat(iview.unLockMc["needLevelTxt_1"], 
														data.needLevel.toString(), 0xff0000);
						BtnStateControl.setBtnState(iview.unLockMc.unLockBtn, false);
					}else {
						HtmlTextTools.setTxtSaveFormat(iview.unLockMc["needLevelTxt_1"], 
														data.needLevel.toString(), 0x000000);
						
					}
					
				}
				
				if (data.needs2) 
				{
					for ( i= 0; i < data.needs2.length; i++) 
					{
						if (data.needs2[i].isGem ) 
						{
							needNum = data.needs2[i].num;
							if (needNum>0) 
							{
								iview.unLockMc["needGemIcon"].visible = 
								iview.unLockMc["needGemNumTxt"].visible = true;
								iview.unLockMc["needGemIcon"].gotoAndStop(4);
							}
							
							if (DataManager.getInstance().getEnouthCrystalType(MoneyType.GEM,needNum)) 
							{
								HtmlTextTools.setTxtSaveFormat(iview.unLockMc["needGemNumTxt"], needNum.toString(), 0x000000);
								BtnStateControl.setBtnState(iview.unLockMc.gemUnLockBtn, true);
							}else {
								HtmlTextTools.setTxtSaveFormat(iview.unLockMc["needGemNumTxt"], needNum.toString(), 0xff0000);
								BtnStateControl.setBtnState(iview.unLockMc.gemUnLockBtn, false);
							}
						}
					}
					if (DataManager.getInstance().currentUser.level<data.needLevel) 
					{
						HtmlTextTools.setTxtSaveFormat(iview.unLockMc["needLevelTxt_2"], data.needLevel.toString(), 0xff0000);
						BtnStateControl.setBtnState(iview.unLockMc.gemUnLockBtn, false);
					}else {
						HtmlTextTools.setTxtSaveFormat(iview.unLockMc["needLevelTxt_2"], data.needLevel.toString(), 0x000000);
					}
				}
			}
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target.name) 
			{
				case "closeBtn":
				closeMe();
				break;
				
				case "moveSceneBtn":
					if (data.sceneId!=DataManager.getInstance().currentUser.currentSceneId) 
					{
						moveScene();
					}
				break;
				
				case "fanhuiBtn":
					closeMe();
				break;
				
				case "unLockBtn":
					unLockScene(1);
				break;
				
				case "gemUnLockBtn":
					unLockScene(2);
				break;
			}
		}
		
		private function unLockScene(unLockType:uint):void
		{
			var command:UnLockSceneCommand = new UnLockSceneCommand();
			command.addEventListener(Event.COMPLETE, unLockScene_complete);
			command.unLockScene(data.sceneId, unLockType);
			
		}
		
		private function unLockScene_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, unLockScene_complete);
			var tmpArr:Array = DataManager.getInstance().scenes;
			var tmp:SceneVo;
			if (e.target.data.result.isSuccess) 
			{
				for (var i:int = 0; i < tmpArr.length; i++) 
				{
					tmp = tmpArr[i];
					if (tmp.sceneId==data.sceneId) 
					{
						tmp.state = SceneState.OPEN;
					}
				}
				data.state = SceneState.OPEN;
				
				//changeSceneView();
				
				setData(data);
			}
		}
		
		private function moveScene():void
		{
			//清除房间的数据
			DataManager.getInstance().decorList = {};
			DataManager.getInstance().floorList = [];
			DataManager.getInstance().wallList = [];
			DataManager.getInstance().enemys = [];
			
			if (data.sceneId==PublicDomain.getInstance().getVar("defaultSceneId")) 
			{
				//更换当前场景id
				DataManager.getInstance().currentUser.currentSceneId = data.sceneId;
				
				//清除此场景
				DataManager.getInstance().worldState.world.clear();
				
				new FriendsHome(DataManager.getInstance().currentUser.uid);
				//关闭面板
				DisplayManager.uiSprite.closeModule("worldMap");
				closeMe();
			}else {
				EventManager.getInstance().showLoading();
				var command:MoveSceneCommand = new MoveSceneCommand();
				command.addEventListener(Event.COMPLETE, moveScene_complete);
				command.moveScene(data.sceneId);
			}
		}
		
		private function moveScene_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, moveScene_complete);
			
			if (e.target.data.result.isSuccess) 
			{
				changeSceneView();
			}
			
			
		}
		
		private function changeSceneView():void {
			//切换场景
			new ChangeSceneCommand(data);
			
			EventManager.getInstance().hideLoading();
			
			//关闭面板
			DisplayManager.uiSprite.closeModule("worldMap");
			closeMe();
			
		}
		
		private function changeSceneView_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, changeSceneView_complete);
			DisplayManager.uiSprite.setBg( ModuleManager.getInstance().getModule("worldMap"));
			//DisplayManager.uiSprite.closeModule("worldMap");
			//DisplayManager.uiSprite.setBg("worldMap");
		}
		
	}

}