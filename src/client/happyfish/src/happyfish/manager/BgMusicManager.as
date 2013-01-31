package happyfish.manager
{
	import flash.events.Event;
	import flash.media.Sound;
	import flash.media.SoundChannel;
	import flash.media.SoundLoaderContext;
	import flash.media.SoundTransform;
	import flash.net.URLRequest;
	/**
	 * ...
	 * @author slamjj
	 */
	public class BgMusicManager
	{
		private var bgSound:Sound;
		private var _isPlaying:Boolean;
		private var nowPlayer:SoundChannel;
		private var bgSoundReady:Boolean;
		
		private var _soundFlag:Boolean;
		
		public function BgMusicManager(access:Private) 
		{	
			if (access != null)
			{	
				if (instance == null)
				{				
					instance = this;
					_soundFlag = true;
				}
			}
			else
			{	
				throw new Error( "BgMusicManager"+"单例" );
			}
		}
		
		
		
		public function setSound(url:String):void {
			currentSoundUrl = url;
			if (!currentSoundUrl || !_soundFlag) 
			{
				stopSound();
				return;
			}
			if (bgSound) 
			{
				if (bgSound.hasEventListener(Event.COMPLETE)) 
				{
					bgSound.removeEventListener(Event.COMPLETE, soundReady);
				}
				stopSound();
				bgSound = null;
				//bgSound.close();
			}
			
			bgSound = new Sound();
			bgSound.addEventListener(Event.COMPLETE, soundReady);
			bgSound.load(new URLRequest(url),new SoundLoaderContext(5000));
			//isPlaying = true;
		}
		
		private function soundReady(e:Event):void 
		{
			bgSoundReady = true;
			e.target.removeEventListener(Event.COMPLETE, soundReady);
			start();
			
		}
		
		public function start():void {
			if (!bgSoundReady) 
			{
				return;
			}
			if (nowPlayer) 
			{
				nowPlayer.stop();
				nowPlayer = null;
			}
			nowPlayer = bgSound.play(0, 999999,new SoundTransform(.5));
			isPlaying = true;
		}
		
		public function stopSound():void {
			if (nowPlayer) 
			{
				nowPlayer.stop();
				nowPlayer = null;
			}else {
				//bgSound.close();
			}
			
			isPlaying = false;
		}
		
		public function isRunning():Boolean {
			return isPlaying;
		}
		
		public static function getInstance():BgMusicManager
		{
			if (instance == null)
			{
				instance = new BgMusicManager( new Private() );
				
			}
			return instance;
		}
		
		
		private static var instance:BgMusicManager;
		private var currentSoundUrl:String;
		
		public function get isPlaying():Boolean { return _isPlaying; }
		
		public function set isPlaying(value:Boolean):void 
		{
			_isPlaying = value;
		}
		
		public function get soundFlag():Boolean { return _soundFlag; }
		
		public function set soundFlag(value:Boolean):void 
		{
			_soundFlag = value;
			if (_soundFlag && !_isPlaying) 
			{
				setSound(currentSoundUrl);
			}else if(!_soundFlag && _isPlaying) 
			{
				stopSound();
			}
		}
	}

}
class Private {}