package happyfish.manager.module.interfaces 
{
	import flash.display.DisplayObject;
	import flash.display.MovieClip;
	import flash.events.Event;
	
	/**
	 * ...
	 * @author jj
	 */
	public interface IModule 
	{
		function set x(value:Number):void;
		function set y(value:Number):void;
		function get x():Number;
		function get y():Number;
		
		function get state():uint;
		function set state(value:uint):void;
		
		function get scaleX():Number;
		function set scaleX(value:Number):void;
		function get scaleY():Number;
		function set scaleY(value:Number):void;
		
		
		function get view():MovieClip;
		
		function set name(str:String):void;
		function get name():String;
		
		function set maskbg(value:DisplayObject):void;
		function get maskbg():DisplayObject;
		
		function init():void;
		
		//function dispatchEvent(e:Event):void;
	}
	
}