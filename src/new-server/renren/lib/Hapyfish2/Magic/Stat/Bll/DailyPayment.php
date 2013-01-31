<?php

class Hapyfish2_Island_Stat_Bll_DailyPayment
{

	public static function saveDailyPayToDb()
	{
		try {
			$strDate = date('Ymd');

	    	$endDate = strtotime($strDate);
			$startDate = $endDate - 60*60*24;
			$logDate = date('Ymd',$startDate);
			$dal = Hapyfish2_Island_Stat_Dal_DailyPayment::getDefaultInstance();
			$dal->delete($logDate);
			$aryDb = array(0=>1016,1=>1017,2=>1010,3=>1011,4=>1012,5=>1013,6=>1014,7=>1015);
info_log(date('Ymd',$startDate).'********', 'stat_daily_pay');
			foreach ($aryDb as $db=>$dbUId) {
				//for ($i=0; $i<10; $i++) {
					$row = $dal->getDailyPaymentStat($dbUId, 0, $startDate, $endDate);
					$rowStat = $dal->getRow($logDate);
					if (empty($rowStat)) {
						$dal->insert(array('log_time'=>$logDate, 'amount'=>$row['amount'], 'gold'=>$row['gold'], 'trans_count'=>$row['cnt']));
					}
					else {
						$dal->updateByMultipleField($logDate, array('gold'=>$row['gold'], 'amount'=>$row['amount'], 'trans_count'=>$row['cnt']));
					}
info_log('db_'.$db.':'.$row['amount'].'-'.$row['gold'].'-'.$row['cnt'], 'stat_daily_pay');
				//}
			}
		}
		catch (Exception $e) {
			info_log($e->getMessage(), 'stat_daily_pay_Err');
		}
		return true;
	}


    public static function saveDailyPayToDbByDate($selDate)
	{
		try {
			$strDate = $selDate;

	    	$endDate = strtotime($strDate);
			$startDate = $endDate - 60*60*24;
			$logDate = date('Ymd',$startDate);
			$dal = Hapyfish2_Island_Stat_Dal_DailyPayment::getDefaultInstance();
			$dal->delete($logDate);
            $aryDb = array(0=>1016,1=>1017,2=>1010,3=>1011,4=>1012,5=>1013,6=>1014,7=>1015);
info_log(date('Ymd',$startDate).'********', 'stat_daily_pay');
			foreach ($aryDb as $db=>$dbUId) {
				//for ($i=0; $i<10; $i++) {
					$row = $dal->getDailyPaymentStat($dbUId, 0, $startDate, $endDate);
					$rowStat = $dal->getRow($logDate);
					if (empty($rowStat)) {
						$dal->insert(array('log_time'=>$logDate, 'amount'=>$row['amount'], 'gold'=>$row['gold'], 'trans_count'=>$row['cnt']));
					}
					else {
						$dal->updateByMultipleField($logDate, array('gold'=>$row['gold'], 'amount'=>$row['amount'], 'trans_count'=>$row['cnt']));
					}
info_log('db_'.$db.':'.$row['amount'].'-'.$row['gold'].'-'.$row['cnt'], 'stat_daily_pay');
				//}
			}
		}
		catch (Exception $e) {
			info_log($e->getMessage(), 'stat_daily_pay_Err');
		}
		return true;
	}
}