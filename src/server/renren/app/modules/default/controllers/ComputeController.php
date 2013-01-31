<?php

class ComputeController extends Zend_Controller_Action
{
    //protected $uid;

    /**
     * initialize basic data
     * @return void
     */
    public function init()
    {
    	$p = $this->_request->getParam('p');
    	if ( $p != 496700 ) {
    		echo '不准非法进入！';
            exit;
    	}
        $controller = $this->getFrontController();
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);
		$this->view->staticUrl = Zend_Registry::get('static');
        $auth = Zend_Auth::getInstance();
        $this->uid = $auth->getIdentity();
        
        //$this->uid != '258027420' && $this->uid != '283848137' && $this->uid != '290434000'
        /*if ( !in_array($this->uid, array('258027420', '283848137', '290434000', '281222990')) ) {
            echo '不准非法进入！';
            exit;
        }
        if (!$auth->hasIdentity()) {
            echo '不准非法进入！';
            exit;
            //$auth->getStorage()->write();
        }*/

    }
    
    public function indexAction()
    {
    	//总注册用户数，日活跃用户数，卸载用户数，日PV数目，日超时用户，日付费用户数目（付费金额，付费总数），以上都以小时为单位，同时有每日合计
        $day = $this->_request->getParam('day');
        $hour = $this->_request->getParam('hour');
        if ( !empty($day) ) {
        	if ( empty($hour) ) {
	            $time = '2010-'.$day.' 00:00';
	            $startTime = strtotime($time);
	            $endTime = $startTime + 86400;
            }
            else {
            	$time = '2010-'.$day.' '.$hour.':00';
                $startTime = strtotime($time);
                $endTime = $startTime + 3600;
            }
        }
        else {
        	$startTime = strtotime(date('Y-m-d'));
        	$endTime = $startTime + 86400;
        }
        //1272729600
    	
        $bllCompute = new Bll_Island_Compute();
        $result = $bllCompute->computeAll($startTime, $endTime);
        //echo Zend_Json::encode($result);

        
        $todayTime = date('Y-m-d', $startTime);
        $todayTime = strtotime($todayTime);
        
        $today = date('Y-m-d H:i', $todayTime);
        
        $start = date('Y-m-d H:i', $startTime);
        
        if ( $endTime > time() ) {
        	$end = '今';
        }
        else {
            $end = date('Y-m-d H:i', $endTime);
        }
        
        
        echo '<br/>';
        echo '目前为止总注册用户数：' . $result['allUserCount'] . ' <br/>';
        echo $start .'至' . $end .' 所增加新用户总数：' . $result['addUserCount'] . ' <br/>';
        echo $today .'至今 的活跃用户数：' . $result['activeCount'] . '<br/>';
        echo $start .'至' . $end .' 所有充值次数：' . $result['payCount'] . ' <br/>';
        echo $start .'至' . $end .' 所充值金额总数：' . $result['goldCount'] . ' <br/>';
        echo '活跃率为：' . sprintf("%01.2f",($result['activeCount']/$result['allUserCount'])*100) . '%  (活跃用户数/总注册用户数) <br/>';
    	
        echo '<br/><br/><br/><br/>';
        echo '备注：地址栏的地址输入格式为 http://rrisland.hapyfish.com/compute/index/p/496700/day/05-02/hour/02 形式，<br/>'.
             '其中 "day/" 后跟 日期（月-日），"hour/" 后跟小时， "hour/"后不跟的情况，默认为日期当天凌晨开始。<br/>'.
             '有什么问题直接与我联系';
    }
    
    /**
     * get level user count
     *
     */
    function getlevelcountAction()
    {
    	$day = $this->_request->getParam('day');
    	if ( !empty($day) ) {
    		$time = '2010-'.$day.' 00:00';
            $startTime = strtotime($time);
    		$endTime = $startTime + 86400;
    	}
    	
    	$bllCompute = new Bll_Island_Compute();
    	$result = $bllCompute->getLevelCount($startTime, $endTime);
    	echo Zend_Json::encode($result);
    }
    
    function getallActoin()
    {
        $day = $this->_request->getParam('day');
        if ( !empty($day) ) {
            $time = '2010-'.$day.' 00:00';
            $startTime = strtotime($time);
            $endTime = $startTime + 86400;
        }
        
        $bllCompute = new Bll_Island_Compute();
        $result = $bllCompute->getAll($startTime, $endTime);
        echo Zend_Json::encode($result);        
    }
    
    public function allAction()
    {
        $startTime = strtotime(date('Y-m-d'));
        $endTime = $startTime + 86400;
        
        $bllCompute = new Bll_Island_Compute();
        $result = $bllCompute->computeAll($startTime, $endTime);
        $this->view->result = $result;
        
    	$this->render();
    }

    public function alluserAction()
    {
        
        $this->render();
    }

    public function adduserAction()
    {
        
        $this->render();
    }

    public function leveluserAction()
    {
        
        $this->render();
    }

    public function paylistAction()
    {
        
        $this->render();
    }
    
    public function setcomputeAction()
    {
    	info_log('setcompute ************ start ************** ', "setcompute");
    	
    	$params = $this->_request->getParam('params');
    	$params = Zend_Json::decode($params);
    	
    	$bllStatics = new Admin_Bll_Statics();
    	if ( $params['computeType'] == 'day' ) {
            $bllStatics->setDayCompute($params);
    	}
    	else if ( $params['computeType'] == 'hour' ) {
            $bllStatics->setHourCompute($params);
    	}
    	info_log('setcompute ************ end ************** ', "setcompute");
    }

    public function settestAction()
    {
        info_log('settest ************ start ************** ', "settest");
        
        $params = $this->_request->getParam('params');
        $params = Zend_Json::decode($params);
        
        $bllTest = new Bll_Island_Test();
        $bllTest->setFifaList($params);
        
        info_log('settest ************ end ************** ', "settest");
    }
        
    public function testAction()
    {
    	$bllBatchWork = new Bll_Island_BatchWork();
        $bllBatchWork->doComputeByHour(time(), 'mixi');
        //$bllBatchWork->doComputeByDay(time(), 'taobao');
    }
    
 }
