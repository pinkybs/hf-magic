package happyfish.utils {
        import flash.display.Loader;
        import flash.display.LoaderInfo;
        import flash.display.Sprite;
        import flash.events.Event;
		import flash.events.EventDispatcher;
        import flash.net.URLRequest;
		import flash.system.LoaderContext;
        import flash.utils.ByteArray;
        import flash.utils.Endian;

        public class SwfClassGeter extends EventDispatcher {
			private var loader:Loader;
			public var classArr:Array;
            public function SwfClassGeter() {
                loader=new Loader();
                loader.contentLoaderInfo.addEventListener(Event.COMPLETE,completeHandler);
            }
			
			public function getSwf(url:String,loaderContext:LoaderContext=null):void {
				loader.load(new URLRequest(url),loaderContext);
			}
            
            private function completeHandler(event:Event):void {
				
				classArr = new Array();
				
                var bytes:ByteArray=LoaderInfo(event.target).bytes;
                bytes.endian=Endian.LITTLE_ENDIAN;
                bytes.position=Math.ceil(((bytes[8]>>>3)*4+5)/8)+12;
                while(bytes.bytesAvailable>2){
                    var head:int=bytes.readUnsignedShort();
                    var size:int=head&63;
                    if (size==63)size=bytes.readInt();
                    if (head>>6!=76)bytes.position+=size;
                    else {
                        head=bytes.readShort();
                        for(var i:int=0;i<head;i++){
                            bytes.readShort();
                            size=bytes.position;
                            while (bytes.readByte() != 0) { };
                            size = bytes.position - (bytes.position = size);
							classArr.push(bytes.readUTFBytes(size));
                            //trace(classArr);
                        }
                    }
                }
				
				dispatchEvent(new Event(Event.COMPLETE));
            }
        }
    }