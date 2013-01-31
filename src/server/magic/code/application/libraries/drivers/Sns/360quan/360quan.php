<?php
require_once "application/libraries/drivers/Sns/360quan/360quan_rest.phpphp";

class Client_360quan {

    public $client;

    private $api_key;
    private $api_secret;
    private $params;


    /**
     * constructor
     *
     * @param string $api_key
     * @param string $api_secret
     */
    function __construct($api_key, $api_secret){
        $this->api_key      = $api_key;
        $this->api_secret   = $api_secret;
        $this->params       = array();

        /**
         * FIXME
         * 此内容待订
         *
         * 调用远程接口
         */
        $this->client = new Client_360quan_Rest($api_key, $api_secret);

        $this->parseParams( 86400 );
    }

    /**
     * 获取接收到的参数
     *
     * @param string $key
     * @return string|bool 
     */
    public function getParam($key){
        if(!isset($this->params[$key])){
            return false;
        } else {
            return $this->params[$key];
        }
    }

    /**
     * 指示当前页面需要用户登录
     *   如果用户未登录, 则自动跳转到登录页面
     *   如果用户未加入这个app, 则自动跳转到安装页面
     *
     * @return int userid
     */
    public function requireLogin(){
        $user = $this->getParam("uid");

        if(empty($user)){
            $this->redirect($this->signinUrl($this->currentUrl()), $this->inFrame());
        }

        $this->requireAdd();

        return $user;
    }

    public function requireAdd(){

        $user = $this->getParam("uid");
        if($user){
            $added = $this->getParam("added");
            if($added){
                return $user;
            }
        }

        $this->redirect($this->addUrl($this->currentUrl()));
    }

    /**
     *  当前页面需要嵌入到平台中. 可以是html或iframe类型的嵌入.
     *  如果不再平台框架中, 则跳转到平台框架的页面.
     *
     */
    public function requireFrame(){
        if($this->inFrame() === false){
            $this->redirect($this->signinUrl($this->currentUrl()), true);
        }

    }

    /* private method below  */

    /**
     * order of params, POST, GET
     */
    private function parseParams($timeout = false){

        $prefix = "qn_sig";
        $params_t = $_POST; 

        if(empty($params_t[$prefix])){
            $params_t = $_GET;
        }

        if(empty($params_t[$prefix])){
            return ;
        }

        $params = array();
        foreach($params_t as $k => $v){
            if(strpos($k, $prefix . "_") === 0){
                $_k = substr($k, strlen($prefix . "_"));
                $params[$_k] = $v;
            }
        } 

        if($timeout and ( time() - $params["time"] > $timeout )){
            return ;
        }

        if(!isset($params_t[$prefix]) or self::generate_sig($params, $this->api_secret) !== $params_t[$prefix]){
            // bad signature, halt.
            // throw new Client_360quan_Exception12("Bad Signature.");
            return;
        }

        $this->params = $params;

        $session_key = $this->getParam("session_key");

        if($session_key){
            $this->client->session_key = $session_key;
        }

    }

    public function redirect($url) {
        if ($this->inCanvas()) {
            echo '<qn:redirect url="' . $url . '" />';
        } else if (preg_match('/^https?:\/\/([^\/]*\.)?360quan\.com(:\d+)?/i', $url)) {
            echo "<script type=\"text/javascript\">\ntop.location.href = \"$url\";\n</script>";
        } else {
            header('Location: ' . $url);
        }
        exit;
    }

    private function signinUrl($returnUrl, $canvas = true){
        return "http://passport.360quan.com/passport.php?action=signin&v=1.1"
            . "&api_key=". $this->api_key
            . "&next=" . urlencode($returnUrl)
            . "&canvas=" . ($canvas ? 1 : '')
            . "";
    }

    private function addUrl($returnUrl, $nouse = true){
        return "http://w.360quan.com/apps/add?v=1.1"
            . "&api_key=" . $this->api_key
            . "&next=" . urlencode($returnUrl)
            . "" ;
    }

    public function inFrame() {
        return $this->getParam("in_iframe") || $this->getParam("in_canvas");
    }

    public function inCanvas() {
        return $this->getParam("in_canvas");
    }

    private function setParams($key, $value){
        $this->params[$key] = $value;
    }

    private function currentUrl(){
        $url = "http://" . $_SERVER["HTTP_HOST"];
        if(isset($_SERVER["SERVER_PORT"]) and $_SERVER["SERVER_PORT"] != "80"){
            $url .= ":" . $_SERVER["SERVER_PORT"];
        }
        $url .= $_SERVER["REQUEST_URI"];
        return $url;
    }


    public static function generate_sig($params_array, $secret) {
        $str = '';

        ksort($params_array);
        foreach ($params_array as $k=>$v) {
            $str .= "$k=$v";
        }
        $str .= $secret;

        return md5($str);
    }

}

