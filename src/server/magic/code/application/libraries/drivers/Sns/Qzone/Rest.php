<?php

require_once 'Qzone/Rest/Abstract.php';
require_once 'Qzone/Rest/Xiaoyou.php';

class Qzone_Rest
{
    public $api_key;
    public $app_id;
    public $app_name;
    public $user_id;

    /**
     * xiaoyou rest api call object
     *
     * @var Qzone_Rest_Xiaoyou
     */
    public $xiaoyou;
    
    protected $err;
    
    protected $code;

    protected static $_instance;

    public function __construct($app_id, $app_key, $app_name)
    {
        $this->app_id = $app_id;
    	$this->api_key = $app_key;
    	$this->app_name = $app_name;
        $this->xiaoyou = new Qzone_Rest_Xiaoyou($app_id, $app_key, $app_name);
        $this->err = false;
        $this->code = 0;
    }

    public function setUser($user_id, $session_key)
    {
        $this->user_id = $user_id;
        $this->xiaoyou->set_User($user_id, $session_key);
    }

    /**
     * single instance of Qzone_Rest
     *
     * @return Qzone_Rest
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self(APP_ID, APP_KEY, APP_NAME);
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
    
    /**
     * check user whether installed app
     *
     * @return boolean true|false
     */
    public function isAppUser()
    {
        $this->clearErr();
    	try {
            $result = $this->xiaoyou->user_isAppUser();
            if(isset($result['setuped'])) {
                return $result['setuped'];
            }
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log('[Qzone_Rest::isAppUser]' . $e->getMessage());
        }

        $this->err = true;
        return null;
    }

    public function getUser()
    {
        $this->clearErr();
    	try {
            return $this->xiaoyou->user_getProfile();
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log('[Qzone_Rest::getUser]' . $e->getMessage());
        }

        $this->err = true;
        return null;
    }
    
    public function getAppFriendIds()
    {
        $this->clearErr();
    	try {
            $data = $this->xiaoyou->user_getAppFriendIds();
            
			$fids = array();
			if (!empty($data['items'])) {
				foreach ($data['items'] as $item) {
					//filter
					if ($item['openid'] != $this->user_id) {
						$fids[] = $item['openid'];
					}
				}
			}
				
			return $fids;
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log($e->getMessage());
        }

        $this->err = true;
        return null;
    }
    
    public function getPayBalance($needVip = false)
    {
        $this->clearErr();
    	try {
            $data = $this->xiaoyou->pay_getBalance($needVip);
            if (isset($data['balance'])) {
            	if (!$needVip) {
					return $data['balance'];
            	} else {
            		return array('balance' => $data['balance'], 'is_vip' => $data['is_vip']);
            	}
            }
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log($e->getMessage());
        }

        $this->err = true;
        return null;
    }
    
    public function isVip()
    {
        $this->clearErr();
    	try {
            $result = $this->xiaoyou->pay_isvip();
            if(isset($result['is_vip'])) {
                return $result['is_vip'];
            }
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log($e->getMessage());
        }

        $this->err = true;
        return null;
    }
    
    public function pay($items, $amt)
    {
        $this->clearErr();
    	try {
            $result = $this->xiaoyou->pay_pay($items, $amt);
            if(isset($result['billno'])) {
                return $result['billno'];
            }
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log($e->getCode() . ':' . $e->getMessage());
        }

        $this->err = true;
        return false;
    }
    
    public function payConfirm($billno)
    {
        $this->clearErr();
    	try {
            $this->xiaoyou->pay_confirm($billno);
			return true;
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log($e->getCode() . ':' . $e->getMessage());
        }

        $this->err = true;
        return false;
    }
    
    public function payCancel($billno)
    {
        $this->clearErr();
    	try {
            $this->xiaoyou->pay_cancel($billno);
            return true;
        }
        catch (Exception $e) {
        	$this->code = $e->getCode();
            err_log($e->getCode() . ':' . $e->getMessage());
        }

        $this->err = true;
        return false;
    }

}