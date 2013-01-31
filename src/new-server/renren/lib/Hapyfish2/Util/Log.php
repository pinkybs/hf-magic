<?php

class Hapyfish2_Util_Log
{
    protected static $_instance;

    public function __construct()
    {

    }

    /**
     * single instance of Hapyfish2_Util_Log
     *
     * @return Hapyfish2_Util_Log
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    
    protected function getLogFile($time, $category)
    {
    	return LOG_DIR . '/' . $category . '-' . date('Ymd', $time) . '.log';
    }
    
	public function saveLog($logfile, $msg)
	{
		file_put_contents($logfile, $msg, FILE_APPEND);
	}
	
	public function getClient()
	{
		$ip = false;
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode (', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip) {
				array_unshift($ips, $ip);
				$ip = false;
			}
			for ($i = 0, $n = count($ips); $i < $n; $i++) {
				if (!eregi ("^(10|172\.16|192\.168)\.", $ips[$i])) {
					$ip = $ips[$i];
					break;
				}
			}
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		$intIp = 0;
		if ($ip) {
			$ipData = explode('.', $ip);
			if (count($ipData) == 4) {
				foreach ($ipData as $k => $v) {
					$intIp += (int)$v * pow(256, 3 - $k);
				}
			}
		}
		
		return $intIp;
	}
	
	public function report($category, $info)
	{
		$ip = $this->getClient();
		$time = time();
		$msg = $time . "\t" . $ip . "\t" . implode("\t", $info) . "\n";
		$file = $this->getLogFile($time, $category);
		$this->saveLog($file, $msg);
	}

}