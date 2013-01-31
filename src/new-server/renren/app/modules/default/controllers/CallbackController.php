<?php

/**
 * application callback controller
 *
 * @copyright  Copyright (c) 2009 Community Factory Inc. (http://communityfactory.com)
 * @create    2009/08/07    HLJ
 */
class CallbackController extends Zend_Controller_Action
{

    /**
     * index Action
     *
     */
    public function indexAction()
    {
    	echo 'hello callback';
    	exit;
    }

    public function paydoneAction()
    {
//info_log(json_encode($_POST), 'paycallback');

        $payKey = 'lizguo10';

        $puid = $_POST['xn_sig_user'];
        $session_key = $_POST['xn_sig_session_key'];
        $order_id = $_POST['xn_sig_order_id'];
        $skey = $_POST['xn_sig_skey'];

        //debug_log('ordercompleted: ' . json_encode($_POST));

        if (empty($puid) || empty($session_key) || empty($order_id) || empty($skey)) {
            exit;
        }

        $validskey = md5($payKey . $puid);
        if ($validskey != $skey) {
            debug_log($puid. ':' . $order_id . ':' . $skey);
            exit;
        }

        $renren = Renren_Client::getInstance();
        if ($renren) {
            $renren->setUser($puid, $session_key);
            $completed = $renren->isOrderCompleted($order_id);

            if ($completed) {
                $ok = true;
                $rowUser = Hapyfish2_Platform_Cache_UidMap::getUser($puid);
                $order = Hapyfish2_Magic_Bll_Payment::getOrder($rowUser['uid'], $order_id);
                if ($order && $order['status'] == 0) {
                    $rst = Hapyfish2_Magic_Bll_Payment::completeOrder($rowUser['uid'], $order_id);
                }

                if ($rst == 0) {
                	$amount = $order['amount'];
                    $result = array('app_res_user' => $puid, 'app_res_order_id' => $order_id, 'app_res_amount' => $amount);

                    //file log
    	            $log = Hapyfish2_Util_Log::getInstance();
                    $log->report('paydone', array($order_id, $amount, $order['token'], $rowUser['uid']));

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