package happyfish.actModule.giftGetAct.interfaces 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.Sprite;
	import flash.display.Stage;
	import happyfish.manager.module.interfaces.IModule;
	import happyfish.manager.module.vo.ModuleVo;
	import happyfish.modules.gift.interfaces.IGiftUserVo;
	/**
	 * ...
	 * @author zc
	 */
	public interface IGiftDomain 
	{
		function setVar(name:String, val:*):void;
        function getVar(name:String):*;
		function addModule(module:ModuleVo):IModule;
		function createMaskBg():Sprite;
		function setBg(target:IModule):void;
		function hideBg(target:IModule):void;
		function showSysMsg(msg:String):void;
		function showPiaoStr(str:String):void;
		function setWord(name:String, value:String):void;
		function getWord(name:String):String;
		function set stage(value:Stage):void;
		function setInterfaceUrl(name:String,url:String):void;
		function getInterfaceUrl(name:String):String;
		function getGiftDiaryVoClassName(_type:uint, _giftCid:uint):String
		function getGiftDiaryVoName(_type:uint, _giftCid:uint):String;
		function showFaceView(_address:String):DisplayObjectContainer;
		function showTips(_view:DisplayObjectContainer,_content:String):void;
	}

}


