<?php

/**
 * application callback controller
 *
 * @copyright  Copyright (c) 2009 Community Factory Inc. (http://communityfactory.com)
 * @create    2009/08/07    HLJ
 */
class CallbackController extends Zend_Controller_Action
{
    private $_xn_params;

    private $_renren;

    /**
     * index Action
     *
     */
    public function indexAction()
    {
    	echo 'callback';
    	exit;
    }

    private function get_valid_xn_params($params, $timeout = null, $namespace = 'xn_sig')
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

    private function validate_xn_params()
    {
        $this->_xn_params = $this->get_valid_xn_params($_POST, 48*3600, 'xn_sig');
        return !empty($this->_xn_params);
    }

    public function payAction()
    {
        //debug_log(json_encode($_POST));
        $app_id = APP_ID;

        $this->_renren = Xiaonei_Renren::getInstance();

        if (!$this->_renren) {
            debug_log('app id error');
            exit;
        }

        if (!$this->validate_xn_params()) {
            debug_log('signature error');
            exit;
        }

        $amount = (int)$_POST['amount'];
        $uid = $this->_xn_params['user'];
        $session_key = $this->_xn_params['session_key'];

        if ($amount > 0) {
            $order = Bll_PayOrder::regOrder($app_id, $uid, $session_key, $amount);
            if ($order) {
                echo Zend_Json::encode($order);
                exit;
            }
        }

        exit;
    }

    public function ordercompletedAction()
    {
        $uid = $_POST['xn_sig_user'];
        $session_key = $_POST['xn_sig_session_key'];
        $order_id = $_POST['xn_sig_order_id'];
        $skey = $_POST['xn_sig_skey'];

        //xn_sig_password
        //xn_sig_order_number

        //debug_log('ordercompleted: ' . json_encode($_POST));

        if (empty($uid) || empty($session_key) || empty($order_id) || empty($skey)) {
            exit;
        }


        $validskey = md5('1234!@#$' . $uid);
        if ($validskey != $skey) {
            debug_log($uid. ':' . $order_id . ':' . $skey);
            exit;
        }

        $renren = Xiaonei_Renren::getInstance();

        if ($renren) {
            $renren->setUser($uid, $session_key);
            $completed = $renren->isOrderCompleted($order_id);

            if ($completed) {
                $ok = true;
                $order = Bll_PayOrder::getOrder($order_id);
                if ($order && $order['completed'] == 0) {
                    $ok = Bll_PayOrder::completeOrder($order);
                }

                if ($ok) {
                	$amount = $order['amount'];
                    $result = array('app_res_user' => $uid, 'app_res_order_id' => $order_id, 'app_res_amount' => $amount);
                    echo json_encode($result);
                }
            }
        }

        exit;
    }

    /**
     * magic function
     *   if call the function is undefined,then echo undefined
     *
     * @param string $methodName
     * @param array $args
     * @return void
     */
    function __call($methodName, $args)
    {
        echo 'undefined method name: ' . $methodName;
        exit;
    }

 }
