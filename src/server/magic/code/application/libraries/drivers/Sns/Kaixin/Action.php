<?

class Kaixin_Action
{
    public $api_key;
    public $secret;
    public $session_key;
    public $v;
    public $server_addr;
    public $method;
    
    const DEFAULT_SERVICE_VERSION = '1.0';

    public function __construct($api_key, $secret, $session_key = null)
    {
        $this->api_key = $api_key;
        $this->secret = $secret;
        $this->v = self::DEFAULT_SERVICE_VERSION;
        $this->last_call_id = 0;
        $this->session_key = $session_key;

        $this->server_addr = 'http://www.kaixin001.com/rest/rest.php';
    }

    
    public function sendNewsFeed($text, $link, $options = null)
    {
        $params = array('text' => $text, 'link' => $link);
        if ($options) {
        	$params = array_merge($params, $options);
        }
    	return $this->create_query_string('actions.sendNewsFeed', $params);
    }
    
    public function sendSysNews($text, $link, $options = null)
    {
        $params = array('text' => $text, 'link' => $link);
        if ($options) {
        	$params = array_merge($params, $options);
        }
        
    	return $this->create_query_string('actions.sendSysNews', $params);
    }
    
    public function sendInvitation($text, $options = null)
    {
        $params = array('text' => $text);
        if ($options) {
        	$params = array_merge($params, $options);
        }
    	return $this->create_query_string('actions.sendInvitation', $params);
    }
    
    public function pay($params)
    {
    	return $this->create_query_string('actions.pay', $params);
    }
    
    public function create_query_string($method, $params)
    {
        $this->finalize_params($method, $params);
        $query = http_build_query($params);

        return $this->url_base64_encode($query);
    }

    public static function generate_sig($params_array, $secret)
    {
        $str = '';
        ksort($params_array);
        foreach ($params_array as $k => $v) {
            $str .= "$k=$v";
        }
        $str .= $secret;

        return md5($str);
    }

    private function add_standard_params($method, &$params)
    {
        $params['method'] = $method;
        $params['session_key'] = $this->session_key;
        $params['api_key'] = $this->api_key;
        $params['call_id'] = microtime(true);
        if ($params['call_id'] <= $this->last_call_id) {
            $params['call_id'] = $this->last_call_id + 0.001;
        }
        $this->last_call_id = $params['call_id'];
        if (!isset($params['v'])) {
            $params['v'] = $this->v;
        }
    }

    private function finalize_params($method, &$params)
    {
        $this->add_standard_params($method, $params);
        $params['sig'] = self::generate_sig($params, $this->secret);
    }
    
	public function url_base64_encode($str) 
	{
		$search = array('+', '/');
		$replace = array('*', '-');
		$basestr = base64_encode($str);
		return str_replace($search, $replace, $basestr);
	}

}