<?

class Kaixin_Web
{
    /**
     * web api call object
     *
     * @var Kaixin_Web_Core
     */
    public $core;
    
    protected $err;
    
    protected $code;

    protected static $_instance;
    
    /**
     * get object
     *
     * @return Kaixin_Rest
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self(APP_KEY, APP_SECRET, APP_ID, APP_NAME);
        }

        return self::$_instance;
    }
    
    protected function clearErr()
    {
    	$this->err = false;
    	$this->code = 0;
    }
    
    public function isErr()
    {
    	return $this->err;
    }
    
    public function getCode()
    {
    	return $this->code;
    }
    
    public function getBalance($vuid)
    {
        $this->clearErr();
    	try {
            $data = $this->core->balance($vuid);
            if (isset($data['balance'])) {
				return $data['balance'];
            }
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log('[Kaixin_Web::getBalance]: ' . $e->getMessage());
        }

        $this->err = true;
        return null;
    }
    
    public function spend($order)
    {
        $this->clearErr();
        
    	$order['vendor'] = 'leyu';
		$order['appname'] = $this->app_name;
    	try {
            $data = $this->core->spend($order);
            return $data;
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log('[Kaixin_Web::spend]: ' . $e->getMessage());
        }

        $this->err = true;
        return null;
    }

    public function __construct($api_key, $secret, $app_id, $app_name)
    {
        $this->api_key = $api_key;
        $this->secret = $secret;
        $this->app_id = $app_id;
        $this->app_name = $app_name;

        $this->core = new Kaixin_Web_Core($api_key, $secret, $app_id);
    }

}