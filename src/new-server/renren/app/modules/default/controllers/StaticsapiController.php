<?php

class StaticsapiController extends Zend_Controller_Action
{
	function vaild()
	{
		
	}
	
    protected function echoResult($data)
    {
    	$data['errno'] = 0;
    	echo json_encode($data);
    	exit;
    }
    
    protected function echoError($errno, $errmsg)
    {
    	$result = array('errno' => $errno, 'errmsg' => $errmsg);
    	echo json_encode($result);
    	exit;
    }
    
    public function noopAction()
    {
    	$data = array('id' => SERVER_ID, 'time' => time(), 'method' => 'noop');
    	$this->echoResult($data);
    }
	
	public function uidlistAction()
	{
		$uidlist = Hapyfish2_Magic_Bll_Statics::getUidList();
		$data = array('list' => $uidlist);
		
		$this->echoResult($data);
	}
	
	public function maxuidAction()
	{
		$maxuid = Hapyfish2_Magic_Bll_Statics::getMaxUid();
		$data = array('maxuid' => $maxuid);
		$this->echoResult($data);
	}
	
	public function mainAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Ymd", strtotime("-1 day"));
		}
		$log = Hapyfish2_Magic_Stat_Bll_Day::getMain($day);
		$data = array('data' => $log);
		$this->echoResult($data);
	}
	
	public function retentionAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Ymd", strtotime("-1 day"));
		}
		
		$log = Hapyfish2_Magic_Stat_Bll_Day::getRetention($day);
		$data = array('data' => $log);
		$this->echoResult($data);
	}
	
	public function paymentofcalAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Y-m-d", strtotime("-1 day"));
		}
		
		$log = Hapyfish2_Magic_Stat_Bll_Payment::cal($day);
		$data = array('data' => $log);
		$this->echoResult($data);
	}
	
	public function paymentAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Y-m-d", strtotime("-1 day"));
		}
		
		$log = Hapyfish2_Magic_Stat_Bll_Day::getPayment($day);
		$data = array('data' => $log);
		$this->echoResult($data);
	}
	
	public function activeuserlevelAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Ymd", strtotime("-1 day"));
		}
		
		$log = Hapyfish2_Magic_Stat_Bll_Day::getActiveUserLevel($day);
		$data = array('data' => $log);
		$this->echoResult($data);
	}
	
	public function mainhourAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Ymd", strtotime("-1 day"));
		}
		
		$log = Hapyfish2_Magic_Stat_Bll_DayHour::getMain($day);
		$data = array('data' => $log);
		$this->echoResult($data);
	}

}