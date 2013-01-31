package happyfish.manager
{
	import flash.events.Event;
	import flash.media.Sound;
	import flash.media.SoundTransform;
	import happyfish.manager.module.interfaces.ISoundManager;
	import happymagic.manager.DataManager;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class SoundEffectManager implements ISoundManager
	{
		public var soundEffect:Boolean = true;
		public function SoundEffectManager(access:Private) 
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
				throw new Error( "SoundEffictManager"+"单例" );
			}
		}
		
		public function playSound(sound:Sound):void {
			if (ShareObjectManager.getInstance().soundEffect) 
			{
				sound.addEventListener(Event.COMPLETE, play_complete);
				sound.play(0,0,new SoundTransform(.5));
			}
			
		}
		
		private function play_complete(e:Event):void 
		{
			var tmpsound:Sound = e.target as Sound;
			e.target.removeEventListener(Event.COMPLETE, play_complete);
			
			tmpsound.close();
			tmpsound=null;
		}
		
		public static function getInstance():SoundEffectManager
		{
			if (instance == null)
			{
				instance = new SoundEffectManager( new Private() );
			}
			return instance;
		}
		
		
		private static var instance:SoundEffectManager;
		
	}
	
}
class Private {}