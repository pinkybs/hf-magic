<?php

class PayController extends Zend_Controller_Action
{
    protected $uid;

    protected $info;

    public static $_aryPay = array(
						'1' =>   array('id' => 1,   'name' => '10钻石',   'price' => 1,   'gold' => 10),
             	   		'10' =>  array('id' => 10,  'name' => '100钻石',  'price' => 10,  'gold' => 100),
             	   		'25' =>  array('id' => 25,  'name' => '260钻石',  'price' => 25,  'gold' => 260),
                 	    '50' =>  array('id' => 50,  'name' => '525钻石',  'price' => 50,  'gold' => 525),
                 	    '100' => array('id' => 100, 'name' => '1100钻石', 'price' => 100, 'gold' => 1100),
                 	    '200' => array('id' => 200, 'name' => '2300钻石', 'price' => 200, 'gold' => 2300));

    public function init()
    {
    	$info = $this->vailid();
        if (!$info) {
            echo '<html><body><script type="text/javascript">window.top.location="http://apps.renren.com/'.APP_NAME.'/";</script></body></html>';
            exit;
        }

        $this->info = $info;
        $this->uid = $info['uid'];
        $data = array('uid' => $info['uid'], 'puid' => $info['puid'], 'session_key' => $info['session_key']);
        $context = Hapyfish2_Util_Context::getDefaultInstance();
        $context->setData($data);

        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
        $this->view->appId = APP_ID;
        $this->view->appKey = APP_KEY;
        $this->view->uid = $info['uid'];
        $this->view->platformUid = $info['puid'];
    }

	protected function vailid()
    {
    	$skey = $_COOKIE[PRODUCT_ID.'_skey'];
    	return Hapyfish2_Validate_UserCertify::checkKey($skey, APP_SECRET);
    }

	public function topAction()
	{
		$uid = $this->uid;
		$user = Hapyfish2_Platform_Bll_User::getUser($uid);
		$user['face'] = $user['figureurl'];
		$user['gold'] = Hapyfish2_Magic_HFC_User::getUserGold($uid);

		$this->view->user = $user;
		$this->render();
	}

	public function orderlistAction()
	{
		$uid = $this->uid;
		$user = Hapyfish2_Platform_Bll_User::getUser($uid);
		$user['face'] = $user['figureurl'];
		$user['gold'] = Hapyfish2_Magic_HFC_User::getUserGold($uid);

		$listOrder = Hapyfish2_Magic_Bll_Payment::getOrderList($uid);

		$this->view->logs = $listOrder;
		$this->view->logCnt = $listOrder==null ? 0 : count($listOrder);
		$this->view->user = $user;
		$this->render();
	}

    public function queryorderAction()
	{
		$uid = $this->uid;
		$user = Hapyfish2_Platform_Bll_User::getUser($uid);
		$user['face'] = $user['figureurl'];
		$user['gold'] = Hapyfish2_Magic_HFC_User::getUserGold($uid);

		$orderid = $this->_request->getParam('orderid');
        $info = $this->info;
        $rest = Renren_Client::getInstance();
        $rest->setUser($info['puid'], $info['session_key']);
        $rst = $rest->queryOrders($orderid);
        if ($rst) {
            if ($rst['status'] == '20') {
                $this->view->msg = '支付已完成';
            }
            else if ($rst['status'] == '15') {
                $this->view->msg = '已扣费，请等待发货';
            }
            else {
                $this->view->msg = '订单未支付';
            }
        }
        else {
            $this->view->msg = '没有找到订单';
        }

		$this->view->user = $user;
		$this->render();
	}


    public function logAction()
    {
		$uid = $this->uid;
		$user = Hapyfish2_Platform_Bll_User::getUser($uid);
		$user['face'] = $user['figureurl'];
		$user['gold'] = Hapyfish2_Island_HFC_User::getUserGold($uid);

    	$notice = Hapyfish2_Island_Cache_BasicInfo::getNoticeList();
        if (empty($notice)) {
        	$this->view->showNotice = false;
        } else {
        	$this->view->showNotice = true;
			$this->view->mainNotice = $notice['main'];
			$this->view->subNotice = $notice['sub'];
			$this->view->picNotice = $notice['pic'];
        }

    	$logs = Hapyfish2_Island_Bll_PaymentLog::getPayment($uid, 50);
    	if (!$logs) {
    		$count = 0;
    		$logs = '[]';
    	} else {
    		$count = count($logs);
    		$logs = json_encode($logs);
    	}
    	$pageSize = 25;
    	$this->view->user = $user;
		$this->view->logs = $logs;
        $this->view->count = $count;
        $this->view->pageSize = 25;
        $this->view->pageNum = ceil($count/$pageSize);
        $this->render();
    }

    public function payAction()
    {
 //debug_log(json_encode($_POST));

        $amount = (int)$this->_request->getParam('amount');
        if (!$amount) {
            debug_log('amount err');
            exit;
        }

        if (!isset(self::$_aryPay[$amount])) {
            debug_log('amount err2');
            exit;
        }

        $renren = Renren_Client::getInstance();
        if (!$renren) {
            debug_log('app id error');
            exit;
        }

        $info = $this->vailid();
        if (!$info) {
			debug_log('session out');
            exit;
        }

        $uid = $info['uid'];
        $time = time();
        $renren->setUser($info['puid'], $info['session_key']);
        $gold = self::$_aryPay[$amount]['gold'];
        $desc = $amount.'人人豆兑换'.self::$_aryPay[$amount]['name'];
        $token = $renren->getPayOrderToken($amount, $desc);
        if (!$token) {
            info_log('pay:failed get token', 'payorder_err');
            exit;
        }

        $orderid = $token['orderid'];
        $token = $token['token'];
        $order = Hapyfish2_Magic_Bll_Payment::createOrder($uid, $orderid, $amount, $gold, $time, $token);
        if ($order) {
            echo Zend_Json::encode($order);
            exit;
        }

        exit;
    }

}