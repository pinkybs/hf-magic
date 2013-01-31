<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * 静态初始化
 * @author Beck
 *
 */
class Static_Controller extends Controller {
	/**
	 * 静态初始化
	 */
	public function init()
	{
		$output = file_get_contents(media::file_url().'config.json');
		echo $output;
	}
	
	public function zlib()
	{
		echo url::base();
		//$output = file_get_contents(media::file_url().'load_game_data.htm');
//		$zd = gzopen(media::file_url().'tmp.txt', "r");
//		$contents = gzread($zd, 10000);
//gzclose($zd);
		//echo $output;
	}
	
private function gzdecode($data) {
  $len = strlen($data);

  $method = ord(substr($data,2,1));  // Compression method
  $flags  = ord(substr($data,3,1));  // Flags

  // NOTE: $mtime may be negative (PHP integer limitations)
  $mtime = unpack("V", substr($data,4,4));
  $mtime = $mtime[1];
  $xfl   = substr($data,8,1);
  $os    = substr($data,8,1);
  $headerlen = 10;
  $extralen  = 0;
  $extra     = "";
  if ($flags & 4) {
    // 2-byte length prefixed EXTRA data in header

    $extralen = unpack("v",substr($data,8,2));
    $extralen = $extralen[1];

    $extra = substr($data,10,$extralen);
    $headerlen += 2 + $extralen;
  }

  $filenamelen = 0;
  $filename = "";
  if ($flags & 8) {
    // C-style string file NAME data in header

    $filenamelen = strpos(substr($data,8+$extralen),chr(0));

    $filename = substr($data,$headerlen,$filenamelen);
    $headerlen += $filenamelen + 1;
  }

  $commentlen = 0;
  $comment = "";
  if ($flags & 16) {
    // C-style string COMMENT data in header

    $commentlen = strpos(substr($data,8+$extralen+$filenamelen),chr(0));

    $comment = substr($data,$headerlen,$commentlen);
    $headerlen += $commentlen + 1;
  }

  $headercrc = "";
  if ($flags & 2) {
    // 2-bytes (lowest order) of CRC32 on header present

    $calccrc = crc32(substr($data,0,$headerlen)) & 0xffff;
    $headercrc = unpack("v", substr($data,$headerlen,2));
    $headercrc = $headercrc[1];

    $headerlen += 2;
  }

  // GZIP FOOTER - These be negative due to PHP's limitations
  $datacrc = unpack("V",substr($data,-8,4));
  $datacrc = $datacrc[1];
  $isize = unpack("V",substr($data,-4));
  $isize = $isize[1];

  // Perform the decompression:
  $bodylen = $len-$headerlen-8;

  $body = substr($data,$headerlen,$bodylen);
  $data = "";
  if ($bodylen > 0) {
    switch ($method) {
      case 8:
        // Currently the only supported compression method:
        $data = gzinflate($body);
        break;
    }
  } else {
    // I'm not sure if zero-byte body content is allowed.
    // Allow it for now...  Do nothing...
  }

  // Verifiy decompressed size and CRC32:
  // NOTE: This may fail with large data sizes depending on how
  //       PHP's integer limitations affect strlen() since $isize
  //       may be negative for large sizes.

  return $data;
}
}