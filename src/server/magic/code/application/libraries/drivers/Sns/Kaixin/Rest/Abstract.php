<?

require_once 'Kaixin/Rest/Exception.php';

class Kaixin_Rest_Abstract
{
    public $api_key;
    public $secret;
    public $session_key;
    public $v;
    public $server_addr;
    public $method;

    const CONNECT_TIMEOUT = 5;
    const TIMEOUT = 3;
    const DNS_CACHE_TIMEOUT = 600;
    const RETRIES = 3;
    
    const DEFAULT_SERVICE_VERSION = '1.0';
    const USERAGENT = 'PHP-cURL/HapyFish-KaixinRest-1.0';

    public function __construct($api_key, $secret, $session_key = null)
    {
        $this->api_key = $api_key;
        $this->secret = $secret;
        $this->v = self::DEFAULT_SERVICE_VERSION;
        $this->last_call_id = 0;
        $this->session_key = $session_key;

        $this->server_addr = 'http://rest.kaixin001.com/api/rest.php';
    }
    
    //===========================================================================================================
        
    public function call_method($method, $params)
    {
        $data = $this->post_request($method, $params);
        $result = $this->convert_result($data, $method, $params);
        if (!$result || !is_array($result)) {
        	throw new Kaixin_Rest_Exception('response error', -1);
        }
        if (isset($result['error'])) {
            throw new Kaixin_Rest_Exception($result['error']['msg'], $result['error']['code']);
        }
        
        return $result;
    }
        
    protected function convert_result($data, $method, $params)
    {
    	return json_decode($data, true);
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
    
    public function create_request_url($method, $params)
    {
        $this->add_standard_params($method, $params);
        $post_string = $this->create_post_string($method, $params);
        return $this->server_addr . '?' . $post_string;        
    }
    
    public function post_request($method, $params)
    {
        $this->finalize_params($method, $params);
        $post_string = $this->create_post_string($method, $params);
        //echo $post_string.'<br /><br />';
        //echo $this->server_addr . '?' . $post_string;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->server_addr);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //max connect time
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIMEOUT);
        //max curl execute time
        curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
        //cache dns 1 hour
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, self::DNS_CACHE_TIMEOUT);
        //
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        
        curl_setopt($ch, CURLOPT_USERAGENT, self::USERAGENT);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $retries = self::RETRIES;
        $result = false;
    	while (($result === false) && (--$retries > 0)) {
			$result = @curl_exec($ch);
		}
		
        $errno = @curl_errno($ch);
        $error = @curl_error($ch);
        curl_close($ch);
        
        if ($errno != CURLE_OK) {
            throw new Kaixin_Rest_Exception("HTTP Error: " . $error, $errno);
        }

        //echo $result;
        //print_r($result);
        //info_log($result, 'curl.data');
        return $result;
    }
    
    private function convert_array_values_to_csv(&$params)
    {
        foreach ($params as $key => &$val) {
            if (is_array($val)) {
                $val = implode(',', $val);
            }
        }
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
        //we need to do this before signing the params
        $this->convert_array_values_to_csv($params);
        $params['sig'] = self::generate_sig($params, $this->secret);
    }

    private function create_post_string($method, $params)
    {
        $post_params = array();
        foreach ($params as $key => &$val) {
            $post_params[] = $key.'='.urlencode($val);
        }
        return implode('&', $post_params);
    }
    
}