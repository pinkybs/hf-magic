<?php

class Hapyfish2_Magic_Bll_Payment
{

    /**
     * search pay order 查询订单
     *
     * @param  int $uid
     * @param  string $orderid
     * @return array
     */
	public static function getOrder($uid, $orderid)
	{
		try {
			$dalPayOrder = Hapyfish2_Magic_Dal_PayOrder::getDefaultInstance();
			return $dalPayOrder->getOrder($uid, $orderid);
		} catch (Exception $e) {
		    info_log('getOrder-Err:'.$e->getMessage(), 'Bll_Payment_Err');
			return null;
		}
	}

    public static function getOrderList($uid)
	{
		try {
			$dalPayOrder = Hapyfish2_Magic_Dal_PayOrder::getDefaultInstance();
			return $dalPayOrder->listOrder($uid);
		} catch (Exception $e) {
		    info_log('getOrderList-Err:'.$e->getMessage(), 'Bll_Payment_Err');
			return null;
		}
	}

	/**
     * create pay order 创建订单
     *
     * @param  int $uid
     * @param  string $orderid
     * @param  int $amount
     * @param  int $gold
     * @param  int $time
     * @param  string $token
     * @return array
     */
	public static function createOrder($uid, $orderid, $amount, $gold, $time, $token='')
	{
	    try {
    	    $order = array();
    	    $userLevelInfo = Hapyfish2_Magic_HFC_User::getUserLevel($uid);
    	    $order['orderid'] = $orderid;
    	    $order['amount'] = $amount;
    	    $order['gold'] = $gold;
    	    $order['token'] = $token;
    	    $order['order_time'] = $time;
    	    $order['status'] = 0;
    	    $order['uid'] = $uid;
    	    $order['user_level'] = $userLevelInfo['level'];

            $dalPayOrder = Hapyfish2_Magic_Dal_PayOrder::getDefaultInstance();
            $dalPayOrder->regOrder($uid, $order);
	    } catch (Exception $e) {
		    info_log('createOrder-Err:'.$e->getMessage(), 'Bll_Payment_Err');
			return null;
		}

        return $order;
	}

	/**
     * complete pay order 完成订单
     *
     * @param  int $uid
     * @param  string $orderid
     * @return int [0-success 1-has already completed 2-not found 3-failed]
     */
    public static function completeOrder($uid, $orderid)
    {
        $order = self::getOrder($uid, $orderid);

        if (empty($order)) {
            return 2;
        }
        if ($order['status'] != 0) {
            return 1;
        }

        $ok = false;
		try {
		    $userGold = Hapyfish2_Magic_HFC_User::getUserGold($uid);
		    $orderid = $order['orderid'];
		    $gold = $order['gold'];
			$dalUser = Hapyfish2_Magic_Dal_User::getDefaultInstance();
			$dalUser->incGold($uid, $gold);
			Hapyfish2_Magic_HFC_User::reloadUserGold($uid);
			$ok = true;
		} catch (Exception $e) {
			info_log('[' . $uid . ':' . $orderid . ']' . $e->getMessage(), 'payment.err.confirm.1');
			return 3;
		}

        if ($ok) {
            $time = time();
    		//更新订单状态
    		$updateinfo = array('status' => 1, 'complete_time' => $time);
            try {
                $dalPayOrder = Hapyfish2_Magic_Dal_PayOrder::getDefaultInstance();
    			$dalPayOrder->completeOrder($uid, $orderid, $updateinfo);

    			//更新充值记录
    			$loginfo = array(
    				'uid' => $uid, 'orderid' => $orderid, 'pid' => $order['token'],
    				'amount' => $order['amount'], 'gold' => $gold,
    				'create_time' => $time, 'user_level' => $order['user_level'],
    				'pay_before_gold' => $userGold,
    				'summary' => $order['amount'].'|'.$gold
			    );
    			$dalPayLog = Hapyfish2_Magic_Dal_PayLog::getDefaultInstance();
    			$dalPayLog->insert($uid, $loginfo);
    		} catch (Exception $e) {
    			info_log('[' . $uid . ':' . $orderid . ']' . $e->getMessage(), 'payment.err.confirm.2');
    		}

			//充值送
			self::chargeGift($uid, $order['amount']);
			return 0;
		}

		info_log('[' . $uid . ':' . $orderid . ']' . 'completeOrderFailed', 'payment.err.confirm.3');
		return 3;
    }

    public static function regOrder($app_id, $uid, $session_key, $amount)
    {
        try {
            $renren = Xiaonei_Renren::getInstance();
            if ($renren) {
                $rowUser = Hapyfish2_Platform_Bll_User::getUser($uid);
                $renren->setUser($rowUser['puid'], $session_key);
                $gold = $amount * 10;
                if ($amount == 10) {
                	$gold += 5;
                } else if ($amount == 20) {
                	$gold += 10;
                } else if ($amount == 50) {
                	$gold += 80;
                } else if ($amount == 100) {
					$gold += 200;
                }/** else if ($amount == 500) {
					$gold += 800;
                }*/
                $desc = $gold . '个宝石';
                $order = $renren->getPayOrderToken($amount, $desc);
                if ($order) {
                    $userLevelInfo = Hapyfish2_Island_HFC_User::getUserLevel($uid);
		            $order['user_level'] = $userLevelInfo['level'];
                    $order['uid'] = $uid;
                    $order['amount'] = $amount;
                    $order['gold'] = $gold;
                    $order['order_time'] = time();
                    $dalPayOrder = Hapyfish2_Island_Dal_PayOrder::getDefaultInstance();
                    $dalPayOrder->regOrder($uid, $order);

                    return $order;
                }
            }

        }catch (Exception $e) {
			info_log('regOrder-Err:'.$e->getMessage(), 'Bll_Payment_Err');
        }

        return null;
    }

    public  static function chargeGift($uid, $amount)
	{
	    if ($amount == 10) {

        } else if ($amount == 20) {

        } else if ($amount == 50) {

        } else if ($amount == 100) {

        }
	    return true;
	}
}