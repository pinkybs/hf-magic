<?

require_once 'Taobao/Rest/Exception.php';

class Taobao_Rest_Abstract
{
    public $api_key;
    public $secret;
    public $session_key;
    public $v;
    public $server_addr;
    public $sign_method;
    
    const TIME_OUT = 5;
    
    const DEFAULT_SERVICE_VERSION = '2.0';
    
    const SIGN_METHOD_MD5 = 'md5';
    const SING_METHOD_HMAC = 'hmac';
    
    public function __construct($api_key, $secret, $session_key = null) 
    {   
        $this->api_key = $api_key;
        $this->secret = $secret;
        $this->session_key = $session_key;
        $this->_init();
    }
    
    protected function _init()
    {
        //$this->server_addr = 'http://gw.api.tbsandbox.com/router/rest';
        $this->server_addr = 'http://gw.api.taobao.com/router/rest';
        
        $this->v = self::DEFAULT_SERVICE_VERSION;
        $this->sign_method = self::SIGN_METHOD_MD5;
    }
    
    //===========================================================================================================
        
    public function call_method($method, $params)
    {
        $data = $this->post_request($method, $params);
        $result = $this->convert_result($data, $method, $params);
        if (is_array($result) && isset($result['code'])) {
            throw new Taobao_Rest_Exception($result['msg'], $result['code']);
        }
        
        return $result;
    }
        
    protected function convert_result($data, $method, $params)
    {
        $is_xml = (empty($params['format']) || strtolower($params['format']) != 'json');
        return ($is_xml) ? $this->convert_xml_to_result($data, $method, $params) : json_decode($data, true);
    }
    
    protected function convert_xml_to_result($xml, $method, $params)
    {
        $xml = str_replace('&', '{HFCHAR}', $xml);
    	$sxml = simplexml_load_string($xml);
        return self::convert_simplexml_to_array($sxml);
    }
    
    public static function convert_simplexml_to_array($sxml)
    {
        $arr = array();
        if ($sxml) {
            foreach ($sxml as $k => $v) {
                if ($sxml['list']) {
                    $arr[] = self::convert_simplexml_to_array($v);
                } else {
                    $arr[$k] = self::convert_simplexml_to_array($v);
                }
            }
        }
        if (sizeof($arr) > 0) {
            return $arr;
        } else {
            return (string)$sxml;
        }
    }
    
    private function xml_to_array($xml, $method, $params)
    {
        $array = (array)(simplexml_load_string($xml,'SimpleXMLElement', LIBXML_NOCDATA));
        foreach ($array as $key => $item){
            $array[$key]  = $this->struct_to_array((array)$item);
        }
        
        return $array;
    }

    private function struct_to_array($item)
    {
        if (!is_string($item)) {
            $item = (array)$item;
            foreach ($item as $key => $val) {
                $item[$key]  =  $this->struct_to_array($val);
            }
        }
        
        return $item;
    }
    
        
    public function generate_sign($params_array)
    {
        ksort($params_array);
        
        $sign = '';
        foreach ($params_array as $k => $v) {
            $sign .= "$k$v";
        }
        
        if ($this->sign_method == self::SING_METHOD_HMAC) {
            $sign = strtoupper(bin2hex(mhash(MHASH_MD5, $sign, $this->secret)));
        } else {
            $sign = strtoupper(md5($this->secret . $sign . $this->secret));
        }

        return $sign;
    }
    
    public function create_request_url($method, $params)
    {
        $this->finalize_params($method, $params);
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
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::TIME_OUT);
        //max curl execute time
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        //cache dns 1 hour
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 3600);
        
        $cURLVersion = curl_version();
        $ua = 'PHP-cURL/' . $cURLVersion['version'] . ' HapyFish-TOPRest/1.0';
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = @curl_exec($ch);
        $errno = @curl_errno($ch);
        $error = @curl_error($ch);
        curl_close($ch);
        
        if ($errno != CURLE_OK) {
            throw new Taobao_Rest_Exception("HTTP Error: " . $error, $errno);
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
    
    private function add_standard_params($method, &$params)
    {
        $params['method'] = $method;
        $params['app_key'] = $this->api_key;
        $params['session'] = $this->session_key;
        $params['timestamp'] = date('Y-m-d H:i:s');
        $params['v'] = $this->v;
        $params['sign_method'] = $this->sign_method;
    }
    
    private function finalize_params($method, &$params)
    {
        $this->add_standard_params($method, $params);
        //we need to do this before signing the params
        $this->convert_array_values_to_csv($params);
        $params['sign'] = $this->generate_sign($params);
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