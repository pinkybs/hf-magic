<?

require_once 'Kaixin/Web/Exception.php';

class Kaixin_Web_Abstract
{
    public $verify;
    protected $api_key;
    protected $secret;
    protected $app_id;
    
    const DEFAULT_SERVICE_VERSION = '1';
    const CONNECT_TIMEOUT = 2;
    const TIMEOUT = 3;
    const DNS_CACHE_TIMEOUT = 600;
    
    const USERAGENT = 'PHP-cURL/HapyFish-KaixinWEB-1.0';

    public function __construct($api_key, $secret, $app_id)
    {
        $this->api_key = $api_key;
        $this->secret = $secret;
        $this->app_id = $app_id;
        $this->v = self::DEFAULT_SERVICE_VERSION;

        $this->server_addr = 'http://www.kaixin001.com/api/';
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
            throw new Kaixin_Web_Exception($result['error']['msg'], $result['error']['code']);
        }
        
        return $result;
    }
        
    protected function convert_result($data, $method, $params)
    {
    	return json_decode($data, true);
    }
    
    public function create_request_url($method, $params)
    {
        $post_string = $this->create_post_string($params);
        return $this->server_addr . $method . '.php?' . $post_string;        
    }
    
    public function post_request($method, $params)
    {
        $this->finalize_params($params);
		$url = $this->create_request_url($method, $params);
		
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //max connect time
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIMEOUT);
        //max curl execute time
        curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
        //cache dns 1 hour
        //curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, self::DNS_CACHE_TIMEOUT);
        
        curl_setopt($ch, CURLOPT_USERAGENT, self::USERAGENT);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $result = @curl_exec($ch);
        $errno = @curl_errno($ch);
        $error = @curl_error($ch);
        curl_close($ch);
        
        if ($errno != CURLE_OK) {
            throw new Kaixin_Web_Exception("HTTP Error: " . $error, $errno);
        }

        //echo $result;
        //print_r($result);
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

    public static function generate_sig($params, $secret)
    {
        $str = '';
        ksort($params);
        $str = http_build_query($params);
        $str .= '&' . $secret;
        return md5($str);
    }

    private function add_standard_params(&$params)
    {
        $params['ver'] = $this->v;
        $params['aid'] = $this->app_id;
        $params['ts'] = time();
    }

    private function finalize_params(&$params)
    {
        $this->add_standard_params($params);
        $params['sign'] = self::generate_sig($params, $this->secret);
    }
    
    private function create_post_string($params)
    {
        $post_params = array();
        foreach ($params as $key => &$val) {
            $post_params[] = $key.'='.urlencode($val);
        }
        return implode('&', $post_params);
    }
    
}