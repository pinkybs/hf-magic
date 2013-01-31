<?php

/**
 * renren direct pay callback controller
 *
 * @copyright  Copyright (c) 2009 Community Factory Inc. (http://communityfactory.com)
 * @create    2010/05/10    HLJ
 */
class SignController extends Zend_Controller_Action
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

    public function regorderAction()
    {
        $uid = $_POST['xn_sig_user'];
        $app_id = $_POST['xn_sig_app'];
        $skey = $_POST['xn_sig_skey'];
        $sandbox = $_POST['xn_sig_sandbox'];
        $payment = $_POST['xn_sig_payment'];

        //debug_log('ordercompleted: ' . json_encode($_POST));

        if (empty($uid) || empty($app_id) || empty($skey) || empty($sandbox) || empty($payment)) {
            exit;
        }
        
        if ($app_id != APP_ID) {
            exit;
        }

        $validskey = md5('1234!@#$' . $uid);
        if ($validskey != $skey) {
            exit;
        }
        
        $payment = Zend_Json::decode($payment);
        
        if (!$payment) {
            exit;
        }
        
        $amount = $payment['amount'];
        $gold = $amount * 10;
        if ($amount == 20) {
        	$gold += 20;
        } else if ($amount == 50) {
        	$gold += 60;
        } else if ($amount == 100) {
			$gold += 140;
        }
        
        $desc = $gold . '个宝石';
        $message = $amount . '人人豆兑换' . $gold . '宝石';
        
        $renren = Xiaonei_Renren::getInstance();
        
        $ok = false;

        $order_id = 0;
        if ($renren) {
            $renren->setUser($uid, '');
            $order_id = $renren->createPayOrderId();
            $order = array(
                'order_id' => $order_id,
                'token' => '0',
                'uid' => $uid,
                'amount' => $amount,
                'gold' => $gold,
                'desc' => $desc,
                'order_time' => time()
            );
            
            if ($sandbox) {
                $ok = true;
            } else {
                try {
                    $dalPayOrder = Dal_PayOrder::getDefaultInstance();
                    $dalPayOrder->regOrder($order);
                    $ok = true;
                }
                catch (Exception $e) {
        			err_log('[regorder]: ' . $e->getMessage());
                }
            }
        }
        
        $result = array(
            'app_res_order_id' => $order_id,
            'app_res_message' => $message,
            'app_res_user' => $uid
        );
        
        if ($ok) {
            $result['app_res_code'] = 'OK';
        } else {
            $result['app_res_code'] = 'APP_LOGIC_ERROR';
        }
        
        echo Zend_Json::encode($result);
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
