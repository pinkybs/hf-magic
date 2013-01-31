<?

require_once 'Qzone/Rest/Exception.php';

class Qzone_Rest_Abstract
{
    public $app_id;
	public $app_key;
	public $app_name;
    public $open_id;
    public $open_key;
    public $v;
    public $server_addr;
    
    const CONNECT_TIMEOUT = 2;
    const TIMEOUT = 3;
    const DNS_CACHE_TIMEOUT = 600;
    
    const DEFAULT_SERVICE_VERSION = '1.0';
    const USERAGENT = 'PHP-cURL/HapyFish-QzoneRest-1.0';
    
    public function __construct($app_id, $app_key, $app_name) 
    {   
        $this->app_id = $app_id;
    	$this->app_key = $app_key;
    	$this->app_name = $app_name;
        $this->_init();
    }
    
    protected function _init()
    {
        $this->server_addr = API_HOST . '/cgi-bin';
        
        $this->v = self::DEFAULT_SERVICE_VERSION;
    }
    
    public function set_User($open_id, $open_key)
    {
        $this->open_id = $open_id;
    	$this->open_key = $open_key;
    }
    
    //===========================================================================================================
        
    public function call_method($method, $params)
    {
        $data = $this->post_request($method, $params);
        $result = $this->convert_result($data, $method, $params);
        if (!$result || !is_array($result)) {
        	throw new Qzone_Rest_Exception('response error', -1);
        }
        if ($result['ret'] != 0) {
            throw new Qzone_Rest_Exception($result['msg'], $result['ret']);
        }
        
        unset($result['ret']);
        return $result;
    }
        
    protected function convert_result($data, $method, $params)
    {
    	return json_decode($data, true);
    }
    
    public function create_request_url($method, $params)
    {
        $this->add_standard_params($method, $params);
        $post_string = $this->create_post_string($method, $params);
        return $this->server_addr . '?' . $post_string;        
    }
    
    public function post_request($method, $params)
    {
        $this->add_standard_params($method, $params);
        $post_string = $this->create_post_string($method, $params);
        
        $url = $this->get_api_address($method);
        //echo $post_string.'<br /><br />';
        //echo $url . '?' . $post_string;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //max connect time
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIMEOUT);
        //max curl execute time
        curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
        //cache dns 1 hour
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, self::DNS_CACHE_TIMEOUT);
        
        curl_setopt($ch, CURLOPT_USERAGENT, self::USERAGENT);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = @curl_exec($ch);
        $errno = @curl_errno($ch);
        $error = @curl_error($ch);
        curl_close($ch);
        
        if ($errno != CURLE_OK) {
            throw new Qzone_Rest_Exception("HTTP Error: " . $error, $errno);
        }

        //echo $result;
        //print_r($result);
        return $result;
    }
    
    private function get_api_address($method)
    {
    	return $this->server_addr . '/' . $method . '.cgi';
    }
    
    private function add_standard_params($method, &$params)
    {
        $params['openid'] = $this->open_id;
        $params['openkey'] = $this->open_key;
        $params['appid'] = $this->app_id;
        $params['appkey'] = $this->app_key;
        $params['ref'] = $this->app_name;
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