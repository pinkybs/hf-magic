<?php

require_once 'Xiaonei/Rest.php';

class Xiaonei_Renren
{
    public $api_key;
    public $secret;
    public $app_id;
    public $app_name;
    public $user_id;

    //"renren.com" or "kaixin.com"
    public $domain;

    //"rr" or "kx"
    public $user_src;


    /**
     * xiaonei api rest object
     *
     * @var Xiaonei_Rest
     */
    public $client;

    protected static $_instance;

    /**
     * get renren object
     *
     * @return Xiaonei_Renren
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self(APP_KEY, APP_SECRET, APP_ID, APP_NAME);
        }

        return self::$_instance;
    }

    public function setLocation($domain, $user_src)
    {
        $this->domain = $domain;
        $this->user_src = $user_src;
        $this->app_url = 'http://app.' . $domain;
    }

    public function setUser($user_id, $session_key)
    {
        $this->user_id = $user_id;
        $this->client->session_key = $session_key;
    }

    public function verifySignature($xn_params, $expected_sig)
    {
        return Xiaonei_Rest::generate_sig($xn_params, $this->secret) == $expected_sig;
    }

    public function getAddUrl($next = null)
    {
        return $this->app_url . '/apps/tos.do?v=1.0&api_key=' . $this->api_key . ($next ? '&next=' . urlencode($next) : '');
    }

    public function getUser()
    {
        try {
            $data = $this->client->users_getInfo($this->user_id);
            if(isset($data[0])) {
                return $data[0];
            }
        }
        catch (Exception $e) {
            err_log('[Xiaonei_Renren::getUser]: ' . $e->getMessage());
        }

        return null;
    }

    public function getFriendIds()
    {
        try {
            //friends_getAppUsers will become invalid
            //$data = $this->client->friends_getAppUsers();
            $data = $this->client->friends_getAppFriends();
            if (isset($data['uid'])) {
                return array($data['uid']);
            } else if(is_array($data)) {
                return $data;
            }
        }
        catch (Exception $e) {
            err_log('[Xiaonei_Renren::getFriendIds]: ' . $e->getMessage());
        }

        return null;
    }

    public function getFriends()
    {
        try {
            $data = $this->client->friends_getFriends();
            if ($data && is_array($data)) {
                $friends = array();
                foreach ($data as $v) {
                   $friends[$v['id']] = array('uid' => $v['id'], 'name' => $v['name'], 'thumbnail' => $v['tinyurl']);
                }
                return $friends;
            }
        }
        catch (Exception $e) {
            err_log('[Xiaonei_Renren::getFriends]: ' . $e->getMessage());
        }

        return null;
    }

    public function getPayOrderToken($amount, $desc)
    {
        $order_id = $this->createPayOrderId();
        try {
            $data = $this->client->pay_regOrder($order_id, $amount, $desc);
            if(isset($data['token'])) {
                return array('order_id' => $order_id, 'token' => $data['token']);
            }
        }
        catch (Exception $e) {
            err_log('[Xiaonei_Renren::getPayOrderToken]: ' . $e->getMessage());
        }
        return null;
    }

    public function isOrderCompleted($order_id)
    {
        try {
            $data = $this->client->pay_isCompleted($order_id);
            if(isset($data[0])) {
                return (bool)$data[0];
            }
        }
        catch (Exception $e) {
            err_log('[Xiaonei_Renren::isOrderCompleted]: ' . $e->getMessage());
        }

        return false;
    }

    public function createPayOrderId()
    {
        //seconds 10 lens
        $time = time();
        //2010-01-01 00:00:00 1262275200
        $ticks = $time - 1262275200;

        //server id, 1 lens 0~9
        if (defined('SERVER_ID')) {
            $serverid = SERVER_ID;
        } else {
            $serverid = '0';
        }

        //max 9 lens
        //$this->user_id
        return $ticks . $serverid . $this->user_id;
    }

    public function __construct($api_key, $secret, $app_id, $app_name)
    {
        $this->api_key = $api_key;
        $this->secret = $secret;
        $this->app_id = $app_id;
        $this->app_name = $app_name;

        $this->client = new Xiaonei_Rest($api_key, $secret);
    }

}