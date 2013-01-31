<?php

class Hapyfish2_Magic_Stat_Bll_Payment
{
	public static function cal($day)
	{
		$begin = strtotime($day);
		$end = $begin + 86400;
		$amount = 0;
		$gold = 0;
		$count = 0;
		
		$yearmonth = date('Ym', $begin);
		$userCount = 0;
		$uidTemp = array();
		$costGold = 0;
		
		try {
			$dalPay = Hapyfish2_Magic_Stat_Dal_PaymentLog::getDefaultInstance();
			for ($i = 0; $i < DATABASE_NODE_NUM; $i++) {
				for ($j = 0; $j < 10; $j++) {
					//充值信息
					$data = $dalPay->getPaymentLogData($i, $j, $begin, $end);
					if ($data) {
						foreach ($data as $row) {
							$amount += $row['amount'];
							$gold += $row['gold'];
							$count++;
							if ( !isset($uidTemp[$row['uid']]) ) {
								$userCount++;
								$uidTemp[$row['uid']] = 1;
							}
						}
					}
					//岛钻消费信息
					$goldData = $dalPay->getGold($i, $j, $yearmonth, $begin, $end);
					if ( $goldData > 0 ) {
						$costGold += $goldData;
					}
				}
			}
			return array('amount' => $amount, 'gold' => $gold, 'costGold' => $costGold, 'count' => $count, 'userCount' => $userCount);
		} catch (Exception $e) {
			return array('amount' => $amount, 'gold' => $gold, 'costGold' => $costGold, 'count' => $count, 'userCount' => $userCount);
		}
	}
}