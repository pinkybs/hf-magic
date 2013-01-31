<?php

class Kaixin_Rest
{
    public $api_key;
    public $secret;
    public $app_id;
    public $app_name;
    public $user_id;

    /**
     * rest api call object
     *
     * @var Kaixin_Rest_Core
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
    
    public function setUser($user_id, $session_key)
    {
        $this->user_id = $user_id;
        $this->core->session_key = $session_key;
    }
    
    public function verifySignature($kx_params, $expected_sig)
    {
        return Kaixin_Rest_Core::generate_sig($kx_params, $this->secret) == $expected_sig;
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
    
    public function getUser()
    {
        $this->clearErr();
    	try {
            $data = $this->core->users_getInfo($this->user_id);
            if(isset($data[0])) {
                return $data[0];
            }
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log('[Kaixin_Rest::getUser]: ' . $e->getMessage());
        }

        $this->err = true;
        return null;
    }

    public function getAppFriendIds()
    {
        $this->clearErr();
    	try {
            $data = $this->core->friends_getAppFriends();
            $fids = array();
			foreach ($data as $uid => $flag) {
				if ($flag == 1) {
					$fids[] = $uid;
				}
			}
			return $fids;
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log('[Kaixin_Rest::getAppFriendIds]: ' . $e->getMessage());
        }

        $this->err = true;
        return null;
    }

    public function getFriends()
    {
        $this->clearErr();
    	try {
            $data = $this->rest->friends_getFriends();
            if ($data && is_array($data)) {
                $friends = array();
                foreach ($data as $v) {
                   $friends[$v['uid']] = array('uid' => $v['uid'], 'name' => $v['name'], 'thumbnail' => $v['logo50']);
                }
                return $friends;
            }
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log('[Kaixin_Rest::getFriends]: ' . $e->getMessage());
        }

        $this->err = true;
        return null;
    }
    
    public function getEncodeSessionKey()
    {
        $this->clearErr();
    	try {
            $data = $this->core->users_getEncodeSessionKey();
            if (isset($data['result'])) {
				return $data['result'];
            }
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log('[Kaixin_Rest::getEncodeSessionKey]: ' . $e->getMessage());
        }

        $this->err = true;
        return null;
    }
    
    public function getInvitationSucList()
    {
        $this->clearErr();
    	try {
            $data = $this->core->users_getInvitationSucList($this->user_id);
            if (isset($data['result'])) {
            	return array();
            } else {
				return $data;
            }
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log('[Kaixin_Rest::getInvitationSucList]: ' . $e->getMessage());
        }

        $this->err = true;
        return null;
    }
    
    public function isFan($starUid)
    {
        $this->clearErr();
    	try {
            $data = $this->core->users_isFan($starUid, array($this->user_id));
            if ($data && is_array($data)) {
            	if ($data[$this->user_id]) {
            		return true;
            	} else {
            		return false;
            	}
            } else {
				return false;
            }
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log('[Kaixin_Rest::isFan]: ' . $e->getMessage());
        }

        $this->err = true;
        return false;
    }

    public function __construct($api_key, $secret, $app_id, $app_name)
    {
        $this->api_key = $api_key;
        $this->secret = $secret;
        $this->app_id = $app_id;
        $this->app_name = $app_name;

        $this->core = new Kaixin_Rest_Core($api_key, $secret);
    }

}