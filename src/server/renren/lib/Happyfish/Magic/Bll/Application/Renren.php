<?php

class Happyfish_Magic_Bll_Application_Renren extends Happyfish_Magic_Bll_Application_Abstract
{
    public $xn_params;

    public $hf_params;

    protected $_renren;

    /**
     * Singleton instance, if null create an new one instance.
     *
     * @param Zend_Controller_Action $actionController
     * @return Bll_Application
     */
    public static function newInstance(Zend_Controller_Action $actionController)
    {
        if (null === self::$_instance) {
            self::$_instance = new Happyfish_Magic_Bll_Application_Renren($actionController);
        }

        return self::$_instance;
    }

    public function get_valid_xn_params($params, $timeout = null, $namespace = 'xn_sig')
    {
        if (empty($params)) {
            return array();
        }

        $prefix = $namespace . '_';
        $prefix_len = strlen($prefix);
        $xn_params = array();

        foreach ($params as $name => $val) {
            if (strpos($name, $prefix) === 0) {
                $xn_params[substr($name, $prefix_len)] = $val;
            }
        }

        // validate that the request hasn't expired. this is most likely
        // for params that come from $_COOKIE
        if ($timeout && (!isset($xn_params['time']) || time() - $xn_params['time'] > $timeout)) {
            return array();
        }

        // validate that the params match the signature
        $signature = isset($params[$namespace]) ? $params[$namespace] : null;

        if (!$signature || (!$this->_renren->verifySignature($xn_params, $signature))) {
            //return array();
        }

        return $xn_params;
    }

    public function get_hf_params($params, $namespace = 'hf')
    {
        if (empty($params)) {
            return array();
        }

        $prefix = $namespace . '_';
        $prefix_len = strlen($prefix);
        $hf_params = array();
        foreach ($params as $name => $val) {
            if (strpos($name, $prefix) === 0) {
                $hf_params[$name] = $val;
            }
        }

        return $hf_params;
    }

    public function validate_xn_params()
    {
        $this->xn_params = $this->get_valid_xn_params($_GET, 48*3600, 'xn_sig');

        if (!$this->xn_params) {
            $this->xn_params = $this->get_valid_xn_params($_POST, 48*3600, 'xn_sig');
        }

        return !empty($this->xn_params);
    }

    public function getRestClient()
    {
        return $this->_renren;
    }

    /**
     * _init()
     *
     * @return void
     */
    protected function _init()
    {
        $request = $this->getRequest();

        $app_id = $request->getParam('xn_sig_app_id');
        $this->_renren = Xiaonei_Renren::getInstance();

        if (!$this->_renren) {
            debug_log('app id error');
            //$this->redirect404();
            echo '系统错误，请联系管理员。';
            exit;
        }

        if (!$this->validate_xn_params()) {
            debug_log('signature error');
            //$this->redirect404();
            echo '参数验证失败，请联系管理员。';
            exit;
        }

        if (!isset($this->xn_params['user_src'])) {
            $this->xn_params['user_src'] = 'rr';
        }
        $this->_renren->setLocation($this->xn_params['domain'], $this->xn_params['user_src']);

        $this->hf_params = $this->get_hf_params($_POST);

        if ($this->xn_params['added'] == 0)
        {
            //$this->redirectAppAdd();
            $this->requireAuth();
        }

        //OK
        $this->_renren->setUser($this->xn_params['user'], $this->xn_params['session_key']);
        $this->_appId = $app_id;
        $this->_userId = $this->xn_params['user'];
        $this->_appName = $this->_renren->app_name;
        $this->_loadUser = true;
    }

    protected function _updateInfo()
    {
        $uid = $this->_userId;
        $user = $this->_renren->getUser();
        //update sns user info if needed
        if ($user) {
        	Happyfish_Magic_Bll_SnsUser::updatePerson($user);
        } else {
	        $oldUser = Happyfish_Magic_Bll_SnsUser::getPerson($uid);
	        if (!$oldUser) {
		        echo 'API call failure';
		        exit;
	        }
        }
    	
        //update sns friends info if needed
		$fids = $this->_renren->getFriendIds();
		if ($fids !== null) {
			$oldfids = Happyfish_Magic_Bll_SnsUser::getFriends($uid);
			$updatefriends = true;
			if (!empty($oldfids)) {
				if (count($fids) == count($oldfids)) {
				    $diff = array_diff($fids, $oldfids);
				    if (empty($diff)) {
				        $updatefriends = false;
				    }
				}
			}
			
			if ($updatefriends) {
			    Happyfish_Magic_Bll_SnsUser::updateFriends($uid, $fids);
			}
		}
    }

    /**
     * run() - main mothed
     *
     * @return void
     */
    public function run()
    {
        if ($this->_loadUser) {
            $this->_updateInfo();
        }

        //P3P privacy policy to use for the iframe document
        //for IE
        header('P3P: CP="CAO PSA OUR"');
        //header('P3P: CP="NOI DEV PSA PSD IVA PVD OTP OUR OTR IND OTC"');
        //header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');

         // start session
        $uid = $this->_userId;
        $session_id = md5('RENREN' . APP_KEY . APP_SECRET . $uid);
        session_id($session_id);
        $auth = Zend_Auth::getInstance();
        $auth->getStorage()->write($uid);
        $_SESSION['user'] = $uid;
        $_SESSION['app_id'] = $this->xn_params['app_id'];
        $_SESSION['session_key'] = $this->xn_params['session_key'];

    }

    public function redirectAppAdd()
    {
         $next = 'http://apps.' . $this->xn_params['domain'] . '/rrmagic' . $_SERVER['REQUEST_URI'];
         if (!empty($this->hf_params)) {
             $params = 'hf_params=' . base64_encode(http_build_query($this->hf_params));
             $next .= '?' . $params;
         }
         //debug_log($next);
         $url = $this->_renren->getAddUrl($next);
         //iframe
         //echo '<script type="text/javascript">top.location.href = "' . $url . '";</script>"';
         //XNML
         echo '<xn:redirect url="' . $url . '" />';
         exit;
    }
    
    public function requireAuth()
    {
         $next = 'http://apps.' . $this->xn_params['domain'] . '/rrmagic' . $_SERVER['REQUEST_URI'];
         if (!empty($this->hf_params)) {
             $params = 'hf_params=' . base64_encode(http_build_query($this->hf_params));
             $next .= '?' . $params;
         }
         $pageUrl = 'http://page.' . $this->xn_params['domain'] . '/rrmagic';
         
         $staticUrl = Zend_Registry::get('static');
         $imgUrl = $staticUrl . '/apps/magic/images/show.jpg';
         
         $content = '<div style="padding-bottom:10px;"><img border="0" src="' . $imgUrl . '" /></div><script type="text/javascript">' 
		          . 'var callback = function(){document.setLocation("' . $next . '"); };'
		          . 'var cancel = function(){ document.setLocation("' . $pageUrl . '");};'
		          . 'Session.requireLogin(callback,cancel);' 
	              . '</script>';
	              
	     echo  $content;
	     exit;
    }

    public function redirectForClient($url)
    {
        echo '<xn:redirect url="' . $url . '" />';
        exit;
    }

}